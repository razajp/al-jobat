<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'date',
        'discount',
        'netAmount',
        'ordered_articles',
        'order_no',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
