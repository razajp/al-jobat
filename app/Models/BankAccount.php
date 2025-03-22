<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'sub_category', 'bank', 'account_title', 'account_no', 'date'];

    public function subCategory()
    {
        return $this->morphTo();
    }
}
