<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PaymentProgram extends Model
{
    use HasFactory;

    protected $fillable = ['program_no', 'order_no', 'date', 'customer_id', 'category', 'sub_category', 'amount', 'remarks'];

    protected $appends = ['payments'];

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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    public function subCategory()
    {
        return $this->morphTo();
    }
    public function customerPayments()
    {
        return $this->hasMany(CustomerPayment::class, 'program_id');
    }

    public function supplierPayments()
    {
        return $this->hasMany(SupplierPayment::class, 'program_id');
    }

    // Custom accessor to merge both types of payments
    public function getPaymentsAttribute()
    {
        // Return merged customerPayments and supplierPayments with their bank account relations
        return $this->customerPayments->merge($this->supplierPayments);
    }

    // Custom method to eager load payments along with their bank account and bank relations
    public function scopeWithPayments($query)
    {
        return $query->with([
            'customerPayments.bankAccount.bank',  // Eager load bankAccount -> bank for customerPayments
            'supplierPayments.bankAccount.bank',  // Eager load bankAccount -> bank for supplierPayments
        ]);
    }
}
