<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Support\Money;

class DashboardController extends Controller
{
    public function index()
    {
        $currencies = Currency::with([
            'loans' => function ($query) {
                $query
                    ->where('status', 'active')
                    ->withSum('repayments', 'amount');
            },
        ])->orderBy('code')->get();

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

            $currency->total_principal = Money::format(
                $principal,
                $currency->decimal_places
            );

            $currency->total_outstanding = Money::format(
                $outstanding,
                $currency->decimal_places
            );
        }

        return view('dashboard', compact('currencies'));
    }
}