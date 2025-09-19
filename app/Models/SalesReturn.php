<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'invoice_id',
        'date',
        'quantity',
        'amount',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function article() {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function invoice() {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
