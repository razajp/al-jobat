<?php

namespace App\Models;

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
    
    public function physicalQuantity()
    {
        return $this->hasMany(physicalQuantity::class, 'article_id');
    }
}
