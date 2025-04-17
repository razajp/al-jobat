<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'discount',
        'netAmount',
        'articles',
        'shipment_no',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'shipment_no', 'shipment_no');
    }
}
