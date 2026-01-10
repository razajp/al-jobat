<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderArticles extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'article_id',
        'description',
        'ordered_pcs',
        'dispatched_pcs',
        'returned_pcs',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
