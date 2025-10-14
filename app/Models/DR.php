<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DR extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'd_r_no',
        'return_payments',
        'new_payments',
    ];

    protected $casts = [
        'date' => 'date',
        'return_payments' => 'array',
        'new_payments' => 'array',
    ];

    public function payments() {
        return $this->hasMany(SupplierPayment::class, 'c_r_id');
    }
}
