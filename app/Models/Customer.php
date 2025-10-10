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
        return $this->morphMany(PaymentProgram::class, 'sub_category');
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
    public function getStatement($fromDate, $toDate, $type = 'summarized')
    {
        // 🧮 Opening & Closing Balances
        $openingBalance = $this->calculateBalance(null, $fromDate, false, false);
        $periodBalance  = $this->calculateBalance($fromDate, $toDate);
        $closingBalance = $openingBalance + $periodBalance;

        // --- SHARED QUERIES ---
        $invoiceQuery = $this->invoices()->whereBetween('date', [$fromDate, $toDate]);
        $paymentQuery = $this->payments()->whereBetween('date', [$fromDate, $toDate]);

        if ($type === 'summarized') {
            // 🧾 Fetch all invoices — ensure normalized date
            $invoices = $invoiceQuery->get()->map(fn($i) => [
                'type' => 'invoice',
                'date' => Carbon::parse($i->date)->toDateString(),
                'bill' => (float) ($i->netAmount ?? 0),
                'payment' => 0,
                'created_at' => $i->created_at,
            ]);

            // 💵 Fetch all payments — ensure normalized date
            $payments = $paymentQuery->get()->map(fn($p) => [
                'type' => 'payment',
                'date' => Carbon::parse($p->date)->toDateString(),
                'bill' => 0,
                'payment' => (float) ($p->amount ?? 0),
                'created_at' => $p->created_at,
            ]);

            // 📅 Merge and group by date (grouped correctly)
            $statement = $invoices
                ->merge($payments)
                ->groupBy('date')
                ->flatMap(function ($rows, $date) {
                    // 🔹 Sort each date’s records by created_at (earliest first)
                    $rows = $rows->sortBy('created_at');

                    $billSum = (float) $rows->sum('bill');
                    $paymentSum = (float) $rows->sum('payment');

                    $results = [];

                    // 💵 If that date has payments
                    if ($paymentSum > 0) {
                        $firstPaymentCreatedAt = $rows
                            ->where('type', 'payment')
                            ->min('created_at');

                        $results[] = [
                            'type' => 'payment',
                            'date' => Carbon::parse($date),
                            'bill' => 0,
                            'payment' => $paymentSum,
                            'created_at' => $firstPaymentCreatedAt,
                        ];
                    }

                    // 🧾 If that date has invoices
                    if ($billSum > 0) {
                        $firstInvoiceCreatedAt = $rows
                            ->where('type', 'invoice')
                            ->min('created_at');

                        $results[] = [
                            'type' => 'invoice',
                            'date' => Carbon::parse($date),
                            'bill' => $billSum,
                            'payment' => 0,
                            'created_at' => $firstInvoiceCreatedAt,
                        ];
                    }

                    // 🔸 Sort by created_at so whichever was entered first comes first
                    return collect($results)->sortBy('created_at')->values();
                })
                ->sortBy([
                    ['date', 'asc'],
                    ['created_at', 'asc'],
                ])
                ->values();
        }
        
        else {
            // 🧾 Detailed invoices
            $invoices = $invoiceQuery->get()->map(fn($i) => [
                'date' => $i->date,
                'reff_no' => $i->invoice_no,
                'type' => 'invoice',
                'bill' => (float) ($i->netAmount ?? 0),
                'payment' => 0,
                'created_at' => $i->created_at,
            ]);

            // 💵 Detailed payments
            $payments = $paymentQuery
                ->with('bankAccount.bank')
                ->get()
                ->map(fn($p) => [
                    'date' => $p->date,
                    'reff_no' => $p->cheque_no ?? $p->slip_no ?? $p->transaction_id ?? $p->reff_no,
                    'type' => 'payment',
                    'method' => $p->method,
                    'payment' => (float) ($p->amount ?? 0),
                    'bill' => 0,
                    'description' =>
                        $p->cheque_date?->format('d-M-Y, D')
                        ?? $p->slip_date?->format('d-M-Y, D')
                        ?? (($p->bankAccount?->account_title || $p->bankAccount?->bank?->short_title)
                            ? trim(
                                ($p->bankAccount?->account_title ?? '') .
                                ($p->bankAccount?->bank?->short_title
                                    ? ' | ' . $p->bankAccount->bank->short_title
                                    : ''),
                                ' |'
                            )
                            : null),
                    'created_at' => $p->created_at,
                ]);

            // 📅 Merge & sort (by date, then created_at)
            $statement = $invoices
                ->merge($payments)
                ->sortBy([
                    ['date', 'asc'],
                    ['created_at', 'asc'],
                ])
                ->values();
        }

        // 📊 Totals
        $billTotal = $statement->sum('bill');
        $paymentTotal = $statement->sum('payment');
        $totals = [
            'bill' => $billTotal,
            'payment' => $paymentTotal,
            'balance' => $billTotal - $paymentTotal,
        ];

        // 🧩 Final Response
        return [
            'date' => Carbon::parse($fromDate)->format('d-M-Y') . ' - ' . Carbon::parse($toDate)->format('d-M-Y'),
            'name' => "{$this->customer_name} | {$this->city->title}",
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'statements' => $statement,
            'totals' => $totals,
            'category' => 'customer',
        ];
    }
}
