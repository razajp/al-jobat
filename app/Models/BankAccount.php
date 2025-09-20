<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'sub_category', 'bank_id', 'account_title', 'date', 'remarks', 'account_no', 'chqbk_serial_start', 'chqbk_serial_end', 'status'];

    protected $hidden = [
        'bank_id',
        'creator_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $appends = ['balance', 'available_cheques'];

    protected static function booted()
    {
        // Automatically set creator_id when creating a new Article
        static::creating(function ($thisModel) {
            if (Auth::check()) {
                $thisModel->creator_id = Auth::id();
            }
        });

        // Always eager load the associated creator
        static::addGlobalScope('withCreator', function (Builder $builder) {
            $builder->with('creator');
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function subCategory()
    {
        return $this->morphTo();
    }
    public function bank()
    {
        return $this->belongsTo(Setup::class, 'bank_id')->where('type', 'bank_name');
    }
    public function paymentPrograms()
    {
        return $this->morphMany(PaymentProgram::class, 'sub_category');
    }
    public function bankAccounts()
    {
        return $this->hasOne(BankAccount::class, 'id');
    }

    public function getAvailableChequesAttribute()
    {
        if ($this->category !== 'self') {
            return null;
        }

        // Ensure serial start and end are valid numbers
        $start = (int) $this->chqbk_serial_start;
        $end = (int) $this->chqbk_serial_end;

        if ($start <= 0 || $end <= 0 || $end < $start) {
            // Invalid or missing serials → return empty array
            return [];
        }

        // Get all the used cheques for this bank account
        $usedCheques = SupplierPayment::where('bank_account_id', $this->id)
            ->pluck('cheque_no')
            ->toArray();

        // Generate full range of cheque numbers
        $fullRange = range($start, $end);

        // Filter out the used ones
        $available = array_diff($fullRange, $usedCheques);

        // Return available cheque numbers
        return array_values($available);
    }

    public function getBalanceAttribute()
    {
        return $this->calculateBalance();
    }

    public function calculateBalance($fromDate = null, $toDate = null, $formatted = false, $includeGivenDate = true)
    {
        if ($this->category === 'self') {
            // Self category: existing logic
            $customerPayments = CustomerPayment::where('bank_account_id', $this->id);
            $supplierPayments = SupplierPayment::where('bank_account_id', $this->id)->whereNotNull('voucher_id');

            // Apply date filters
            if ($fromDate && $toDate) {
                if ($includeGivenDate) {
                    $customerPayments->whereBetween('date', [$fromDate, $toDate]);
                    $supplierPayments->whereBetween('date', [$fromDate, $toDate]);
                } else {
                    $customerPayments->where('date', '>', $fromDate)->where('date', '<', $toDate);
                    $supplierPayments->where('date', '>', $fromDate)->where('date', '<', $toDate);
                }
            } elseif ($fromDate) {
                $operator = $includeGivenDate ? '>=' : '>';
                $customerPayments->where('date', $operator, $fromDate);
                $supplierPayments->where('date', $operator, $fromDate);
            } elseif ($toDate) {
                $operator = $includeGivenDate ? '<=' : '<';
                $customerPayments->where('date', $operator, $toDate);
                $supplierPayments->where('date', $operator, $toDate);
            }

            $totalPayments = $customerPayments->sum('amount') ?? 0;
            $totalPays = $supplierPayments->sum('amount') ?? 0;

            $balance = $totalPayments - $totalPays;

        } else if ($this->category === 'supplier') {
            $balance = PaymentClear::where('bank_account_id', $this->id)
                ->where('method', '!=', 'cash') // ignore cash
                ->when($fromDate, fn($q) => $q->where('date', $includeGivenDate ? '>=' : '>', $fromDate))
                ->when($toDate, fn($q) => $q->where('date', $includeGivenDate ? '<=' : '<', $toDate))
                ->sum('amount');
        } else if ($this->category === 'customer') {
            $balance = PaymentClear::where('bank_account_id', $this->id)
                ->where('method', '!=', 'cash') // ignore cash
                ->when($fromDate, fn($q) => $q->where('date', $includeGivenDate ? '>=' : '>', $fromDate))
                ->when($toDate, fn($q) => $q->where('date', $includeGivenDate ? '<=' : '<', $toDate))
                ->sum('amount');
        }

        return $formatted ? number_format($balance, 1, '.', ',') : $balance;
    }

    public function getStatement($fromDate, $toDate)
    {
        // Opening balance (before fromDate)
        $openingBalance = $this->calculateBalance(null, $fromDate, false, false);

        // Period balance (fromDate → toDate)
        $periodBalance = $this->calculateBalance($fromDate, $toDate);

        $closingBalance = $openingBalance + $periodBalance;

        // Customer payments
        $customerPayments = collect(CustomerPayment::where('bank_account_id', $this->id)
            ->whereBetween('date', [$fromDate, $toDate])
            ->get())
            ->map(fn($p) => [
                'date' => $p->date ?? null,
                'reff_no' => $p->cheque_no ?? $p->slip_no ?? $p->transaction_id ?? $p->reff_no ?? null,
                'type' => 'customer_payment',
                'method' => $p->method ?? null,
                'bill' => $p->amount ?? 0,
                'payment' => 0,
                'account' => $p->customer?->customer_name ?? null,
                'created_at' => $p->created_at ?? null,
            ]);

        // Supplier payments
        $supplierPayments = collect(SupplierPayment::where('bank_account_id', $this->id)
            ->whereBetween('date', [$fromDate, $toDate])
            ->with('bankAccount.bank')
            ->get())
            ->map(fn($p) => [
                'date' => $p->date ?? null,
                'reff_no' => $p->cheque_no ?? $p->slip_no ?? $p->transaction_id ?? $p->reff_no ?? null,
                'type' => 'supplier_payment',
                'method' => $p->method ?? null,
                'payment' => $p->amount ?? 0,
                'bill' => 0,
                'account' => $p->bankAccount?->account_title || $p->bankAccount?->bank?->short_title
                    ? trim(($p->bankAccount?->account_title ?? '') . ' | ' . ($p->bankAccount?->bank?->short_title ?? ''), ' |')
                    : null,
                'created_at' => $p->created_at ?? null,
            ]);

        $statement = $customerPayments->merge($supplierPayments)
            ->sort(function ($a, $b) {
                $aDate = $a['date'] ?? '1970-01-01';
                $bDate = $b['date'] ?? '1970-01-01';
                $dateCompare = strcmp($aDate, $bDate);

                if ($dateCompare === 0) {
                    $aCreated = $a['created_at'] ?? '1970-01-01 00:00:00';
                    $bCreated = $b['created_at'] ?? '1970-01-01 00:00:00';
                    return strtotime($aCreated) <=> strtotime($bCreated);
                }

                return $dateCompare;
            })->values();

        // Totals
        $totals = [
            'bill' => $customerPayments->sum('bill'), // customer payments considered as 'bill' if needed
            'payment' => $supplierPayments->sum('payment'),
            'balance' => $customerPayments->sum('bill') - $supplierPayments->sum('payment'),
        ];

        return [
            'date' => $fromDate . ' - ' . $toDate,
            'name' => $this->account_title . ' | ' . $this->bank->short_title,
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'statements' => $statement,
            'totals' => $totals,
            'category' => 'account',
        ];
    }
}
