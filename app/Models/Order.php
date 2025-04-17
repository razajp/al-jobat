<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'date',
        'discount',
        'netAmount',
        'articles',
        'order_no',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_no', 'order_no');
    }

    public function paymentPrograms()
    {
        return $this->hasOne(PaymentProgram::class, 'order_no', 'order_no');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function getArticles()
    {
        $rawArticles = json_decode($this->articles, true); // decode the JSON field
    
        if (!is_array($rawArticles)) return [];
    
        $articles = [];
    
        foreach ($rawArticles as $rawArticle) {
            $article = Article::where('article_no', $rawArticle['article_no'])->first();
    
            if ($article) {
                $articles[] = [
                    'ordered_quantity' => $rawArticle['ordered_quantity'],
                    'description' => $rawArticle['description'],
                    'article' => $article // contains all columns of the article
                ];
            }
        }
    
        return $articles;
    }
}
