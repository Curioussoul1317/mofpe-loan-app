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
    public function __construct(
        private LoanAuditService $auditService
    ) {
    }

   public function create(array $data, User $user): Loan
    {
        $currency = Currency::findOrFail($data['currency_id']);

        try {
            Money::validate(
                $data['principal_amount'],
                $currency->decimal_places
            );
        } catch (InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'principal_amount' => [$e->getMessage()],
            ]);
        }

        return DB::transaction(function () use ($data, $user) {

            $loan = Loan::create([
                'customer_id' => $data['customer_id'],
                'currency_id' => $data['currency_id'],
                'principal_amount' => $data['principal_amount'],
                'start_date' => $data['start_date'],
                'maturity_date' => $data['maturity_date'],
                'status' => $data['status'],
                'created_by' => $user->id,
            ]);

            $loan->update([
                'loan_number' => 'LN-' . str_pad(
                    $loan->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            $this->auditService->record(
                $loan,
                $user,
                'created',
                null,
                [
                    'loan_number' => $loan->loan_number,
                    'principal_amount' => $loan->principal_amount,
                    'status' => $loan->status,
                ]
            );

            return $loan;
        });
    }

    public function update(
        Loan $loan,
        array $data,
        User $user
    ): Loan {
        return DB::transaction(function () use ($loan, $data, $user) {

            $oldStatus = $loan->status;

            $loan->update([
                'status' => $data['status'],
            ]);

            $this->auditService->record(
                $loan,
                $user,
                'updated',
                ['status' => $oldStatus],
                ['status' => $loan->status]
            );

            return $loan;
        });
    }
}