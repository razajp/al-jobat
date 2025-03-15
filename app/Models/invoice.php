<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        "invoice_no",
        "order_no",
        "date",
        "netAmount",
        "articles_in_invoice",
    ];

    public function order() {
        return $this->belongsTo(Order::class, 'order_no');
    }
}
