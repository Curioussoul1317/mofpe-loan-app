<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"  >

    <title>Dashboard</title>
</head>

<body>

<h1>Loan Portfolio Dashboard</h1>

<p>
    Logged in as:
    <strong>{{ auth()->user()->name }}</strong>
</p>

<p>
    Role:
    <strong>{{ auth()->user()->role }}</strong>
</p>

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">
        Logout
    </button>
</form>

</body>
</html>