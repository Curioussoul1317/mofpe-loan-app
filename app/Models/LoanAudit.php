<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanAudit extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'loan_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}