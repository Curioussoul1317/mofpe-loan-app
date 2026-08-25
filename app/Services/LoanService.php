<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\Loan;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class LoanService
{
    public function __construct(private readonly LoanAuditService $auditService ) {
    }

    public function create(array $data, User $user): Loan
    {
        $currency = Currency::query()
            ->findOrFail($data['currency_id']);

        try {
            Money::ensurePositive(
                $data['principal_amount']
            );

            Money::validateScale(
                $data['principal_amount'],
                $currency->decimal_places
            );
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages([
                'principal_amount' => [
                    $exception->getMessage(),
                ],
            ]);
        }

        return DB::transaction(function () use (
            $data,
            $user
        ) {
            $loan = Loan::create([
                'loan_number' => $data['loan_number'],
                'customer_id' => $data['customer_id'],
                'currency_id' => $data['currency_id'],
                'principal_amount' =>
                    $data['principal_amount'],
                'start_date' => $data['start_date'],
                'maturity_date' =>
                    $data['maturity_date'],
                'status' => $data['status'],
                'created_by' => $user->id,
            ]);

            $this->auditService->record(
                loan: $loan,
                user: $user,
                action: 'created',
                newValues: [
                    'loan_number' =>
                        $loan->loan_number,
                    'customer_id' =>
                        $loan->customer_id,
                    'currency_id' =>
                        $loan->currency_id,
                    'principal_amount' =>
                        $loan->principal_amount,
                    'start_date' =>
                        $loan->start_date
                            ->format('Y-m-d'),
                    'maturity_date' =>
                        $loan->maturity_date
                            ->format('Y-m-d'),
                    'status' =>
                        $loan->status,
                ]
            );

            return $loan;
        });
    }
}