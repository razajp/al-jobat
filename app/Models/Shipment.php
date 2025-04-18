<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
