@extends('layouts.app')

@section('title', 'Loans')

@section('content')

<div class="d-flex justify-content-between mb-3">

    <h1>Loans</h1>

    <a href="{{ route('loans.create') }}"
       class="btn btn-primary">
        Create Loan
    </a>

</div>

<div class="card">

<table class="table mb-0">

    <thead>
    <tr>
        <th>Loan</th>
        <th>Customer</th>
        <th>Currency</th>
        <th>Principal</th>
        <th>Paid</th>
        <th>Status</th>
    </tr>
    </thead>

    <tbody>

    @foreach($loans as $loan)

        <tr>

            <td>
                <a href="{{ route('loans.show', $loan) }}">
                    {{ $loan->loan_number }}
                </a>
            </td>

            <td>{{ $loan->customer->name }}</td>

            <td>{{ $loan->currency->code }}</td>

            <td>
                {{ $loan->currency->code }}
                {{ \App\Support\Money::format(
                    $loan->principal_amount,
                    $loan->currency->decimal_places
                ) }}
            </td>

            <td>
                {{ $loan->currency->code }}
                {{ \App\Support\Money::format(
                    (string) ($loan->repayments_sum_amount ?? '0'),
                    $loan->currency->decimal_places
                ) }}
            </td>

            <td>{{ ucfirst($loan->status) }}</td>

        </tr>

    @endforeach

    </tbody>

</table>

</div>

<div class="mt-3">
    {{ $loans->links() }}
</div>

@endsection