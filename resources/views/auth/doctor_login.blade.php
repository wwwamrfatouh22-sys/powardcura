<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - NUH</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(90deg, #94c1e9 0%, #ffffff 50%, #94c1e9 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: sans-serif;
        }

        .login-card {
            background: white;
            width: 100%;
            max-width: 750px;
            padding: 60px 80px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }

        .logo-box {
            text-align: center;
            margin-bottom: 50px;
        }

        .logo-box img {
            height: 70px;
        }

        .form-label {
            font-weight: 600;
            color: #444;
            margin-bottom: 10px;
        }

        .form-control {
            border: 1.5px solid #a5c1d6;
            border-radius: 12px;
            padding: 14px 15px;
            margin-bottom: 35px;
        }

        .btn-login {
            background-color: #154484;
            color: white;
            border-radius: 12px;
            padding: 14px;
            width: 100%;
            font-size: 18px;
            font-weight: bold;
            border: none;
            transition: 0.3s;
        }

        .btn-login:hover {
            background-color: #0f3363;
            transform: translateY(-2px);
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="login-card">

    <div class="logo-box">
        <img src="{{ asset('images/image.png') }}" alt="NUH Logo">
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
        <div class="error-message">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('doctor.login.submit') }}">
        @csrf

        <label class="form-label">Email</label>
        <input type="email"
               name="email"
               value="{{ old('email') }}"
               class="form-control"
               placeholder="Enter your email"
               required>

        <label class="form-label">Password</label>
        <input type="password"
               name="password"
               class="form-control"
               placeholder="Enter your password"
               required>

        <button type="submit" class="btn-login">
            Log in
        </button>
    </form>
    <div class="text-center mt-3">
        <span>Don't have an account?</span>
        <a href="{{ route('register') }}"
           style="color:#0d3b75; font-weight:600; text-decoration:none;">
            Create an account
        </a>
    </div>
</div>

</body>
</html>
