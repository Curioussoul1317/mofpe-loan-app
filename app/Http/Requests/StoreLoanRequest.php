<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [ 

            'customer_id' => [
                'required',
                'integer',
                'exists:customers,id',
            ],

            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
            ],

            'principal_amount' => [
                'required',
                'string',
                'regex:/^\d+(\.\d+)?$/',
            ],

            'start_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'maturity_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'active',
                    'closed',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'principal_amount.regex' =>
                'The principal amount must be a valid positive decimal number.',

            'maturity_date.after_or_equal' =>
                'The maturity date cannot be before the start date.',
        ];
    }
}