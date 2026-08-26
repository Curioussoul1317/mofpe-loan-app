<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Loan Portfolio')</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand"
           href="{{ route('dashboard') }}">
            Loan Portfolio
        </a>

        <div class="d-flex gap-3 align-items-center">

            <a class="text-white text-decoration-none"
               href="{{ route('customers.index') }}">
                Customers
            </a>

            <a class="text-white text-decoration-none"
               href="{{ route('loans.index') }}">
                Loans
            </a>

            <form method="POST"
                  action="{{ route('logout') }}">
                @csrf

                <button class="btn btn-sm btn-outline-light">
                    Logout
                </button>
            </form>

        </div>
    </div>
</nav>

<main class="container py-4">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')

</main>

</body>
</html>