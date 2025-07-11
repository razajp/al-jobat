<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = ['category', 'sub_category', 'bank_id', 'account_title', 'date', 'remarks', 'account_no', 'chqbk_serial_start', 'chqbk_serial_end', 'status'];

    protected $hidden = [
        'bank_id',
        'creator_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected $appends = ['balance', 'available_cheques'];

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

    public function subCategory()
    {
        return $this->morphTo();
    }
    public function bank()
    {
        return $this->belongsTo(Setup::class, 'bank_id')->where('type', 'bank_name');
    }
    public function paymentPrograms()
    {
        return $this->morphMany(PaymentProgram::class, 'sub_category');
    }
    public function bankAccounts()
    {
        return $this->hasOne(BankAccount::class, 'id');
    }
    public function getAvailableChequesAttribute()
    {
        if ($this->category !== 'self') {
            return null;
        }

        // Get all the used cheques for this bank account
        $usedCheques = SupplierPayment::where('bank_account_id', $this->id)
            ->pluck('cheque_no')
            ->toArray();

        // Generate full range of cheque numbers
        $start = (int) $this->chqbk_serial_start;
        $end = (int) $this->chqbk_serial_end;
        $fullRange = range($start, $end);

        // Filter out the used ones
        $available = array_diff($fullRange, $usedCheques);

        // Return available cheque numbers
        return array_values($available);
    }

    public function getBalanceAttribute()
    {
        return $this->calculateBalance();
    }
    public function calculateBalance($fromDate = null, $toDate = null, $formatted = false, $includeGivenDate = true)
    {
        $customerPaymetns = CustomerPayment::where('bank_account_id', $this->id);
        $supplierPaymetns = SupplierPayment::where('bank_account_id', $this->id);
    
        // Handle different date scenarios
        if ($fromDate && $toDate) {
            if ($includeGivenDate) {
                $customerPaymetns->whereBetween('date', [$fromDate, $toDate]);
                $supplierPaymetns->whereBetween('date', [$fromDate, $toDate]);
            } else {
                $customerPaymetns->where('date', '>', $fromDate)->where('date', '<', $toDate);
                $supplierPaymetns->where('date', '>', $fromDate)->where('date', '<', $toDate);
            }
        } elseif ($fromDate) {
            $operator = $includeGivenDate ? '>=' : '>';
            $customerPaymetns->where('date', $operator, $fromDate);
            $supplierPaymetns->where('date', $operator, $fromDate);
        } elseif ($toDate) {
            $operator = $includeGivenDate ? '<=' : '<';
            $customerPaymetns->where('date', $operator, $toDate);
            $supplierPaymetns->where('date', $operator, $toDate);
        }
    
        // Calculate totals
        $totalPayments = $customerPaymetns->sum('netAmount') ?? 0;
        $totalPays = $supplierPaymetns->sum('amount') ?? 0;

        $balance = $totalPays - $totalPayments;

        return $formatted ? number_format($balance, 1, '.', ',') : $balance;
    }    
}
