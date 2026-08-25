<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,

            'loan_number' => $this->loan_number,

            'principal_amount' =>
                $this->principal_amount,

            'start_date' =>
                $this->start_date?->format('Y-m-d'),

            'maturity_date' =>
                $this->maturity_date?->format('Y-m-d'),

            'status' => $this->status,

            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ],

            'currency' => [
                'id' => $this->currency->id,
                'code' => $this->currency->code,
                'name' => $this->currency->name,
                'decimal_places' =>
                    $this->currency->decimal_places,
            ],

            'created_by' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
            ],

            'created_at' =>
                $this->created_at?->toISOString(),
        ];
    }
}