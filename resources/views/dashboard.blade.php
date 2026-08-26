@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<h1 class="mb-4">Portfolio Dashboard</h1>

<div class="row g-3">

    @foreach($currencies as $currency)

        <div class="col-md-6">

            <div class="card">
                <div class="card-body">

                    <h4>{{ $currency->code }}</h4>

                    <p>
                        Active Loans:
                        <strong>
                            {{ $currency->loans->count() }}
                        </strong>
                    </p>

                    <p>
                        Total Principal:
                        <strong>
                            {{ $currency->code }}
                            {{ $currency->total_principal }}
                        </strong>
                    </p>

                    <p class="mb-0">
                        Outstanding:
                        <strong>
                            {{ $currency->code }}
                            {{ $currency->total_outstanding }}
                        </strong>
                    </p>

                </div>
            </div>

        </div>

    @endforeach

</div>

@endsection