<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'discount',
        'netAmount',
        'articles',
        'shipment_no',
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

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'shipment_no', 'shipment_no');
    }
    
    public function getArticles()
    {
        $rawArticles = json_decode($this->articles, true); // decode the JSON field
    
        if (!is_array($rawArticles)) return [];
    
        $articles = [];
    
        foreach ($rawArticles as $rawArticle) {
            $article = Article::where('id', $rawArticle['id'])->first();
    
            if ($article) {
                $articles[] = [
                    'shipment_quantity' => $rawArticle['shipment_quantity'],
                    'description' => $rawArticle['description'],
                    'article' => $article // contains all columns of the article
                ];
            }
        }
    
        return $articles;
    }
}
