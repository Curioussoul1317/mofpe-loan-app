<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Support\Money;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $currencies = Currency::query()
            ->with([
                'loans' => function ($query) {
                    $query
                        ->where('status', 'active')
                        ->withSum('repayments', 'amount');
                },
            ])
            ->orderBy('code')
            ->get();

        $data = [];

        foreach ($currencies as $currency) {
            $principal = '0';
            $outstanding = '0';

            foreach ($currency->loans as $loan) {
                $paid = (string) ($loan->repayments_sum_amount ?? '0');

                $principal = Money::add(
                    $principal,
                    (string) $loan->principal_amount
                );

                $outstanding = Money::add(
                    $outstanding,
                    Money::subtract(
                        (string) $loan->principal_amount,
                        $paid
                    )
                );
            }

            $data[] = [
                'currency' => $currency->code,

                'active_loans' =>
                    $currency->loans->count(),

                'total_principal' => Money::format(
                    $principal,
                    $currency->decimal_places
                ),

                'total_outstanding' => Money::format(
                    $outstanding,
                    $currency->decimal_places
                ),
            ];
        }

        return response()->json([
            'data' => $data,
        ]);
    }
}