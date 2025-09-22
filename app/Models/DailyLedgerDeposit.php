<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLedgerDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'method',
        'amount',
        'reff_no',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
