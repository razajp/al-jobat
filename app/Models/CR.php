<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CR extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'voucher_id',
        'return_payments',
        'new_payments',
    ];

    protected $casts = [
        'date' => 'date',
        'return_payments' => 'array',
        'new_payments' => 'array',
    ];

    public function voucher() {
        return $this->belongsTo(Voucher::class, 'voucher_id');
    }
}
