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
        return $this->morphMany(PaymentProgram::class, 'sub_category');
    }

    public function bankAccounts()
    {
        return $this->morphMany(BankAccount::class, 'sub_category');
    }

    public function getBalanceAttribute()
    {
        return $this->calculateBalance();
    }

    public function calculateBalance($fromDate = null, $toDate = null)
    {
        $ordersQuery = $this->orders();
        $paymentsQuery = $this->payments();

        // Apply date filters if provided
        if ($fromDate || $toDate) {
            $fromDate = $fromDate ?? '0000-01-01'; // Default to start of time
            $toDate = $toDate ?? now(); // Default to today

            $ordersQuery->whereBetween('date', [$fromDate, $toDate]);
            $paymentsQuery->whereBetween('date', [$fromDate, $toDate]);
        }

        $totalOrders = $ordersQuery->sum('netAmount');
        $totalPayments = $paymentsQuery->sum('amount');

        $balance = $totalOrders - $totalPayments;

        return number_format($balance, 1); // Formatted balance
    }
}
