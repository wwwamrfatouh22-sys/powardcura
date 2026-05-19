@extends('layouts.auth')

@section('title', 'Staff Login - NUH')

@section('content')
<div class="card auth-card">
    <div class="card-body">
        <img src="{{ asset('images/image.png') }}" class="auth-logo" alt="NUH Logo">
        <h1 class="auth-title">Staff Login</h1>

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('staff.login.submit') }}" class="auth-form">
        @csrf

        <div>
            <label class="form-label" for="email">Email</label>
            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email') }}"
                   class="form-control form-control-lg"
                   placeholder="Enter your email"
                   required>
        </div>

        <div>
            <label class="form-label" for="password">Password</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control form-control-lg"
                   placeholder="Enter your password"
                   required>
        </div>

        <button type="submit" class="btn btn-auth btn-lg btn-primary w-100">
            Log in
        </button>
    </form>
    </div>
</div>
@endsection
