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
