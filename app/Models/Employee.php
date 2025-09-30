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
        'status',
        'profile_picture',
    ];

    protected $casts = [
        'joining_date' => 'date',
    ];

    protected $appends = ['balance'];

    public function type() {
        return $this->belongsTo(Setup::class, 'type_id');
    }

    public function tags() {
        return $this->hasMany(IssuedFabric::class, 'worker_id');
    }

    public function productions() {
        return $this->hasMany(Production::class, 'worker_id');
    }

    public function payments() {
        return $this->hasMany(EmployeePayment::class, 'employee_id');
    }

    public function supplier() {
        return $this->hasOne(Supplier::class, 'worker_id');
    }

    public function getBalanceAttribute()
    {
        return $this->calculateBalance();
    }
    public function calculateBalance($fromDate = null, $toDate = null, $formatted = false, $includeGivenDate = true)
    {
        $productionsQuery = $this->productions();
        $paymentsQuery = $this->payments();

        // Handle different date scenarios
        if ($fromDate && $toDate) {
            if ($includeGivenDate) {
                $productionsQuery->whereBetween('date', [$fromDate, $toDate]);
                $paymentsQuery->whereBetween('date', [$fromDate, $toDate]);
            } else {
                $productionsQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
                $paymentsQuery->where('date', '>', $fromDate)->where('date', '<', $toDate);
            }
        } elseif ($fromDate) {
            $operator = $includeGivenDate ? '>=' : '>';
            $productionsQuery->where('date', $operator, $fromDate);
            $paymentsQuery->where('date', $operator, $fromDate);
        } elseif ($toDate) {
            $operator = $includeGivenDate ? '<=' : '<';
            $productionsQuery->where('date', $operator, $toDate);
            $paymentsQuery->where('date', $operator, $toDate);
        }

        // Calculate totals
        $totalInvoices = $productionsQuery->sum('netAmount') ?? 0;
        $totalPayments = $paymentsQuery->sum('amount') ?? 0;

        $balance = $totalInvoices - $totalPayments;

        return $formatted ? number_format($balance, 1, '.', ',') : $balance;
    }
}
