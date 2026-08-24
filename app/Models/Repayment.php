<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'currency_id',
        'amount',
        'payment_date',
        'reference_number',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:8',
            'payment_date' => 'date',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
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
}