<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentArticles extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'article_id',
        'description',
        'shipment_pcs',
        'returned_pcs',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
