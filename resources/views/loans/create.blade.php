@extends('layouts.app')

@section('title', 'Create Loan')

@section('content')

<h1>Create Loan</h1>

<form method="POST"
      action="{{ route('loans.store') }}"
      class="card card-body">

    @csrf

     

    <div class="mb-3">
        <label class="form-label">Customer</label>

        <select name="customer_id"
                class="form-select"
                required>

            @foreach($customers as $customer)

                <option value="{{ $customer->id }}">
                    {{ $customer->name }}
                </option>

            @endforeach

        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Currency</label>

        <select name="currency_id"
                class="form-select"
                required>

            @foreach($currencies as $currency)

                <option value="{{ $currency->id }}">
                    {{ $currency->code }}
                </option>

            @endforeach

        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Principal</label>

        <input
            name="principal_amount"
            value="{{ old('principal_amount') }}"
            class="form-control"
            required
        >
    </div>

    <div class="mb-3">
        <label class="form-label">Start Date</label>

        <input
            type="date"
            name="start_date"
            value="{{ old('start_date') }}"
            class="form-control"
            required
        >
    </div>

    <div class="mb-3">
        <label class="form-label">Maturity Date</label>

        <input
            type="date"
            name="maturity_date"
            value="{{ old('maturity_date') }}"
            class="form-control"
            required
        >
    </div>

    <input type="hidden"
           name="status"
           value="active">

    <button class="btn btn-primary">
        Create Loan
    </button>

</form>

@endsection