<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PartialClear extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'clear_date',
        'bank_account_id',
        'amount',
        'reff_no',
        'remarks',
        'creator_id',
    ];

    protected $hidden = [
        'creator_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'clear_date' => 'date',
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
}
