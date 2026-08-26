<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;

class CurrencyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Currency::orderBy('code')->get(),
        ]);
    }

    public function store(
        StoreCurrencyRequest $request
    ): JsonResponse {
        $data = $request->validated();

        $data['code'] = strtoupper($data['code']);

        $currency = Currency::create($data);

        return response()->json([
            'data' => $currency,
        ], 201);
    }
}