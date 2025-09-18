<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'person_name',
        'urdu_title',
        'phone_number',
        'date',
        'category',
        'city_id',
        'address',
    ];

    protected $hidden = [
        'user_id',
        'creator_id',
        'city_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted()
    {
        // Automatically set creator_id when creating a new Article
        static::creating(function ($thisModel) {
            if (Auth::check()) {
                $thisModel->creator_id = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    protected $appends = ['balance'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function city()
    {
        return $this->belongsTo(Setup::class, 'city_id', 'id')->where('type', 'city');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class, 'customer_id');
    }

    public function paymentPrograms()
    {
        return $this->hasMany(PaymentProgram::class, 'customer_id');
    }

    public function bankAccounts()
    {
        return $this->morphMany(BankAccount::class, 'sub_category');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'customer_id');
    }

    public function getBalanceAttribute()
    {
        return $this->calculateBalance();
    }
    public function calculateBalance($fromDate = null, $toDate = null, $formatted = false, $includeGivenDate = true)
    {
        $invoicesQuery = $this->invoices()->whereNotNull('shipment_no');
        $paymentsQuery = $this->payments();

        // Handle different date scenarios
        if ($fromDate && $toDate) {
            if ($includeGivenDate) {
                $invoicesQuery->whereBetween('date', [$fromDate, $toDate]);
                $paymentsQuery->whereBetween('date', [$fromDate, $toDate]);
            } else {
                $invoicesQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                $paymentsQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
            }
        } elseif ($fromDate) {
            $operator = $includeGivenDate ? '>=' : '>';
            $invoicesQuery->where('date', $operator, $fromDate);
            $paymentsQuery->where('date', $operator, $fromDate);
        } elseif ($toDate) {
            $operator = $includeGivenDate ? '<=' : '<';
            $invoicesQuery->where('date', $operator, $toDate);
            $paymentsQuery->where('date', $operator, $toDate);
        }

        // Calculate totals
        $totalInvoices = $invoicesQuery->sum('netAmount') ?? 0;
        $totalPayments = $paymentsQuery->sum('amount') ?? 0;

        $balance = $totalInvoices - $totalPayments;

        return $formatted ? number_format($balance, 1, '.', ',') : $balance;
    }

    public function getStatement($fromDate, $toDate)
    {
        // Opening balance (before fromDate)
        $openingBalance = $this->calculateBalance(null, $fromDate, false, false);

        // Period balance (fromDate → toDate)
        $periodBalance = $this->calculateBalance($fromDate, $toDate);

        $closingBalance = $openingBalance + $periodBalance;

        $invoices = collect($this->invoices()
            ->whereBetween('date', [$fromDate, $toDate])
            ->get())
            ->map(fn($i) => [
                'date' => $i->date ?? null,
                'reff_no' => $i->invoice_no ?? null,
                'type' => 'invoice',
                'bill' => $i->netAmount ?? 0,
                'payment' =>  0,
                'created_at' => $i->created_at ?? null,
            ]);

        $payments = collect($this->payments()
            ->whereBetween('date', [$fromDate, $toDate])
            ->with('bankAccount.bank')
            ->get())
            ->map(fn($p) => [
                'date' => $p->date ?? null,
                'reff_no' => $p->cheque_no ?? $p->slip_no ?? $p->transaction_id ?? $p->reff_no ?? null,
                'type' => 'payment',
                'method' => $p->method ?? null,
                'payment' => $p->amount ?? 0,
                'bill' =>  0,
                'description' => $p->cheque_date?->format('d-M-Y, D') ?? $p->slip_date?->format('d-M-Y, D') ?? ($p->bankAccount?->account_title || $p->bankAccount?->bank?->short_title ? trim(($p->bankAccount?->account_title ?? '') . ($p->bankAccount?->bank?->short_title ? ' | ' . $p->bankAccount->bank->short_title : ''), ' |' ) : null ),
                'created_at' => $p->created_at ?? null,
            ]);

        $statement = $invoices->merge($payments)
            ->sort(function ($a, $b) {
                $aDate = $a['date'] ?? '1970-01-01';
                $bDate = $b['date'] ?? '1970-01-01';
                $dateCompare = strcmp($aDate, $bDate); // ascending (oldest first)

                if ($dateCompare === 0) {
                    $aCreated = $a['created_at'] ?? '1970-01-01 00:00:00';
                    $bCreated = $b['created_at'] ?? '1970-01-01 00:00:00';
                    return strtotime($aCreated) <=> strtotime($bCreated); // oldest created_at first
                }

                return $dateCompare;
            })->values();

        // Totals
        $totals = [
            'bill' => $invoices->sum('bill'),
            'payment' => $payments->sum('payment'),
            'balance' => $invoices->sum('bill') - $payments->sum('payment'),
        ];

        return [
            'date' => Carbon::parse($fromDate)->format('d-M-Y') . ' - ' . Carbon::parse($toDate)->format('d-M-Y'),
            'name' => $this->customer_name . ' | ' . $this->city->title,
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'statements' => $statement,
            'totals' => $totals,
            'category' => 'customer',
        ];
    }
}
