<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'tenure_months',
        'purpose',
        'status',
        'interest_rate',
        'monthly_emi',
        'admin_notes',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_emi' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function repayments()
    {
        return $this->hasMany(Repayment::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculateEmi(float $annualRate): float
    {
        $principal = (float) $this->amount;
        $n = $this->tenure_months;

        if ($annualRate == 0) {
            return round($principal / $n, 2);
        }

        $r = $annualRate / 12 / 100;
        $emi = $principal * $r * pow(1 + $r, $n) / (pow(1 + $r, $n) - 1);

        return round($emi, 2);
    }

    public function generateRepaymentSchedule(): void
    {
        $startDate = Carbon::now()->addMonth();

        for ($i = 1; $i <= $this->tenure_months; $i++) {
            Repayment::create([
                'loan_id'        => $this->id,
                'installment_no' => $i,
                'due_date'       => $startDate->copy()->addMonths($i - 1)->format('Y-m-d'),
                'due_amount'     => $this->monthly_emi,
                'paid_amount'    => 0,
                'status'         => 'pending',
            ]);
        }
    }

    public function getTotalPayableAttribute(): float
    {
        return round((float) $this->monthly_emi * $this->tenure_months, 2);
    }

    public function getTotalInterestAttribute(): float
    {
        return round($this->total_payable - (float) $this->amount, 2);
    }

    public function getPaidInstallmentsCountAttribute(): int
    {
        return $this->repayments()->where('status', 'paid')->count();
    }
}
