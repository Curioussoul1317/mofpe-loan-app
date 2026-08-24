<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'decimal_places',
    ];

    protected function casts(): array
    {
        return [
            'decimal_places' => 'integer',
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function repayments(): HasMany
    {
        return $this->hasMany(Repayment::class);
    }
}