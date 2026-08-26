<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRepaymentRequest;
use App\Http\Resources\RepaymentResource;
use App\Models\Loan;
use App\Services\RepaymentService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RepaymentController extends Controller
{
    public function __construct(
        private readonly RepaymentService $repaymentService
    ) {
    }

    public function index(
        Loan $loan
    ): AnonymousResourceCollection {
        $repayments = $loan
            ->repayments()
            ->with([
                'currency',
                'creator',
            ])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(25);

        return RepaymentResource::collection(
            $repayments
        );
    }

    public function store(
        StoreRepaymentRequest $request,
        Loan $loan
    ): RepaymentResource {
        $repayment =
            $this->repaymentService->record(
                $loan,
                $request->validated(),
                $request->user()
            );

        $repayment->load([
            'currency',
            'creator',
        ]);

        return new RepaymentResource(
            $repayment
        );
    }
}