<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $fillable = [
        "cargo_no",
        "date",
        "cargo_name",
        "invoices_array",
    ];
    protected $appends = ['invoices'];
    public function getInvoicesAttribute()
    {
        $RawInvoices = json_decode($this->invoices_array, true);

        if (!is_array($RawInvoices)) return [];
    
        $invoices = [];

        foreach ($RawInvoices as $RawInvoice) {
            $invoice = Invoice::with('customer')->where('id', $RawInvoice['id'])->first();
    
            if ($invoice) {
                $invoices[] = $invoice;
            }
        }

        return $invoices;
    }
}
