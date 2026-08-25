<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
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
            ->latest()
            ->paginate(15);

        return LoanResource::collection($loans);
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

        return new LoanResource($loan);
    }
}