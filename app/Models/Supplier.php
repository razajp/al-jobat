<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supplier_name',
        'person_name',
        'phone_number',
        'date',
        'categories_array',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function onlinePrograms()
    {
        return $this->morphMany(OnlineProgram::class, 'sub_category');
    }

    public function bankAccounts()
    {
        return $this->morphMany(BankAccount::class, 'sub_category');
    }
}