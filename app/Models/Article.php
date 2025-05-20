<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_no',
        'date',
        'category',
        'size',
        'season',
        'quantity',
        'extra_pcs',
        'fabric_type',
        'sales_rate',
        'rates_array',
        'pcs_per_packet',
        'image',
    ];

    protected $casts = [
        'rates_array' => 'array',
        'date' => 'date',
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

    public function physicalQuantity()
    {
        return $this->hasMany(physicalQuantity::class, 'article_id');
    }
}
