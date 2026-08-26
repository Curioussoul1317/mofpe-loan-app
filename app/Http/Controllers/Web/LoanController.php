<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\StoreRepaymentRequest;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Loan;
use App\Services\LoanService;
use App\Services\RepaymentService;

class LoanController extends Controller
{
    public function __construct(
        private LoanService $loanService,
        private RepaymentService $repaymentService
    ) {
    }

    public function index()
    {
        $loans = Loan::with([
            'customer',
            'currency',
        ])
            ->withSum('repayments', 'amount')
            ->latest()
            ->paginate(15);

        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $customers = Customer::where('status', 'active')
            ->orderBy('name')
            ->get();

        $currencies = Currency::orderBy('code')->get();

        return view(
            'loans.create',
            compact('customers', 'currencies')
        );
    }

    public function store(StoreLoanRequest $request)
    {
        $loan = $this->loanService->create(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', 'Loan created successfully.');
    }

    public function show(Loan $loan)
    {
        $loan->load([
            'customer',
            'currency',
            'creator',
            'repayments.currency',
            'repayments.creator',
            'audits.user',
        ]);

        $loan->loadSum('repayments', 'amount');

        return view('loans.show', compact('loan'));
    }

    public function storeRepayment(
        StoreRepaymentRequest $request,
        Loan $loan
    ) {
        $this->repaymentService->record(
            $loan,
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', 'Repayment recorded successfully.');
    }
}