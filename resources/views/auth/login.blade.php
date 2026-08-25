<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | Loan Portfolio Management System</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            margin: 0;
        }

        .container {
            max-width: 420px;
            margin: 100px auto;
            background: white;
            padding: 32px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        h1 {
            margin-top: 0;
            font-size: 24px;
        }

        .field {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            width: 100%;
            padding: 11px;
            border: 0;
            border-radius: 4px;
            cursor: pointer;
        }

        .error {
            color: #b91c1c;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Loan Portfolio Management System</h1>
    <p>Sign in to continue.</p>
    <form method="POST" action="{{ route('login.store') }}">
        @csrf

        <div class="field">
            <label for="email">
                Email
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
            >
            @error('email')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="field">
            <label for="password">
                Password
            </label>
            <input
                id="password"
                name="password"
                type="password"
                required
            >
            @error('password')
                <div class="error">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit">
            Login
        </button>

    </form>

</div>

</body>
</html>