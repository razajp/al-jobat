<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'city',
        'address',
    ];

    protected $appends = ['balance'];
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'customer_id');
    }

    public function paymentPrograms()
    {
        return $this->hasMany(PaymentProgram::class, 'customer_id');
    }

    public function bankAccounts()
    {
        return $this->morphMany(BankAccount::class, 'sub_category');
    }

    public function getBalanceAttribute()
    {
        return $this->calculateBalance();
    }

    public function calculateBalance($fromDate = null, $toDate = null, $formatted = false, $includeGivenDate = true)
    {
        $ordersQuery = $this->orders();
        $paymentsQuery = $this->payments();

        // Handle different date scenarios
        if ($fromDate && $toDate) {
            // Between two dates
            $ordersQuery->whereBetween('date', [$fromDate, $toDate]);
            $paymentsQuery->whereBetween('date', [$fromDate, $toDate]);

            if (!$includeGivenDate) {
                $ordersQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                $paymentsQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
            }
        } elseif ($fromDate) {
            // From a specific date onwards
            $ordersQuery->where('date', '>=', $fromDate);
            $paymentsQuery->where('date', '>=', $fromDate);

            if (!$includeGivenDate) {
                $ordersQuery->where('date', '>', $fromDate);
                $paymentsQuery->where('date', '>', $fromDate);
            }
        } elseif ($toDate) {
            // Up to a specific date
            $ordersQuery->where('date', '<=', $toDate);
            $paymentsQuery->where('date', '<=', $toDate);

            if (!$includeGivenDate) {
                $ordersQuery->where('date', '<', $toDate);
                $paymentsQuery->where('date', '<', $toDate);
            }
        }

        // Calculate totals
        $totalOrders = $ordersQuery->sum('netAmount') ?? 0;
        $totalPayments = $paymentsQuery->sum('amount') ?? 0;

        $balance = $totalOrders - $totalPayments;

        // Return balance (formatted or raw)
        return $formatted ? number_format($balance, 1, '.', ',') : $balance;
    }
}
