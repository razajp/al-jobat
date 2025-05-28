<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supplier_name',
        'person_name',
        'urdu_title',
        'phone_number',
        'date',
        'categories_array',
    ];

    protected $appends = ['balance'];

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
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
    public function getBalanceAttribute()
    {
        return $this->calculateBalance();
    }
    public function calculateBalance($fromDate = null, $toDate = null, $formatted = false, $includeGivenDate = true)
    {
        $ordersQuery = $this->expenses();
        $paymentsQuery = $this->payments();
    
        // Handle different date scenarios
        if ($fromDate && $toDate) {
            if ($includeGivenDate) {
                $ordersQuery->whereBetween('date', [$fromDate, $toDate]);
                $paymentsQuery->whereBetween('date', [$fromDate, $toDate]);
            } else {
                $ordersQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                $paymentsQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
            }
        } elseif ($fromDate) {
            $operator = $includeGivenDate ? '>=' : '>';
            $ordersQuery->where('date', $operator, $fromDate);
            $paymentsQuery->where('date', $operator, $fromDate);
        } elseif ($toDate) {
            $operator = $includeGivenDate ? '<=' : '<';
            $ordersQuery->where('date', $operator, $toDate);
            $paymentsQuery->where('date', $operator, $toDate);
        }
    
        // Calculate totals
        $totalOrders = $ordersQuery->sum('amount') ?? 0;
        $totalPayments = $paymentsQuery->sum('amount') ?? 0;
    
        $balance = $totalOrders - $totalPayments;
    
        return $formatted ? number_format($balance, 1, '.', ',') : $balance;
    }    
}