@extends('layouts.auth')

@section('title', 'Select Login Type - NUH')
@section('auth_col_class', 'col-12 col-md-10 col-lg-8')

@push('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@push('styles')
    <style>
        .login-type-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .login-type-card {
            display: block;
            height: 100%;
            padding: 24px 18px;
            color: #1f2d3d;
            text-align: center;
            text-decoration: none;
            border: 1px solid #e8edf4;
            border-radius: 14px;
            background: #fff;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .login-type-card:hover,
        .login-type-card:focus {
            color: #1f2d3d;
            border-color: #b9cfe6;
            box-shadow: 0 10px 24px rgba(21, 68, 132, 0.12);
            transform: translateY(-3px);
        }

        .login-type-icon {
            width: 54px;
            height: 54px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
            border-radius: 14px;
            background: #eaf1f8;
            color: #154484;
            font-size: 22px;
        }

        .login-type-card h2 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .login-type-card p {
            color: #667085;
            font-size: .9rem;
            margin: 0;
        }

        @media (max-width: 767.98px) {
            .login-type-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
<div class="card auth-card">
    <div class="card-body">
        <img src="{{ asset('images/image.png') }}" class="auth-logo" alt="NUH Logo">
        <h1 class="auth-title">Select Login Type</h1>

        <div class="login-type-grid">
            <a href="{{ route('patient.login') }}" class="login-type-card">
                <span class="login-type-icon"><i class="fa-regular fa-user"></i></span>
                <h2>Login as Patient</h2>
                <p>Access medical records and appointments</p>
            </a>

            <a href="{{ route('doctor.login') }}" class="login-type-card">
                <span class="login-type-icon"><i class="fa-solid fa-user-doctor"></i></span>
                <h2>Login as Doctor</h2>
                <p>Manage patients and medical procedures</p>
            </a>

            <a href="{{ route('staff.login') }}" class="login-type-card">
                <span class="login-type-icon"><i class="fa-solid fa-user-tie"></i></span>
                <h2>Login as Staff</h2>
                <p>Access the staff portal and resources</p>
            </a>

            <a href="{{ route('admin.login') }}" class="login-type-card">
                <span class="login-type-icon"><i class="fa-solid fa-shield-halved"></i></span>
                <h2>Login as Admin</h2>
                <p>Manage hospital operations and settings</p>
            </a>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-auth btn-sm btn-primary px-4">
                <i class="fa-solid fa-globe me-1"></i> Visit Our Website
            </a>
        </div>
    </div>
</div>
@endsection
