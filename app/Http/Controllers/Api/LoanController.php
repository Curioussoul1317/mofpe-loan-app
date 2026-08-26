<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Http\Resources\LoanAuditResource;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use App\Services\LoanService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LoanController extends Controller
{
    public function __construct(
        private readonly LoanService $loanService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $loans = Loan::query()
            ->with([
                'customer',
                'currency',
                'creator',
            ])
            ->withSum(
                'repayments',
                'amount'
            )
            ->latest()
            ->paginate(15);

        return LoanResource::collection(
            $loans
        );
    }

    public function store(
        StoreLoanRequest $request
    ): LoanResource {
        $loan = $this->loanService->create(
            $request->validated(),
            $request->user()
        );

        $loan->load([
            'customer',
            'currency',
            'creator',
        ]);

        $loan->loadSum(
            'repayments',
            'amount'
        );

        return new LoanResource($loan);
    }

    public function show(
        Loan $loan
    ): LoanResource {
        $loan->load([
            'customer',
            'currency',
            'creator',
        ]);

        $loan->loadSum(
            'repayments',
            'amount'
        );

        return new LoanResource($loan);
    }

        public function update(
        UpdateLoanRequest $request,
        Loan $loan
    ): LoanResource {
        $loan = $this->loanService->update(
            $loan,
            $request->validated(),
            $request->user()
        );

        $loan->load([
            'customer',
            'currency',
            'creator',
        ]);

        $loan->loadSum('repayments', 'amount');

        return new LoanResource($loan);
    }

    public function audit(Loan $loan)
        {
            $audits = $loan->audits()
                ->with('user')
                ->oldest()
                ->get();

            return LoanAuditResource::collection($audits);
        }
}