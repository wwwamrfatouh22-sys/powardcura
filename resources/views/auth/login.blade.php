@extends('layouts.auth')

@section('title', 'Patient Login - NUH')

@section('content')
<div class="card auth-card">
    <div class="card-body">
        <img src="{{ asset('images/image.png') }}" class="auth-logo" alt="NUH Logo">
        <h1 class="auth-title">Patient Login</h1>

    @if(session('success'))
        <div class="alert alert-success py-2">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('patient.login.submit') }}" class="auth-form">
        @csrf

        <div>
            <label class="form-label" for="national_id">National ID number</label>
            <input type="text"
                   id="national_id"
                   name="national_id"
                   value="{{ old('national_id') }}"
                   class="form-control form-control-lg"
                   placeholder="National ID number"
                   required>
        </div>

        <div>
            <label class="form-label" for="password">Password</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control form-control-lg"
                   placeholder="Password"
                   required>
        </div>

        <button type="submit" class="btn btn-auth btn-lg btn-primary w-100">
            Log in
        </button>
    </form>

    <div class="text-center mt-3">
        <span>Don't have an account?</span>
        <a href="{{ route('register') }}" class="auth-link">
            Create an account
        </a>
    </div>
    </div>
</div>
@endsection
