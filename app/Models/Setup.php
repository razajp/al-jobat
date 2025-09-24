<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Setup extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'type',
        'title',
        'short_title'
    ];

    public function scopeWorkerTypesNotE($query)
    {
        return $query->where('type', 'worker_type')
                    ->where('short_title', 'not like', '%|E%');
    }
}
