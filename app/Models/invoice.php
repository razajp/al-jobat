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
        "cotton_count",
        "articles_in_invoice",
    ];
    protected $appends = ['is_in_cargo'];
    public function order() {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    public function shipment() {
        return $this->belongsTo(Shipment::class, 'shipment_no', 'shipment_no');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function getIsInCargoAttribute()
    {
        // Loop through all cargos
        $cargos = Cargo::all();

        foreach ($cargos as $cargo) {
            $invoices = json_decode($cargo->invoices_array, true);

            if (!is_array($invoices)) continue;

            foreach ($invoices as $invoice) {
                if (isset($invoice['id']) && $invoice['id'] == $this->id) {
                    return true;
                }
            }
        }

        return false;
    }
}
