<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SupplierPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        "supplier_id",
        "date",
        "method",
        "amount",
        "cheque_id",
        "slip_id",
        "program_id",
        "remarks",
        "voucher_id",
    ];

    protected static function booted()
    {
        // Automatically set creator_id when creating a new Article
        static::creating(function ($thisModel) {
            if (Auth::check()) {
                $thisModel->creator_id = Auth::id();
            }
        });

        // Always eager load the associated creator
        static::addGlobalScope('withCreator', function (builder $builder) {
            $builder->with('creator');
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, "supplier_id");
    }
    
    public function program()
    {
        return $this->belongsTo(PaymentProgram::class, "program_id");
    }
    
    public function cheque()
    {
        return $this->belongsTo(CustomerPayment::class, "cheque_id");
    }
    
    public function slip()
    {
        return $this->belongsTo(CustomerPayment::class, "slip_id");
    }

    public function voucher() {
        return $this->belongsTo(Voucher::class, "voucher_id");
    }
}
