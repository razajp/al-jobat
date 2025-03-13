<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhysicalQuantity extends Model
{
    use HasFactory;

    protected $fillable = [
        "date",
        "article_id",
        "packets",
    ];
    
    public function article() 
    {
        return $this->belongsTo(Article::class);
    }
}
