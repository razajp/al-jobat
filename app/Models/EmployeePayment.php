<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'method',
        'amount',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function employee() {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
