<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        "customer_id",
        "date",
        "type",
        "amount",
        "cheque_no",
        "slip_no",
        "transition_id",
        "cheque_date",
        "slip_date",
        "clear_date",
        "bank",
        "remarks",
    ];

    public function customer() {
        return $this->belongsTo(Customer::class, "customer_id");
    }
}
