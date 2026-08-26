@extends('layouts.app')

@section('title', $customer->name)

@section('content')

<h1>{{ $customer->name }}</h1>

<p>{{ $customer->email }}</p>
<p>{{ $customer->phone }}</p>

<h3 class="mt-4">Loans</h3>

<table class="table">

    <thead>
    <tr>
        <th>Loan</th>
        <th>Currency</th>
        <th>Principal</th>
        <th>Status</th>
    </tr>
    </thead>

    <tbody>

    @forelse($customer->loans as $loan)

        <tr>
            <td>
                <a href="{{ route('loans.show', $loan) }}">
                    {{ $loan->loan_number }}
                </a>
            </td>

            <td>{{ $loan->currency->code }}</td>

            <td>
                {{ $loan->currency->code }}
                {{ \App\Support\Money::format(
                    $loan->principal_amount,
                    $loan->currency->decimal_places
                ) }}
            </td>

            <td>{{ ucfirst($loan->status) }}</td>
        </tr>

    @empty

        <tr>
            <td colspan="4">
                No loans found.
            </td>
        </tr>

    @endforelse

    </tbody>

</table>

@endsection