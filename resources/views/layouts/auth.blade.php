<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Login - NUH')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('head')

    <style>
        html,
        body {
            min-height: 100%;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #e7f2ff 0%, #ffffff 48%, #d8ebff 100%);
        }

        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
        }

        .auth-card {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 18px 46px rgba(21, 68, 132, 0.16);
            overflow: hidden;
        }

        .auth-card .card-body {
            padding: 48px;
        }

        .auth-logo {
            display: block;
            width: auto;
            max-width: 160px;
            height: 76px;
            object-fit: contain;
            margin: 0 auto 22px;
        }

        .auth-title {
            color: #1f2d3d;
            font-size: 1.75rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-link {
            color: #0d3b75;
            font-weight: 600;
            text-decoration: none;
        }

        .auth-link:hover {
            color: #154484;
            text-decoration: underline;
        }

        .btn-auth {
            background-color: #154484;
            border-color: #154484;
            font-weight: 700;
        }

        .btn-auth:hover,
        .btn-auth:focus {
            background-color: #0f3363;
            border-color: #0f3363;
        }

        .form-label {
            color: #444;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border-color: #a5c1d6;
            border-radius: 14px;
        }

        .form-control-lg,
        .form-select-lg {
            min-height: 52px;
            font-size: 1rem;
            padding: 12px 16px;
        }

        .auth-form {
            display: grid;
            gap: 20px;
        }

        @media (max-width: 575.98px) {
            .auth-card .card-body {
                padding: 32px 22px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <main class="auth-page">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="@yield('auth_col_class', 'col-12 col-sm-10 col-md-6 col-lg-5')">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    @stack('scripts')
</body>
</html>
