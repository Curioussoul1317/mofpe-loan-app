@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<h1>Customers</h1>

<form method="GET" class="mb-3">
    <div class="input-group">

        <input
            name="q"
            value="{{ request('q') }}"
            class="form-control"
            placeholder="Search customers"
        >

        <button class="btn btn-primary">
            Search
        </button>

    </div>
</form>

<div class="card">
    <table class="table mb-0">

        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
        </tr>
        </thead>

        <tbody>

        @foreach($customers as $customer)
            <tr>

                <td>
                    <a href="{{ route('customers.show', $customer) }}">
                        {{ $customer->name }}
                    </a>
                </td>

                <td>{{ $customer->email }}</td>

                <td>{{ $customer->phone }}</td>

                <td>{{ ucfirst($customer->status) }}</td>

            </tr>
        @endforeach

        </tbody>

    </table>
</div>

<div class="mt-3">
    {{ $customers->withQueryString()->links() }}
</div>

@endsection