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
}
