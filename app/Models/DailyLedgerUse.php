<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLedgerUse extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'case',
        'amount',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
