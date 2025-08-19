<?php

namespace App\Models;

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

        // Always eager load the associated creator
        // static::addGlobalScope('withCreator', function (Builder $builder) {
        //     $builder->with('creator');
        // });
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
        $ordersQuery = $this->orders();
        $invoicesQuery = $this->invoices()->whereNotNull('shipment_no');
        $paymentsQuery = $this->payments();

        // Handle different date scenarios
        if ($fromDate && $toDate) {
            if ($includeGivenDate) {
                $ordersQuery->whereBetween('date', [$fromDate, $toDate]);
                $invoicesQuery->whereBetween('date', [$fromDate, $toDate]);
                $paymentsQuery->whereBetween('date', [$fromDate, $toDate]);
            } else {
                $ordersQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                $invoicesQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                $paymentsQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
            }
        } elseif ($fromDate) {
            $operator = $includeGivenDate ? '>=' : '>';
            $ordersQuery->where('date', $operator, $fromDate);
            $invoicesQuery->where('date', $operator, $fromDate);
            $paymentsQuery->where('date', $operator, $fromDate);
        } elseif ($toDate) {
            $operator = $includeGivenDate ? '<=' : '<';
            $ordersQuery->where('date', $operator, $toDate);
            $invoicesQuery->where('date', $operator, $toDate);
            $paymentsQuery->where('date', $operator, $toDate);
        }

        // Calculate totals
        $totalOrders = $ordersQuery->sum('netAmount') ?? 0;
        $totalInvoices = $invoicesQuery->sum('netAmount') ?? 0;
        $totalPayments = $paymentsQuery->sum('amount') ?? 0;

        $balance = ($totalOrders + $totalInvoices) - $totalPayments;

        return $formatted ? number_format($balance, 1, '.', ',') : $balance;
    }

    public function getStatement($fromDate, $toDate)
    {
        // Opening balance (before fromDate)
        $openingBalance = $this->calculateBalance(null, $fromDate, false, false);

        // Period balance (fromDate → toDate)
        $periodBalance = $this->calculateBalance($fromDate, $toDate);

        $closingBalance = $openingBalance + $periodBalance;

        // Fetch data safely
        $orders = collect($this->orders()
            ->whereBetween('date', [$fromDate, $toDate])
            ->get())
            ->map(fn($o) => [
                'type' => 'order',
                'id' => $o->id ?? null,
                'date' => $o->date ?? null,
                'created_at' => $o->created_at ?? null,
                'amount' => $o->netAmount ?? 0,
                'details' => $o,
            ]);

        $invoices = collect($this->invoices()
            ->whereNotNull('shipment_no')
            ->whereBetween('date', [$fromDate, $toDate])
            ->get())
            ->map(fn($i) => [
                'type' => 'invoice',
                'id' => $i->id ?? null,
                'date' => $i->date ?? null,
                'created_at' => $i->created_at ?? null,
                'amount' => $i->netAmount ?? 0,
                'details' => $i,
            ]);

        $payments = collect($this->payments()
            ->whereBetween('date', [$fromDate, $toDate])
            ->get())
            ->map(fn($p) => [
                'type' => 'payment',
                'id' => $p->id ?? null,
                'date' => $p->date ?? null,
                'created_at' => $p->created_at ?? null,
                'amount' => $p->amount ?? 0,
                'details' => $p,
            ]);

        // Merge and sort safely
        $statement = $orders->merge($invoices)->merge($payments)
            ->sort(function ($a, $b) {
                $aDate = $a['date'] ?? '1970-01-01';
                $bDate = $b['date'] ?? '1970-01-01';
                $dateCompare = strcmp($bDate, $aDate);

                if ($dateCompare === 0) {
                    $aCreated = $a['created_at'] ?? '1970-01-01 00:00:00';
                    $bCreated = $b['created_at'] ?? '1970-01-01 00:00:00';
                    return strtotime($bCreated) <=> strtotime($aCreated);
                }

                return $dateCompare;
            })->values();

        // Totals
        $totals = [
            'orders' => $orders->sum('amount'),
            'invoices' => $invoices->sum('amount'),
            'payments' => $payments->sum('amount'),
        ];

        return [
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'statement' => $statement,
            'totals' => $totals,
        ];
    }
}
