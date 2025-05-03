<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        "customer_id",
        "date",
        "type",
        "method",
        "amount",
        "cheque_no",
        "slip_no",
        "transition_id",
        "cheque_date",
        "slip_date",
        "clear_date",
        "bank",
        "remarks",
        "program_id",
        "bank_account_id",
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
        static::addGlobalScope('withCreator', function (Builder $builder) {
            $builder->with('creator');
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    // Relationship with the Customer model
    public function customer()
    {
        return $this->belongsTo(Customer::class, "customer_id");
    }
    
    public function program()
    {
        return $this->belongsTo(PaymentProgram::class, "program_id");
    }
    
    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class, "bank_account_id");
    }
}
