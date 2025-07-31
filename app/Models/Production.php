<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'article_id',
        'work_id',
        'worker_id',
        'tags',
        'title',
        'rate',
    ];

    protected $casts = [
        'date' => 'date',
        'tags' => 'array',
        'rate' => 'float',
    ];

    protected $hidden = [
        'article_id',
        'work_id',
        'worker_id',
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

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    public function work()
    {
        return $this->belongsTo(Setup::class, 'work_id', 'id');
    }

    public function worker()
    {
        return $this->belongsTo(Employee::class, 'worker_id', 'id');
    }
}
