<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineProgram extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'customer_id', 'category', 'sub_category', 'amount', 'remarks'];

    function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function subCategory()
    {
        return $this->morphTo();
    }
}
