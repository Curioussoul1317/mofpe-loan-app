@extends('layouts.app')

@section('title', $loan->loan_number)

@section('content')

@php
    $places = $loan->currency->decimal_places;

    $principal = \App\Support\Money::format(
        $loan->principal_amount,
        $places
    );

    $paid = \App\Support\Money::format(
        (string) ($loan->repayments_sum_amount ?? '0'),
        $places
    );

    $outstanding = \App\Support\Money::format(
        \App\Support\Money::subtract(
            $loan->principal_amount,
            $paid
        ),
        $places
    );
@endphp

<h1>{{ $loan->loan_number }}</h1>

<div class="card mb-4">
    <div class="card-body">

        <p>
            Customer:
            <strong>{{ $loan->customer->name }}</strong>
        </p>

        <p>
            Principal:
            <strong>
                {{ $loan->currency->code }}
                {{ $principal }}
            </strong>
        </p>

        <p>
            Total Paid:
            <strong>
                {{ $loan->currency->code }}
                {{ $paid }}
            </strong>
        </p>

        <p>
            Outstanding:
            <strong>
                {{ $loan->currency->code }}
                {{ $outstanding }}
            </strong>
        </p>

        <p>
            Status:
            <strong>{{ ucfirst($loan->status) }}</strong>
        </p>

    </div>
</div>

<h3>Record Repayment</h3>

<form
    method="POST"
    action="{{ route('loans.repayments.store', $loan) }}"
    class="card card-body mb-4"
>

    @csrf

    <input
        type="hidden"
        name="currency_id"
        value="{{ $loan->currency_id }}"
    >

    <div class="mb-3">
        <label class="form-label">Amount</label>

        <input
            name="amount"
            class="form-control"
            required
        >
    </div>

    <div class="mb-3">
        <label class="form-label">Payment Date</label>

        <input
            type="date"
            name="payment_date"
            class="form-control"
            required
        >
    </div>
 

    <button class="btn btn-primary">
        Record Repayment
    </button>

</form>

<h3>Repayments</h3>

<table class="table">

    <thead>
    <tr>
        <th>Date</th>
        <th>Reference</th>
        <th>Amount</th>
        <th>Recorded By</th>
    </tr>
    </thead>

    <tbody>

    @forelse($loan->repayments as $repayment)

        <tr>
            <td>
                {{ $repayment->payment_date->format('Y-m-d') }}
            </td>

            <td>{{ $repayment->reference_number }}</td>

            <td>
                {{ $loan->currency->code }}
               {{ \App\Support\Money::format(
                    $repayment->amount,
                    $loan->currency->decimal_places
                ) }}
            </td>

            <td>{{ $repayment->creator->name }}</td>
        </tr>

    @empty

        <tr>
            <td colspan="4">
                No repayments recorded.
            </td>
        </tr>

    @endforelse

    </tbody>

</table>

<h3 class="mt-5">Audit History</h3>

<table class="table">

    <thead>
    <tr>
        <th>Date</th>
        <th>Action</th>
        <th>User</th>
    </tr>
    </thead>

    <tbody>

    @foreach($loan->audits as $audit)

        <tr>
            <td>{{ $audit->created_at }}</td>
            <td>{{ $audit->action }}</td>
            <td>{{ $audit->user->name }}</td>
        </tr>

    @endforeach

    </tbody>

</table>

@endsection