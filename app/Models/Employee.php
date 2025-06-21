<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    
    protected $fillable = [
        "category",
        "type_id",
        "employee_name",
        "urdu_title",
        "phone_number",
        "joining_date",
        "cnic_no",
        "salary",
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function type() {
        return $this->belongsTo(Setup::class, 'type_id');
    }
}
