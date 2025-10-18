<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DR extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'date',
        'return_payments',
        'new_payments',
    ];

    protected $casts = [
        'date' => 'date',
        'return_payments' => 'array',
        'new_payments' => 'array',
    ];

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
