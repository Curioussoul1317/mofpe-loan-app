<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => Money::format(
                $this->amount,
                $this->currency->decimal_places
            ),
            'currency' => $this->currency->code,
            'payment_date' => $this->payment_date->format('Y-m-d'),
            'reference_number' => $this->reference_number,
            'created_by' => $this->creator->name,
        ];
    }
}