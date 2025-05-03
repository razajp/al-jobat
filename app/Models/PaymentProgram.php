<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PaymentProgram extends Model
{
    use HasFactory;

    protected $fillable = ['program_no', 'order_no', 'date', 'customer_id', 'category', 'sub_category', 'amount', 'remarks'];

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

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    public function subCategory()
    {
        return $this->morphTo();
    }

    public function payments() {
        return $this->hasMany(Payment::class, "program_id");
    }
}
