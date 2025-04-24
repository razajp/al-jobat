<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        "invoice_no",
        "order_no",
        "shipment_no",
        "customer_id",
        "date",
        "netAmount",
        "articles_in_invoice",
    ];

    public function order() {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    public function shipment() {
        return $this->belongsTo(Shipment::class, 'shipment_no', 'shipment_no');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
