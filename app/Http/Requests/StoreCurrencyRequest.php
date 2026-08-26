<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => [
                'required',
                'string',
                'size:3',
                'unique:currencies,code',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'decimal_places' => [
                'required',
                'integer',
                'between:0,8',
            ],
        ];
    }
}