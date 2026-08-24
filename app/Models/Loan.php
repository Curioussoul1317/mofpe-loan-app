<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_number',
        'customer_id',
        'currency_id',
        'principal_amount',
        'start_date',
        'maturity_date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'principal_amount' => 'decimal:8',
            'start_date' => 'date',
            'maturity_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(LoanAudit::class);
    }
}