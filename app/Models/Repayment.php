<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repayment extends Model
{
    protected $fillable = [
        'loan_id',
        'installment_no',
        'due_date',
        'due_amount',
        'paid_amount',
        'paid_at',
        'status',
    ];

    protected $casts = [
        'due_date'   => 'date',
        'paid_at'    => 'datetime',
        'due_amount' => 'decimal:2',
        'paid_amount'=> 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
