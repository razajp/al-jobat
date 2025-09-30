<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Supplier extends Model
{
    use HasFactory;

    protected $hidden = [
        'user_id',
        'creator_id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'user_id',
        'supplier_name',
        'person_name',
        'urdu_title',
        'phone_number',
        'date',
        'categories_array',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $appends = ['balance', 'categories'];

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

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paymentPrograms()
    {
        return $this->morphMany(PaymentProgram::class, 'sub_category');
    }

    public function bankAccounts()
    {
        return $this->morphMany(BankAccount::class, 'sub_category');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class, 'supplier_id');
    }

    public function getCategoriesAttribute() {
        $ids = json_decode($this->categories_array, true);
        return is_array($ids) ? Setup::whereIn('id', $ids)->get() : [];
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function worker()
    {
        return $this->belongsTo(Employee::class, 'worker_id');
    }

    public function getBalanceAttribute()
    {
        return $this->calculateBalance();
    }

    public function calculateBalance($fromDate = null, $toDate = null, $formatted = false, $includeGivenDate = true)
    {
        $expenseQuery = $this->expenses();
        $paymentsQuery = $this->payments()
            ->whereNotNull('voucher_id')
            ->whereIn('method', [
                'Cheque',
                'Cash',
                'Slip',
                'ATM',
                'Self Cheque',
                'Program',
                'Adjustment',
            ]);

        // Worker productions include karo agar worker set hai
        $productionQuery = null;
        if ($this->worker) {
            $productionQuery = $this->worker->productions()->select([
                'id',
                'worker_id',
                'date',
                'amount', // yeh field apne table ka check kar lena (piece_rate * pcs etc.)
            ]);
        }

        // Date filtering handle karo
        if ($fromDate && $toDate) {
            if ($includeGivenDate) {
                $expenseQuery->whereBetween('date', [$fromDate, $toDate]);
                $paymentsQuery->whereBetween('date', [$fromDate, $toDate]);
                if ($productionQuery) {
                    $productionQuery->whereBetween('date', [$fromDate, $toDate]);
                }
            } else {
                $expenseQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                $paymentsQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                if ($productionQuery) {
                    $productionQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                }
            }
        } elseif ($fromDate) {
            $operator = $includeGivenDate ? '>=' : '>';
            $expenseQuery->where('date', $operator, $fromDate);
            $paymentsQuery->where('date', $operator, $fromDate);
            if ($productionQuery) {
                $productionQuery->where('date', $operator, $fromDate);
            }
        } elseif ($toDate) {
            $operator = $includeGivenDate ? '<=' : '<';
            $expenseQuery->where('date', $operator, $toDate);
            $paymentsQuery->where('date', $operator, $toDate);
            if ($productionQuery) {
                $productionQuery->where('date', $operator, $toDate);
            }
        }

        // Totals
        $totalExpense = $expenseQuery->sum('amount') ?? 0;
        $totalPayments = $paymentsQuery->sum('amount') ?? 0;
        $totalProduction = $productionQuery ? $productionQuery->sum('amount') : 0;

        // Expense + Production dono mila ke
        $balance = ($totalExpense + $totalProduction) - $totalPayments;

        return $formatted ? number_format($balance, 1, '.', ',') : $balance;
    }

    public function getStatement($fromDate, $toDate)
    {
        // Opening balance (before fromDate)
        $openingBalance = $this->calculateBalance(null, $fromDate, false, false);

        // Period balance (fromDate → toDate)
        $periodBalance = $this->calculateBalance($fromDate, $toDate);

        $closingBalance = $openingBalance + $periodBalance;

        $expense = collect($this->expenses()
            ->whereBetween('date', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay()
            ])
            ->get())
            ->map(fn($i) => [
                'date' => $i->date ?? null,
                'reff_no' => $i->reff_no ?? null,
                'type' => 'invoice',
                'bill' => $i->amount ?? 0,
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
                'account' => $p->bankAccount?->account_title || $p->bankAccount?->bank?->short_title ? trim(($p->bankAccount?->account_title ?? '') . ' | ' . ($p->bankAccount?->bank?->short_title ?? ''), ' |') : null,
                'created_at' => $p->created_at ?? null,
            ]);

        $statement = $expense->merge($payments)
            ->sort(function ($a, $b) {
                $aDate = $a['date'] ?? '1970-01-01';
                $bDate = $b['date'] ?? '1970-01-01';

                // Compare by actual timestamps
                $dateCompare = strtotime($aDate) <=> strtotime($bDate);

                if ($dateCompare === 0) {
                    $aCreated = $a['created_at'] ?? $aDate . ' 00:00:00';
                    $bCreated = $b['created_at'] ?? $bDate . ' 00:00:00';
                    return strtotime($aCreated) <=> strtotime($bCreated);
                }

                return $dateCompare;
            })->values();

        // Totals
        $totals = [
            'bill' => $expense->sum('bill'),
            'payment' => $payments->sum('payment'),
            'balance' => $expense->sum('bill') - $payments->sum('payment'),
        ];

        return [
            'date' => $fromDate . ' - ' . $toDate,
            'name' => $this->supplier_name,
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance,
            'statements' => $statement,
            'totals' => $totals,
            'category' => 'supplier',
        ];
    }
}
