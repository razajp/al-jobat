<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'month',
        'units',
        'amount',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function account() {
        return $this->belongsTo(UtilityAccount::class, 'account_id');
    }
}
