<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssuedFabric extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $fillable = [
        'date',
        'tag',
        'worker_id',
        'quantity',
        'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function worker() {
        return $this->belongsTo(Employee::class, 'worker_id');
    }
}
