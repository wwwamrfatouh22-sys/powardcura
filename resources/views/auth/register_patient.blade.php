@extends('layouts.auth')

@section('title', 'Create Patient Account - NUH')
@section('auth_col_class', 'col-12 col-sm-11 col-md-8 col-lg-6')

@push('styles')
<style>
    .signup-intro {
        color: #5e7088;
        font-size: 1rem;
        line-height: 1.7;
        margin: -16px 0 28px;
        text-align: center;
    }

    .signup-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .signup-grid .full-width {
        grid-column: 1 / -1;
    }

    .field-hint {
        color: #738298;
        font-size: .86rem;
        margin-top: 7px;
    }

    .login-link-section {
        color: #526277;
        font-size: .96rem;
        text-align: center;
        margin-top: 24px;
    }

    @media (max-width: 767.98px) {
        .signup-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="card auth-card">
    <div class="card-body">
        <img src="{{ asset('images/image.png') }}" class="auth-logo" alt="NUH Logo">
        <h1 class="auth-title">Create Patient Account</h1>
        <p class="signup-intro">Patient accounts use your National ID for secure access to appointments, results, and profile details.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST" class="auth-form">
            @csrf

            <div class="signup-grid">
                <div class="full-width">
                    <label class="form-label" for="national_id">National ID number</label>
                    <input type="text"
                           id="national_id"
                           name="national_id"
                           value="{{ old('national_id') }}"
                           class="form-control form-control-lg @error('national_id') is-invalid @enderror"
                           placeholder="14-digit National ID"
                           required>
                    <div class="field-hint">14 numbers</div>
                </div>

                <div class="full-width">
                    <label class="form-label" for="full_name">Four-part name</label>
                    <input type="text"
                           id="full_name"
                           name="full_name"
                           value="{{ old('full_name') }}"
                           class="form-control form-control-lg @error('full_name') is-invalid @enderror"
                           placeholder="Enter your full name"
                           required>
                </div>

                <div>
                    <label class="form-label" for="dob">Date of birth</label>
                    <input type="date"
                           id="dob"
                           name="dob"
                           value="{{ old('dob') }}"
                           class="form-control form-control-lg @error('dob') is-invalid @enderror"
                           required>
                </div>

                <div>
                    <label class="form-label" for="gender">Gender</label>
                    <select id="gender"
                            name="gender"
                            class="form-select form-select-lg @error('gender') is-invalid @enderror"
                            required>
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <div class="full-width">
                    <label class="form-label" for="phone">Phone number</label>
                    <input type="text"
                           id="phone"
                           name="phone"
                           value="{{ old('phone') }}"
                           class="form-control form-control-lg @error('phone') is-invalid @enderror"
                           placeholder="01XXXXXXXXX"
                           required>
                    <div class="field-hint">11 numbers</div>
                </div>

                <div class="full-width">
                    <label class="form-label" for="password">Password</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control form-control-lg @error('password') is-invalid @enderror"
                           placeholder="At least 8 characters"
                           required>
                </div>
            </div>

            <button type="submit" class="btn btn-auth btn-lg btn-primary w-100 mt-2">Create Account</button>
        </form>

        <div class="login-link-section">
            Already have a patient account? <a href="{{ route('patient.login') }}" class="auth-link">Log in</a>
        </div>
    </div>
</div>
@endsection
