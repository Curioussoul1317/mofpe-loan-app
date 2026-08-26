<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRepaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
            ],

            'amount' => [
                'required',
                'string',
                'regex:/^\d+(\.\d+)?$/',
            ],

            'payment_date' => [
                'required',
                'date_format:Y-m-d',
            ],
 
        ];
    }

    public function messages(): array
    {
        return [
            'amount.regex' =>
                'The repayment amount must be a valid positive decimal number.',
        ];
    }
}