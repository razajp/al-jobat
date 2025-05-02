<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProgram extends Model
{
    use HasFactory;

    protected $fillable = ['program_no', 'order_no', 'date', 'customer_id', 'category', 'sub_category', 'amount', 'remarks'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    public function subCategory()
    {
        return $this->morphTo();
    }

    public function payments() {
        return $this->hasMany(Payment::class, "program_id");
    }
}
