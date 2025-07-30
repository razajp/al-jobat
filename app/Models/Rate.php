<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Rate extends Model
{
    protected $fillable = [
        'type_id',
        'effective_date',
        'categories',
        'seasons',
        'sizes',
        'title',
        'rate',
        'creator_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'categories' => 'array',
        'seasons' => 'array',
        'sizes' => 'array',
        'rate' => 'float',
    ];

    protected $hidden = [
        'type_id',
        'creator_id',
        'created_at',
        'updated_at',
    ];

    protected static function booted()
    {
        // Automatically set creator_id when creating a new Article
        static::creating(function ($thisModel) {
            if (Auth::check()) {
                $thisModel->creator_id = Auth::id();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(Setup::class, 'type_id', 'id');
    }
}
