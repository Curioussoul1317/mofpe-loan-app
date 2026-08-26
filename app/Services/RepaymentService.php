<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Repayment;
use App\Models\User;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class RepaymentService
{
    public function __construct(
        private LoanAuditService $auditService
    ) {
    }

    public function record(
        Loan $loan,
        array $data,
        User $user
    ): Repayment {
        return DB::transaction(function () use ($loan, $data, $user) {

            $loan = Loan::lockForUpdate()
                ->findOrFail($loan->id);

            $loan->load('currency');

            if (
                (int) $data['currency_id']
                !== (int) $loan->currency_id
            ) {
                throw ValidationException::withMessages([
                    'currency_id' => [
                        'Repayment currency must match loan currency.',
                    ],
                ]);
            }

            try {
                Money::validate(
                    $data['amount'],
                    $loan->currency->decimal_places
                );
            } catch (InvalidArgumentException $e) {
                throw ValidationException::withMessages([
                    'amount' => [$e->getMessage()],
                ]);
            }

            $totalPaid = (string) Repayment::where(
                'loan_id',
                $loan->id
            )->sum('amount');

            $outstanding = bcsub(
                $loan->principal_amount,
                $totalPaid,
                8
            );

            if (
                bccomp(
                    $data['amount'],
                    $outstanding,
                    8
                ) === 1
            ) {
                throw ValidationException::withMessages([
                    'amount' => [
                        'Repayment cannot exceed outstanding balance.',
                    ],
                ]);
            }

            $repayment = Repayment::create([
                'loan_id' => $loan->id,
                'currency_id' => $loan->currency_id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'created_by' => $user->id,
            ]);

            $repayment->update([
                'reference_number' => 'PAY-' . str_pad(
                    $repayment->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            $this->auditService->record(
                $loan,
                $user,
                'repayment_recorded',
                null,
                [
                    'repayment_id' => $repayment->id,
                    'reference_number' => $repayment->reference_number,
                    'amount' => $repayment->amount,
                ]
            );

            return $repayment;
        });
    }
}