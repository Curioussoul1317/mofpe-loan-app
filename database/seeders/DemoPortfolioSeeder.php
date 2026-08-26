<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Loan;
use App\Models\LoanAudit;
use App\Models\Repayment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoPortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $officer = User::where(
            'email',
            'officer@example.test'
        )->firstOrFail();

        $usd = Currency::where('code', 'USD')->firstOrFail();
        $mvr = Currency::where('code', 'MVR')->firstOrFail();

        DB::transaction(function () use ($officer, $usd, $mvr) {

            for ($i = 1; $i <= 100; $i++) {

                $customer = Customer::firstOrCreate(
                    ['email' => "customer{$i}@example.test"],
                    [
                        'name' => "Demo Customer {$i}",
                        'phone' => '777'.str_pad($i, 4, '0', STR_PAD_LEFT),
                        'status' => 'active',
                    ]
                );

                $currency = $i % 2 === 0 ? $usd : $mvr;

                $loan = Loan::firstOrCreate(
                    ['loan_number' => sprintf('DEMO-LN-%06d', $i)],
                    [
                        'customer_id' => $customer->id,
                        'currency_id' => $currency->id,
                        'principal_amount' => '1000000.00',
                        'start_date' => '2026-01-01',
                        'maturity_date' => '2027-01-01',
                        'status' => 'active',
                        'created_by' => $officer->id,
                    ]
                );

                if ($loan->wasRecentlyCreated) {
                    LoanAudit::create([
                        'loan_id' => $loan->id,
                        'user_id' => $officer->id,
                        'action' => 'created',
                        'new_values' => [
                            'principal_amount' => '1000000.00',
                            'currency' => $currency->code,
                        ],
                    ]);
                }

                for ($j = 1; $j <= 100; $j++) {

                    $reference = sprintf(
                        'DEMO-PAY-%03d-%03d',
                        $i,
                        $j
                    );

                    $repayment = Repayment::firstOrCreate(
                        ['reference_number' => $reference],
                        [
                            'loan_id' => $loan->id,
                            'currency_id' => $currency->id,
                            'amount' => '5000.00',
                            'payment_date' => '2026-06-01',
                            'created_by' => $officer->id,
                        ]
                    );

                    if ($repayment->wasRecentlyCreated) {
                        LoanAudit::create([
                            'loan_id' => $loan->id,
                            'user_id' => $officer->id,
                            'action' => 'repayment_recorded',
                            'new_values' => [
                                'reference_number' => $reference,
                                'amount' => '5000.00',
                            ],
                        ]);
                    }
                }
            }
        });
    }
}