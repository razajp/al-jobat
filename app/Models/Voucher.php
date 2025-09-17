<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $fillable = [
        "supplier_id",
        "date",
        "voucher_no",
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function supplier() {
        return $this->belongsTo(Supplier::class, "supplier_id");
    }

    public function payments() {
        return $this->hasMany(SupplierPayment::class);
    }
}
