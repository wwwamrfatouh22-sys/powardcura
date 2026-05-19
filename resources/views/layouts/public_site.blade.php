<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('ui.brand.name'))</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ app()->isLocale('ar') ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0b3d91;
            --primary-dark: #062e73;
            --primary-soft: rgba(11, 61, 145, 0.12);
            --surface: rgba(255, 255, 255, 0.92);
            --surface-strong: rgba(255, 255, 255, 0.98);
            --text: #203047;
            --muted: #66768a;
            --border: rgba(130, 157, 196, 0.28);
            --shadow: 0 18px 40px rgba(27, 58, 100, 0.14);
            --shadow-hover: 0 24px 48px rgba(27, 58, 100, 0.2);
            --radius-xl: 30px;
            --radius-lg: 24px;
            --radius-md: 18px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--text);
            font-family: {{ app()->isLocale('ar') ? "'Cairo', Arial, Helvetica, sans-serif" : "'Inter', Arial, Helvetica, sans-serif" }};
            background:
                radial-gradient(circle at 10% 18%, rgba(117, 188, 255, 0.55), transparent 24%),
                radial-gradient(circle at 88% 16%, rgba(11, 61, 145, 0.12), transparent 16%),
                linear-gradient(90deg, #8ecbff 0%, #cfeaff 18%, #edf6ff 40%, #f8f9fb 66%, #f4f4f6 100%);
            background-attachment: fixed;
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }

        a {
            text-decoration: none;
        }

        .navbar {
            background: rgba(8, 58, 136, 0.94);
            backdrop-filter: blur(14px);
            padding: 12px 22px;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #fff !important;
            font-weight: 1000;
            font-size: 24px;
            letter-spacing: -0.4px;
        }

        .navbar-brand img {
            height: 70px;
            width: auto;
            object-fit: contain;
        }

        .navbar .container-fluid {
            gap: 14px;
        }

        .navbar-nav {
            gap: 6px;
        }

        .nav-shell {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            flex: 1;
        }

        .nav-link {
            color: #eef5ff !important;
            font-size: 14px;
            font-weight: 600;
            padding: 10px 14px !important;
            border-radius: 999px;
            transition: .25s ease;
            position: relative;
        }

        .nav-link::after {
            content: "";
            position: absolute;
            bottom: 7px;
            left: 14px;
            width: calc(100% - 28px);
            height: 2px;
            background: #7ec8ff;
            border-radius: 2px;
            transform: scaleX(0);
            transform-origin: center;
            transition: transform .25s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after,
        .show > .nav-link::after {
            transform: scaleX(1);
        }

        .nav-link:hover,
        .nav-link.active,
        .show > .nav-link {
            color: #fff !important;
            background: rgba(255, 255, 255, 0.1);
        }

        .dropdown-menu {
            border: none;
            border-radius: 16px;
            padding: 10px;
            box-shadow: 0 18px 45px rgba(26, 60, 110, 0.18);
        }

        .dropdown-item {
            border-radius: 12px;
            padding: 10px 14px;
            font-size: 14px;
            transition: .2s ease;
        }

        .dropdown-item:hover,
        .dropdown-item.active {
            background: #edf5ff;
            color: var(--primary-dark);
        }

        .navbar-toggler {
            border: 1px solid rgba(255, 255, 255, 0.26);
            border-radius: 14px;
            padding: 8px 10px;
            box-shadow: none !important;
        }

        .navbar-toggler-icon {
            width: 1.35em;
            height: 1.35em;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255,255,255,0.92%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2.2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .auth-links {
            gap: 8px;
            flex-wrap: wrap;
        }

        .lang-link {
            color: #dbe9ff !important;
            font-size: 13px;
            font-weight: 600;
        }

        .auth-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 78px;
            height: 34px;
            padding: 0 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            transition: .25s ease;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .auth-btn-signup {
            background: #ffffff;
            color: #0b4aa2 !important;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }

        .auth-btn-login {
            background: transparent;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.35);
        }

        .profile-toggle {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff !important;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
        }

        .page-shell {
            max-width: 1240px;
            margin: 0 auto;
            padding: 40px 16px 56px;
        }

        .surface-panel {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }

        .page-hero {
            border-radius: var(--radius-xl);
            padding: 34px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.78);
            color: var(--primary);
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .page-hero h1 {
            margin: 0 0 10px;
            font-size: clamp(2rem, 4vw, 3.2rem);
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .page-hero p {
            margin: 0;
            max-width: 720px;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-main,
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 999px;
            font-weight: 700;
            padding: 12px 20px;
            transition: .25s ease;
        }

        .btn-main {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 12px 24px rgba(11, 61, 145, 0.22);
        }

        .btn-main:hover {
            background: var(--primary-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.8);
            color: var(--primary-dark);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            color: var(--primary-dark);
            background: #fff;
        }

        .site-footer {
            background: #f5f8fd;
            border-top: 1px solid rgba(11, 61, 145, 0.08);
            padding: 28px 0 34px;
        }

        .container-xl-custom {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        .footer-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 14px;
        }

        .footer-links,
        .footer-contact {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .footer-links a,
        .footer-contact a,
        .footer-contact div {
            color: #4e6179;
            font-size: 14px;
        }

        .footer-links a:hover,
        .footer-contact a:hover {
            color: var(--primary-dark);
        }

        .footer-map {
            border-radius: 18px;
            overflow: hidden;
            min-height: 180px;
        }

        .footer-map iframe {
            width: 100%;
            height: 100%;
            min-height: 180px;
            border: 0;
        }

        @media (max-width: 991.98px) {
            .nav-shell {
                align-items: flex-start;
                flex-direction: column;
            }

            .navbar-collapse {
                width: 100%;
            }

            .page-hero {
                padding: 28px 24px;
                flex-direction: column;
                align-items: flex-start;
            }

            .footer-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .navbar {
                padding-inline: 14px;
            }

            .navbar-brand img {
                height: 58px;
            }

            .page-shell {
                padding-top: 28px;
            }

            .page-hero {
                padding: 22px 18px;
                border-radius: 24px;
            }
        }
    </style>
    @yield('head')
</head>
<body>
@php($publicSection = $publicSection ?? '')

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="nav-shell">
            <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
                <ul class="navbar-nav align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#booking-section">{{ __('ui.nav.about') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#departments-section">{{ __('ui.nav.departments') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#departments-section">{{ __('ui.nav.clinics') }}</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ in_array($publicSection, ['jobs', 'training'], true) ? 'active' : '' }}" href="#" id="jobsDropdown" role="button" data-bs-toggle="dropdown">
                            {{ __('ui.nav.jobs_training') }}
                        </a>
                        <ul class="dropdown-menu shadow">
                            <li><a class="dropdown-item {{ $publicSection === 'jobs' ? 'active' : '' }}" href="{{ route('staff.module.jobs') }}">{{ __('ui.nav.job_opportunities') }}</a></li>
                            <li><a class="dropdown-item {{ $publicSection === 'training' ? 'active' : '' }}" href="{{ route('staff.module.training') }}">{{ __('ui.nav.training_programs') }}</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="contactDropdown" role="button" data-bs-toggle="dropdown">
                            {{ __('ui.nav.contact') }}
                        </a>
                        <ul class="dropdown-menu shadow">
                            <li><a class="dropdown-item" href="{{ route('home') }}#location-section">{{ __('ui.nav.landline') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('complaints.create') }}">{{ __('ui.nav.complaints') }}</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="d-flex align-items-center auth-links">
                <a href="{{ route('locale.switch', app()->isLocale('ar') ? 'en' : 'ar') }}" class="lang-link">{{ __('ui.language.switch_to') }}</a>

                @guest('patient')
                    <a href="{{ route('register') }}" class="auth-btn auth-btn-signup">{{ __('ui.nav.signup') }}</a>
                    <a href="{{ route('login') }}" class="auth-btn auth-btn-login">{{ __('ui.nav.login') }}</a>
                @endguest

                @auth('patient')
                    <div class="nav-item dropdown ms-2">
                        <a class="profile-toggle dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-fill"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}">{{ __('ui.common.my_profile') }}</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item">{{ __('ui.common.logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

@if(session('success'))
    <div class="container-xl-custom pt-3">
        <div class="alert alert-success border-0 shadow-sm mb-0">{{ session('success') }}</div>
    </div>
@endif

<main class="page-shell">
    @yield('content')
</main>

<footer class="site-footer">
    <div class="container-xl-custom">
        <div class="footer-grid">
            <div>
                <div class="footer-title">{{ __('ui.home.related_sites') }}</div>
                <div class="footer-links">
                    <a href="#">Smart Village</a>
                    <a href="#">Faculty of Engineering - NUB</a>
                    <a href="#">Silicon Waha New Beni Suef Tech Park</a>
                </div>
            </div>
            <div>
                <div class="footer-title">{{ __('ui.home.access_map') }}</div>
                <div class="footer-map" id="location-section">
                    <iframe src="https://www.google.com/maps?q=New%20Beni%20Suef%20City&z=13&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            <div>
                <div class="footer-title">{{ __('ui.home.contact_information') }}</div>
                <div class="footer-contact">
                    <div>{{ __('ui.home.city') }}</div>
                    <a href="tel:+2001000004000"><i class="bi bi-telephone-fill"></i> +20 01000004000</a>
                    <a href="tel:0822222888"><i class="bi bi-telephone-fill"></i> 0822222888</a>
                    <a href="mailto:nuh90@gmail.com"><i class="bi bi-envelope-fill"></i> Mail: nuh90@gmail.com</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
