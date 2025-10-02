<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_type_id',
        'location_id',
        'account_title',
        'account_no',
    ];

    public function billType() {
        return $this->belongsTo(Setup::class, 'bill_type_id')->where('type', 'utility_bill_type');
    }

    public function location() {
        return $this->belongsTo(Setup::class, 'location_id')->where('type', 'utility_bill_location');
    }
}
