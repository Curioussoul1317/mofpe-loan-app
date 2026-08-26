<?php

namespace App\Http\Resources;

use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $places = $this->currency->decimal_places;

        $paid = (string) (
            $this->repayments_sum_amount ?? '0'
        );

        $outstanding = bcsub(
            $this->principal_amount,
            $paid,
            8
        );

        return [
            'id' => $this->id,
            'loan_number' => $this->loan_number,

            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
            ],

            'currency' => $this->currency->code,

            'principal_amount' => Money::format(
                $this->principal_amount,
                $places
            ),

            'total_paid' => Money::format(
                $paid,
                $places
            ),

            'outstanding_balance' => Money::format(
                $outstanding,
                $places
            ),

            'start_date' => $this->start_date->format('Y-m-d'),

            'maturity_date' => $this->maturity_date->format('Y-m-d'),

            'status' => $this->status,

            'created_by' => $this->creator->name,
        ];
    }
}