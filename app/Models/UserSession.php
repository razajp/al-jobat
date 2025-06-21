<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $fillable = [
        'user_id',
        'session_token',
        'last_activity',
        'is_active',
    ];

    /**
     * Relationship to the user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if session is still valid based on activity time (optional).
     */
    public function isStillActive($timeoutInMinutes = 60)
    {
        return $this->is_active && $this->last_activity >= now()->subMinutes($timeoutInMinutes);
    }
}
