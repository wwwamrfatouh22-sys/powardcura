<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.brand.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="{{ app()->isLocale('ar') ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <style>
    :root {
        --primary: #0b3d91;
        --primary-dark: #062e73;
        --light-blue: #bfe0ff;
        --bg: #eef6ff;
        --card: #ffffff;
        --text: #1e2a3b;
        --muted: #6e7b8f;
        --danger: #e53935;
        --shadow: 0 10px 25px rgba(16, 51, 100, 0.12);
        --radius: 22px;
    }

    * {
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: {{ app()->isLocale('ar') ? "'Cairo', Arial, Helvetica, sans-serif" : "'Inter', Arial, Helvetica, sans-serif" }};
        color: var(--text);
        overflow-x: hidden;
        background: linear-gradient(90deg, #8ecbff 0%, #cfeaff 18%, #edf6ff 38%, #f8f9fb 62%, #f4f4f5 100%);
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
        animation: slideDown 0.6s ease both;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
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
        margin: 0;
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

    .nav-link:hover::after {
        transform: scaleX(1);
    }

    .nav-link:hover {
        color: #fff !important;
        transform: translateY(-1px);
        background: rgba(255, 255, 255, 0.08);
    }

    .nav-link.active,
    .show > .nav-link {
        background: rgba(255, 255, 255, 0.12);
        color: #fff !important;
    }

    .nav-link.active::after,
    .show > .nav-link::after {
        transform: scaleX(1);
    }

    .dropdown-menu {
        border: none;
        border-radius: 16px;
        padding: 10px;
        box-shadow: 0 18px 45px rgba(26, 60, 110, 0.18);
        animation: fadeInDown 0.2s ease both;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-item {
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 14px;
        transition: .2s;
    }

    .dropdown-item:hover {
        background: #edf5ff;
        transform: translateX(4px);
    }

    .navbar-toggler {
        border: 1px solid rgba(255, 255, 255, 0.26);
        border-radius: 14px;
        padding: 8px 10px;
        box-shadow: none !important;
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 4px rgba(126, 200, 255, 0.18) !important;
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
        margin-right: 6px;
    }

    html[dir="rtl"] .lang-link {
        margin-right: 0;
        margin-left: 6px;
    }

    html[dir="rtl"] .dropdown-item:hover {
        transform: translateX(-4px);
    }

    html[dir="rtl"] .department-footer i,
    html[dir="rtl"] .dept-arrow i,
    html[dir="rtl"] .bi-arrow-right,
    html[dir="rtl"] .bi-chevron-right,
    html[dir="rtl"] .bi-chevron-left {
        transform: scaleX(-1);
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

    .auth-btn-signup:hover {
        background: #f3f8ff;
        transform: translateY(-1px);
    }

    .auth-btn-login {
        background: transparent;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.35);
    }

    .auth-btn-login:hover {
        background: rgba(255, 255, 255, 0.12);
        transform: translateY(-1px);
    }

    .profile-toggle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.14);
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
        margin-left: 8px;
    }

    html[dir="rtl"] .profile-toggle {
        margin-left: 0;
        margin-right: 8px;
    }

    .profile-toggle i {
        font-size: 19px;
    }

    .hero {
        min-height: 720px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background:
            linear-gradient(rgba(255, 255, 255, 0.16), rgba(255, 255, 255, 0.16)),
            url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=1600&auto=format&fit=crop') center/cover no-repeat;
        overflow: hidden;
    }

    .hero::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent 30%, rgba(255, 255, 255, 0.08) 50%, transparent 70%);
        background-size: 200% 200%;
        animation: shimmer 4s ease-in-out infinite;
        z-index: 1;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 50%;
        }

        100% {
            background-position: -200% 50%;
        }
    }

    .hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.12) 40%, rgba(255, 255, 255, 0.38));
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 900px;
        padding: 40px 20px;
        animation: heroFadeUp 1s ease 0.3s both;
    }

    @keyframes heroFadeUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero h1 {
        font-size: clamp(38px, 5vw, 68px);
        font-weight: 800;
        color: #0f3d8a;
        margin-bottom: 12px;
        letter-spacing: -1px;
    }

    .hero p {
        font-size: clamp(16px, 2vw, 24px);
        color: #35588e;
        margin-bottom: 28px;
    }

    .btn-main {
        background: #0f4ba5;
        color: #fff;
        padding: 14px 36px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 700;
        box-shadow: 0 8px 20px rgba(15, 75, 165, 0.22);
        display: inline-block;
        transition: .3s ease;
        min-width: 220px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .btn-main::before {
        content: "";
        position: absolute;
        top: 0;
        left: -75%;
        width: 50%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transform: skewX(-20deg);
        transition: left 0.5s ease;
    }

    .btn-main:hover::before {
        left: 130%;
    }

    .btn-main:hover {
        background: #0b3f8b;
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 14px 28px rgba(15, 75, 165, 0.32);
    }

    .main-section {
        position: relative;
        z-index: 3;
        margin-top: -30px;
    }

    .container-xl-custom {
        width: min(1240px, calc(100% - 32px));
        margin: auto;
    }

    .emergency-top {
        text-align: center;
        margin-bottom: 26px;
    }

    .emergency-top .small-title {
        color: #d72121;
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 8px;
    }

    .emergency-top .sub {
        color: #5e6b80;
        font-size: 13px;
        margin-bottom: 16px;
    }

    .hotline-wrap {
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .pill-btn {
        border-radius: 999px;
        padding: 11px 28px;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: .25s ease;
    }

    .pill-outline {
        border: 2px solid #ef5a5a;
        color: #ef5a5a;
        background: #fff;
    }

    .pill-outline:hover {
        background: #fff0f0;
        transform: translateY(-2px);
    }

    .pill-danger {
        background: #e91d24;
        color: #fff;
        box-shadow: 0 8px 18px rgba(233, 29, 36, .22);
        position: relative;
    }

    .pill-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(233, 29, 36, .32);
    }

    .pill-danger::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 999px;
        border: 2px solid #e91d24;
        animation: pulseRing 2s ease-out infinite;
    }

    @keyframes pulseRing {
        0% {
            transform: scale(1);
            opacity: .7;
        }

        100% {
            transform: scale(1.4);
            opacity: 0;
        }
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1.35fr .95fr;
        gap: 24px;
        align-items: stretch;
        margin-top: 28px;
    }

    .card-box {
        background: rgba(255, 255, 255, 0.95);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        padding: 28px;
        transition: .3s ease;
    }

    .card-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 40px rgba(16, 51, 100, 0.18);
    }

    .contact-big {
        min-height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .contact-icon {
        font-size: 30px;
        color: #0b4fb3;
        margin-bottom: 18px;
        animation: floatIcon 3s ease-in-out infinite;
    }

    @keyframes floatIcon {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-6px);
        }
    }

    .contact-item {
        height: 58px;
        border: 1px solid #d7e5f5;
        background: #fbfdff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        padding: 0 18px;
        margin-bottom: 16px;
        color: #4a5870;
        font-size: 15px;
        transition: .25s ease;
    }

    .contact-item:hover {
        border-color: #7ec8ff;
        background: #f0f8ff;
        transform: translateX(4px);
    }

    .contact-item:last-child {
        margin-bottom: 0;
    }

    .contact-item .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #5a8df0;
        margin-right: 12px;
        flex-shrink: 0;
        animation: dotBlink 2s ease-in-out infinite;
    }

    @keyframes dotBlink {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: .5;
            transform: scale(.7);
        }
    }

    .side-cards {
        display: grid;
        grid-template-rows: 1fr 1fr;
        gap: 20px;
    }

    .mini-card {
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .mini-card .icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 14px;
    }

    .mini-card.mail .icon {
        background: #edf4ff;
        color: #0f59c4;
    }

    .mini-card.blue {
        background: #114a98;
        color: #fff;
    }

    .mini-card.blue .icon {
        background: rgba(255, 255, 255, .12);
        color: #fff;
    }

    .mini-card h6 {
        margin: 0 0 10px;
        font-size: 16px;
        font-weight: 700;
    }

    .mini-card p {
        margin: 0;
        font-size: 13px;
        line-height: 1.7;
        color: inherit;
        opacity: .94;
    }

    .active-link {
        font-size: 12px;
        margin-top: 14px;
        display: inline-block;
        color: inherit;
        opacity: .9;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin: 26px 0 70px;
    }

    .stat-chip {
        background: #fff;
        border-radius: 16px;
        box-shadow: var(--shadow);
        padding: 18px 14px;
        text-align: center;
        transition: .3s ease;
        cursor: default;
    }

    .stat-chip:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 18px 36px rgba(16, 51, 100, 0.2);
    }

    .stat-chip .big {
        font-size: 30px;
        color: #1a56b3;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 8px;
    }

    .stat-chip .small {
        font-size: 12px;
        color: #6c7a91;
    }

    .smart-care {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 30px;
        align-items: center;
        margin-bottom: 80px;
    }

    .section-tag {
        display: inline-block;
        background: #1a56b3;
        color: #fff;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        margin-bottom: 18px;
        box-shadow: 0 8px 18px rgba(26, 86, 179, .18);
        animation: tagPulse 3s ease-in-out infinite;
    }

    @keyframes tagPulse {

        0%,
        100% {
            box-shadow: 0 8px 18px rgba(26, 86, 179, .18);
        }

        50% {
            box-shadow: 0 8px 28px rgba(26, 86, 179, .38);
        }
    }

    .smart-care h2 {
        font-size: 56px;
        line-height: 1.08;
        margin: 0 0 18px;
        font-weight: 300;
    }

    .smart-care h2 span {
        color: #1c57b0;
        font-weight: 700;
    }

    .smart-care p {
        color: #6d7d92;
        line-height: 1.8;
        max-width: 470px;
        margin-bottom: 26px;
    }

    .gallery-wrap {
        position: relative;
        min-height: 520px;
    }

    .gallery-card {
        position: absolute;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: var(--shadow);
        background: #fff;
        transition: .4s ease;
    }

    .gallery-card:hover {
        transform: scale(1.04) rotate(-1deg);
        box-shadow: 0 22px 45px rgba(16, 51, 100, 0.22);
        z-index: 10;
    }

    .gallery-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .gallery-lg {
        width: 290px;
        height: 330px;
        top: 0;
        left: 40px;
        animation: floatCard 5s ease-in-out infinite;
    }

    .gallery-md {
        width: 180px;
        height: 220px;
        top: 30px;
        right: 20px;
        animation: floatCard 5s ease-in-out infinite 1.2s;
    }

    .gallery-sm {
        width: 210px;
        height: 150px;
        bottom: 30px;
        right: 60px;
        animation: floatCard 5s ease-in-out infinite 2.4s;
    }

    @keyframes floatCard {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .departments-section {
        margin-bottom: 90px;
    }

    .departments-header {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        margin-bottom: 34px;
    }

    .departments-title {
        font-size: 44px;
        text-align: center;
        font-weight: 500;
        margin: 0;
    }

    .departments-controls {
        position: absolute;
        right: 0;
        display: flex;
        gap: 14px;
    }

    .dept-arrow {
        width: 64px;
        height: 64px;
        border: none;
        border-radius: 50%;
        background: #0b4aa2;
        color: #fff;
        font-size: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 24px rgba(11, 74, 162, 0.22);
        transition: 0.25s ease;
    }

    .dept-arrow:hover {
        background: #083c84;
        transform: translateY(-2px) scale(1.08);
    }

    .dept-arrow:active {
        transform: scale(0.95);
    }

    .departments-slider-wrap {
        overflow: hidden;
        position: relative;
    }

    .departments-slider {
        display: flex;
        gap: 22px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 10px 6px 20px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .departments-slider::-webkit-scrollbar {
        display: none;
    }

    .department-card {
        min-width: 340px;
        max-width: 340px;
        background: #fff;
        border-radius: 28px;
        overflow: hidden;
        box-shadow: 0 14px 32px rgba(41, 73, 122, 0.16);
        flex-shrink: 0;
        transition: 0.3s ease;
    }

    .department-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 24px 48px rgba(41, 73, 122, 0.24);
    }

    .department-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        display: block;
        transition: .4s ease;
    }

    .department-card:hover img {
        transform: scale(1.06);
    }

    .department-image-wrap {
        position: relative;
        overflow: hidden;
    }

    .department-overlay {
        position: absolute;
        inset: auto 16px 16px 16px;
        display: flex;
        justify-content: flex-start;
        pointer-events: none;
    }

    .department-overlay span {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(8, 58, 136, 0.82);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .02em;
        backdrop-filter: blur(8px);
    }

    .department-body {
        padding: 20px 22px 24px;
    }

    .department-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 17px;
        color: #334155;
        margin-bottom: 18px;
        font-weight: 500;
    }

    .department-meta i {
        color: #2b6ed2;
        font-size: 23px;
        transition: .25s ease;
    }

    .department-card:hover .department-meta i {
        transform: scale(1.2) rotate(-5deg);
    }

    .department-sub {
        color: #6d7d92;
        font-size: 14px;
        margin-bottom: 18px;
    }

    .department-footer a {
        color: #2f73d7;
        font-size: 16px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: .25s ease;
    }

    .department-footer a:hover {
        color: #1d56aa;
        gap: 16px;
    }

    .diagnostic-services {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 24px;
        margin: -42px 0 90px;
        scroll-margin-top: 110px;
    }

    .diagnostic-service {
        min-height: 330px;
        border-radius: 28px;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: flex-end;
        box-shadow: 0 14px 32px rgba(41, 73, 122, 0.16);
        background: #0b4aa2;
    }

    .diagnostic-service img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: .4s ease;
    }

    .diagnostic-service:hover img {
        transform: scale(1.05);
    }

    .diagnostic-service::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(8, 28, 68, 0.06) 0%, rgba(8, 36, 82, 0.78) 100%);
    }

    .diagnostic-content {
        position: relative;
        z-index: 1;
        padding: 28px;
        color: #fff;
    }

    .diagnostic-content span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 14px;
        backdrop-filter: blur(8px);
    }

    .diagnostic-content h3 {
        font-size: 30px;
        font-weight: 700;
        margin: 0 0 10px;
    }

    .diagnostic-content p {
        margin: 0;
        color: rgba(255, 255, 255, 0.88);
        line-height: 1.7;
        max-width: 520px;
    }

    .overview {
        display: grid;
        grid-template-columns: 1fr 1.15fr;
        gap: 34px;
        align-items: center;
        margin-bottom: 100px;
    }

    .overview-text {
        padding-left: 20px;
    }

    .overview h2 {
        font-size: 64px;
        line-height: 1.02;
        margin: 0;
        font-weight: 300;
        letter-spacing: -1px;
    }

    .overview h2 span {
        display: block;
        color: #1d58b1;
        font-weight: 700;
        margin-top: 6px;
    }

    .overview-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
        align-items: start;
    }

    .overview-column {
        display: flex;
        flex-direction: column;
        gap: 22px;
    }

    .overview-column.top-shift {
        transform: translateY(-26px);
    }

    .overview-column.bottom-shift {
        transform: translateY(26px);
    }

    .overview-card {
        background: rgba(255, 255, 255, 0.97);
        border-radius: 24px;
        box-shadow: 0 14px 34px rgba(45, 72, 120, 0.12);
        padding: 26px 22px;
        min-height: 132px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border: 1px solid rgba(210, 226, 246, 0.9);
        position: relative;
        overflow: hidden;
        transition: .3s ease;
    }

    .overview-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 22px 44px rgba(45, 72, 120, 0.2);
    }

    .overview-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 18px;
        width: 3px;
        height: 34px;
        border-radius: 10px;
        background: linear-gradient(to bottom, #8fd2ff, #1d58b1);
    }

    .overview-icon {
        font-size: 18px;
        margin-bottom: 10px;
        color: #55b37a;
    }

    .overview-card.blue-icon .overview-icon {
        color: #1d58b1;
    }

    .overview-card.purple-icon .overview-icon {
        color: #6c63ff;
    }

    .overview-card.teal-icon .overview-icon {
        color: #0bb7a7;
    }

    .overview-card.sky-icon .overview-icon {
        color: #3aa3ff;
    }

    .overview-card .num {
        font-size: 38px;
        font-weight: 800;
        color: #134ea2;
        margin-bottom: 6px;
        line-height: 1;
        display: inline-block;
    }

    .overview-card .label {
        color: #7c899d;
        font-size: 13px;
        font-weight: 500;
    }

    .section-title {
        font-size: 38px;
        text-align: center;
        font-weight: 500;
        margin-bottom: 34px;
    }

    .news-section {
        margin-bottom: 80px;
    }

    .news-list {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
    }

    .news-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: var(--shadow);
        transition: .3s ease;
    }

    .news-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 22px 44px rgba(16, 51, 100, 0.2);
    }

    .news-card img {
        width: 100%;
        height: 190px;
        object-fit: cover;
        display: block;
        transition: .4s ease;
    }

    .news-card:hover img {
        transform: scale(1.07);
    }

    .news-body {
        padding: 16px;
    }

    .news-date {
        font-size: 11px;
        color: #8693a9;
        margin-bottom: 8px;
    }

    .news-title {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #263448;
    }

    .news-text {
        font-size: 13px;
        color: #6d7d92;
        line-height: 1.7;
        margin: 0;
    }

    .site-footer {
        background: #083a88;
        color: #fff;
        padding: 55px 0;
    }

    .footer-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr 1fr;
        gap: 28px;
        align-items: start;
    }

    .footer-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 18px;
    }

    .footer-links a,
    .footer-contact a,
    .footer-contact div {
        display: block;
        color: #dce9ff;
        margin-bottom: 12px;
        font-size: 14px;
        transition: .2s ease;
    }

    .footer-links a:hover,
    .footer-contact a:hover {
        color: #fff;
        transform: translateX(4px);
    }

    .footer-map {
        background: #fff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
    }

    .footer-map iframe {
        width: 100%;
        height: 220px;
        border: 0;
        display: block;
    }

    .reveal {
        opacity: 0;
        transform: translateY(32px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }

    .reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }

    .reveal-left {
        opacity: 0;
        transform: translateX(-32px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }

    .reveal-left.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .reveal-right {
        opacity: 0;
        transform: translateX(32px);
        transition: opacity 0.7s ease, transform 0.7s ease;
    }

    .reveal-right.visible {
        opacity: 1;
        transform: translateX(0);
    }

    .stagger-children>* {
        opacity: 0;
        transform: translateY(24px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .stagger-children.visible>*:nth-child(1) {
        opacity: 1;
        transform: translateY(0);
        transition-delay: .05s;
    }

    .stagger-children.visible>*:nth-child(2) {
        opacity: 1;
        transform: translateY(0);
        transition-delay: .15s;
    }

    .stagger-children.visible>*:nth-child(3) {
        opacity: 1;
        transform: translateY(0);
        transition-delay: .25s;
    }

    .stagger-children.visible>*:nth-child(4) {
        opacity: 1;
        transform: translateY(0);
        transition-delay: .35s;
    }

    /* =========================
       AI ROBOT
    ========================= */

    .ai-assistant-wrap {
        position: fixed;
        right: 22px;
        bottom: 18px;
        z-index: 9999;
    }

    .ai-assistant-wrap:hover .ai-label {
        opacity: 1;
        transform: translateY(0);
    }

    .ai-robot-btn {
        position: relative;
        display: block;
        width: 300px;
        height: auto;
        background: transparent;
        border: none;
        box-shadow: none;
        padding: 0;
        cursor: pointer;
        opacity: 0;
        transform: translateY(40px) scale(0.85);
        animation: robotEntrance 0.9s ease-out 0.8s forwards;
    }

    .robot-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        background: transparent;
        filter: drop-shadow(0 0 18px rgba(0, 200, 255, 0.35));
        animation: robotFloat 3s ease-in-out 1.8s infinite;
        transform-origin: bottom center;
    }

    .ai-robot-btn:hover .robot-img {
        animation:
            robotFloat 3s ease-in-out infinite,
            robotWave 0.8s ease-in-out 1;
    }

    @keyframes robotEntrance {
        0% {
            opacity: 0;
            transform: translateY(40px) scale(0.85);
        }

        60% {
            opacity: 1;
            transform: translateY(-8px) scale(1.04);
        }

        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes robotFloat {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes robotWave {
        0% {
            transform: rotate(0deg);
        }

        20% {
            transform: rotate(-4deg);
        }

        40% {
            transform: rotate(4deg);
        }

        60% {
            transform: rotate(-3deg);
        }

        80% {
            transform: rotate(3deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }

    #page-loader {
        position: fixed;
        inset: 0;
        background: #083a88;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 20px;
        transition: opacity .5s ease, visibility .5s ease;
    }

    #page-loader.hidden {
        opacity: 0;
        visibility: hidden;
    }

    .loader-cross {
        width: 48px;
        height: 48px;
        position: relative;
        animation: loaderSpin 1.2s ease-in-out infinite;
    }

    .loader-cross::before,
    .loader-cross::after {
        content: "";
        position: absolute;
        background: #fff;
        border-radius: 4px;
    }

    .loader-cross::before {
        width: 8px;
        height: 48px;
        left: 20px;
        top: 0;
    }

    .loader-cross::after {
        width: 48px;
        height: 8px;
        left: 0;
        top: 20px;
    }

    .ai-label {
        position: absolute;
        bottom: 180px;
        right: 10px;

        background: #0b3d91;
        color: #fff;

        padding: 10px 14px;
        border-radius: 20px;

        font-size: 13px;
        font-weight: 600;

        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);

        opacity: 0;
        transform: translateY(10px);

        transition: 0.4s ease;
    }

    @keyframes loaderSpin {

        0%,
        100% {
            transform: rotate(0deg) scale(1);
        }

        50% {
            transform: rotate(90deg) scale(1.15);
        }
    }

    .loader-text {
        color: #bfe0ff;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 2px;
        animation: loaderFade 1.2s ease-in-out infinite;
    }

    @keyframes loaderFade {

        0%,
        100% {
            opacity: .4;
        }

        50% {
            opacity: 1;
        }
    }

    .count-num {
        transition: all .1s ease;
    }

    .alert-success {
        border-left: 4px solid #28a745;
        border-radius: 12px;
        animation: slideInAlert .5s ease both;
    }

    @keyframes slideInAlert {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 991px) {
        .nav-shell {
            width: 100%;
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .navbar-collapse {
            margin-top: 14px;
            padding: 16px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .navbar-nav {
            align-items: stretch !important;
        }

        .nav-link {
            width: 100%;
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }

        .auth-links {
            width: 100%;
            justify-content: flex-start;
            padding-top: 6px;
        }

        .contact-grid {
            grid-template-columns: 1fr;
        }

        .smart-care {
            grid-template-columns: 1fr;
        }

        .overview {
            grid-template-columns: 1fr;
        }

        .diagnostic-services {
            grid-template-columns: 1fr;
            margin-top: -36px;
        }

        .overview-grid {
            grid-template-columns: 1fr;
        }

        .overview-column.top-shift,
        .overview-column.bottom-shift {
            transform: none;
        }

        .news-list {
            grid-template-columns: repeat(2, 1fr);
        }

        .footer-grid {
            grid-template-columns: 1fr;
        }

        .departments-controls {
            position: static;
            justify-content: center;
            margin-top: 20px;
        }

        .departments-header {
            flex-direction: column;
        }
    }

    .ai-bubble {
        position: absolute;
        bottom: 190px;
        right: 20px;

        background: #ffffff;
        color: #1e2a3b;

        padding: 12px 16px;
        border-radius: 18px;

        font-size: 14px;
        font-weight: 500;

        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);

        opacity: 0;
        transform: translateY(20px) scale(0.9);

        transition: 0.4s ease;

        max-width: 200px;
    }

    /* السهم بتاع الفقاعة */
    .ai-bubble::after {
        content: "";
        position: absolute;
        bottom: -8px;
        right: 20px;

        width: 14px;
        height: 14px;

        background: #ffffff;
        transform: rotate(45deg);
    }

    /* لما تظهر */
    .ai-bubble.show {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .ai-assistant-wrap:hover .ai-bubble {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    @media (max-width: 767px) {
        .navbar {
            padding: 10px 14px;
        }

        .navbar-brand img {
            height: 56px;
        }

        .ai-assistant-wrap {
            right: 10px;
            bottom: 10px;
        }

        .ai-robot-btn {
            width: 120px;
            height: 120px;
        }

        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .news-list {
            grid-template-columns: 1fr;
        }

        .hero {
            min-height: 620px;
        }

        .departments-title {
            font-size: 34px;
        }

        .diagnostic-service {
            min-height: 280px;
        }

        .section-title {
            font-size: 30px;
        }

        .overview h2 {
            font-size: 44px;
        }

        .smart-care h2 {
            font-size: 42px;
        }
    }
    </style>
</head>

<body>

    <div id="page-loader">
        <div class="loader-cross"></div>
        <div class="loader-text">{{ strtoupper(__('ui.brand.name')) }}</div>
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">

            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/nuh-logo.png') }}" alt="Logo">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="nav-shell">
                <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
                    <ul class="navbar-nav align-items-lg-center">
                        <li class="nav-item"><a class="nav-link" href="#booking-section">{{ __('ui.nav.about') }}</a></li>
                        <li class="nav-item">
                            <a href="#departments-section" class="nav-link active">{{ __('ui.nav.departments') }}</a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('radiology_results.index') }}">{{ __('ui.nav.radiology') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('laboratory_results.index') }}">{{ __('ui.nav.laboratory') }}</a></li>
                        <li class="nav-item"><a class="nav-link" href="#departments-section">{{ __('ui.nav.clinics') }}</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="jobsDropdown" role="button"
                                data-bs-toggle="dropdown">{{ __('ui.nav.jobs_training') }}</a>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="{{ route('staff.module.jobs') }}">{{ __('ui.nav.job_opportunities') }}</a>
                                </li>
                                <li><a class="dropdown-item" href="{{ route('staff.module.training') }}">{{ __('ui.nav.training_programs') }}</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="contactDropdown" role="button"
                                data-bs-toggle="dropdown">{{ __('ui.nav.contact') }}</a>
                            <ul class="dropdown-menu shadow">
                                <li><a class="dropdown-item" href="#location-section">{{ __('ui.nav.landline') }}</a></li>
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
    <div class="alert alert-success text-center m-3">{{ session('success') }}</div>
    @endif

    <section class="hero" id="booking-section">
        <div class="hero-content">
            <h1>{{ __('ui.brand.name') }}</h1>
            <p>{{ __('ui.home.tagline') }}</p>
            <a href="#departments-section" class="btn-main">{{ __('ui.common.book_now') }}</a>
        </div>
    </section>

    <div class="main-section">
        <div class="container-xl-custom">

            <section class="py-5 reveal" id="emergency-section">
                <div class="emergency-top">
                    <div class="small-title">{{ __('ui.home.emergency_title') }}</div>
                    <div class="sub">{{ __('ui.home.emergency_sub') }}</div>
                    <div class="hotline-wrap">
                        <div class="pill-btn pill-outline">{{ __('ui.home.call_now') }}: 01000004000</div>
                        <div class="pill-btn pill-danger">
                            <i class="bi bi-telephone-fill"></i>
                            {{ __('ui.home.hotline') }} 0822222888
                        </div>
                    </div>
                </div>

                <div class="contact-grid">
                    <div class="card-box contact-big">
                        <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div class="contact-item"><span class="dot"></span><span>+20 01000004000</span></div>
                        <div class="contact-item"><span class="dot"></span><span>0822222888</span></div>
                    </div>

                    <div class="side-cards">
                        <div class="card-box mini-card mail">
                            <div class="icon"><i class="bi bi-envelope-fill"></i></div>
                            <h6>nuh90@gmail.com</h6>
                        </div>
                        <div class="card-box mini-card blue">
                            <div class="icon"><i class="bi bi-clock-fill"></i></div>
                            <h6>{{ __('ui.home.service_24') }}</h6>
                            <p>{{ __('ui.home.service_24_desc') }}</p>
                            <span class="active-link">• {{ __('ui.home.active_now') }}</span>
                        </div>
                    </div>
                </div>

                <div class="stats-row stagger-children" id="statsRow">
                    <div class="stat-chip">
                        <div class="big">24/7</div>
                        <div class="small">{{ __('ui.home.continuous_support') }}</div>
                    </div>
                    <div class="stat-chip">
                        <div class="big">&lt; 2 min</div>
                        <div class="small">{{ __('ui.home.response_time') }}</div>
                    </div>
                    <div class="stat-chip">
                        <div class="big count-num" data-target="100" data-suffix="%">0%</div>
                        <div class="small">{{ __('ui.home.patient_satisfaction') }}</div>
                    </div>
                    <div class="stat-chip">
                        <div class="big">∞</div>
                        <div class="small">{{ __('ui.home.service_quality') }}</div>
                    </div>
                </div>
            </section>

            <section class="smart-care">
                <div class="reveal-left">
                    <span class="section-tag">{{ __('ui.home.advanced_healthcare') }}</span>
                    <h2>{{ __('ui.home.smart_care') }} <br><span>{{ __('ui.home.better_life') }}</span></h2>
                    <p>{{ __('ui.home.smart_care_desc') }}</p>
                    <a href="#departments-section" class="btn-main">{{ __('ui.home.book_your_appointment') }}</a>
                </div>

                <div class="gallery-wrap reveal-right">
                    <div class="gallery-card gallery-lg">
                        <img src="https://images.unsplash.com/photo-1581595219315-a187dd40c322?q=80&w=900&auto=format&fit=crop"
                            alt="medical">
                    </div>
                    <div class="gallery-card gallery-md">
                        <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=900&auto=format&fit=crop"
                            alt="doctor">
                    </div>
                    <div class="gallery-card gallery-sm">
                        <img src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?q=80&w=900&auto=format&fit=crop"
                            alt="hospital team">
                    </div>
                </div>
            </section>

            <section class="departments-section reveal" id="departments-section">
                <div class="departments-header">
                    <h2 class="departments-title">{{ __('ui.home.hospital_departments') }}</h2>
                    <div class="departments-controls">
                        <button class="dept-arrow" type="button" id="deptPrev">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                        <button class="dept-arrow" type="button" id="deptNext">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <div class="departments-slider-wrap">
                    <div class="departments-slider" id="departmentsSlider">

                        @foreach($departments as $department)
                        <div class="department-card">
                            <div class="department-image-wrap">
                                @php
                                $departmentImages = [
                                'Cardiology' =>
                                'https://images.unsplash.com/photo-1666214280557-f1b5022eb634?q=80&w=1200&auto=format&fit=crop',
                                'Pediatrics' =>
                                'https://images.unsplash.com/photo-1584515933487-779824d29309?q=80&w=1200&auto=format&fit=crop',
                                'Dentistry' =>
                                'https://images.unsplash.com/photo-1606811841689-23dfddce3e95?q=80&w=1200&auto=format&fit=crop',
                                'Orthopedics' =>
                                'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?q=80&w=1200&auto=format&fit=crop',
                                'Neurology' =>
                                'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?q=80&w=1200&auto=format&fit=crop',
                                'Internal Medicine' =>
                                'https://images.unsplash.com/photo-1579684385127-1ef15d508118?q=80&w=1200&auto=format&fit=crop',
                                'Dermatology' =>
                                'https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?q=80&w=1200&auto=format&fit=crop',
                                'Ophthalmology' =>
                                'https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?q=80&w=1200&auto=format&fit=crop',
                                'ENT' =>
                                'https://images.unsplash.com/photo-1629909613654-28e377c37b09?q=80&w=1200&auto=format&fit=crop',
                                'Radiology' =>
                                'https://images.unsplash.com/photo-1516549655169-df83a0774514?q=80&w=1200&auto=format&fit=crop',
                                'Oncology' =>
                                'https://images.unsplash.com/photo-1579154204601-01588f351e67?q=80&w=1200&auto=format&fit=crop',
                                'Urology' =>
                                'https://images.unsplash.com/photo-1584982751601-97dcc096659c?q=80&w=1200&auto=format&fit=crop',
                                'Gynecology' =>
                                'https://images.unsplash.com/photo-1666214276250-24f4f63d9aa1?q=80&w=1200&auto=format&fit=crop',
                                'Obstetrics' =>
                                'https://images.unsplash.com/photo-1516574187841-cb9cc2ca948b?q=80&w=1200&auto=format&fit=crop',
                                'Psychiatry' =>
                                'https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?q=80&w=1200&auto=format&fit=crop',
                                'Pulmonology' =>
                                'https://images.unsplash.com/photo-1631815589968-fdb09a223b1e?q=80&w=1200&auto=format&fit=crop',
                                'Gastroenterology' =>
                                'https://images.unsplash.com/photo-1580281657527-47d3f996c947?q=80&w=1200&auto=format&fit=crop',
                                'Nephrology' =>
                                'https://images.unsplash.com/photo-1631217868264-e5b90bb7e133?q=80&w=1200&auto=format&fit=crop',
                                'General Surgery' =>
                                'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=1200&auto=format&fit=crop',
                                'Emergency' =>
                                'https://images.unsplash.com/photo-1587745416684-47953f16f02f?q=80&w=1200&auto=format&fit=crop',
                                ];
                                $departmentName = trim((string) $department->name_en);
                                $departmentImage = $departmentImages[$departmentName]
                                    ?? match (true) {
                                        str_contains($departmentName, 'Card') => $departmentImages['Cardiology'],
                                        str_contains($departmentName, 'Ped') || str_contains($departmentName, 'Child') => $departmentImages['Pediatrics'],
                                        str_contains($departmentName, 'Dent') => $departmentImages['Dentistry'],
                                        str_contains($departmentName, 'Ortho') || str_contains($departmentName, 'Bone') => $departmentImages['Orthopedics'],
                                        str_contains($departmentName, 'Neuro') => $departmentImages['Neurology'],
                                        str_contains($departmentName, 'Derm') => $departmentImages['Dermatology'],
                                        str_contains($departmentName, 'Eye') || str_contains($departmentName, 'Ophth') => $departmentImages['Ophthalmology'],
                                        str_contains($departmentName, 'ENT') => $departmentImages['ENT'],
                                        str_contains($departmentName, 'Radio') => $departmentImages['Radiology'],
                                        str_contains($departmentName, 'Onco') => $departmentImages['Oncology'],
                                        str_contains($departmentName, 'Uro') => $departmentImages['Urology'],
                                        str_contains($departmentName, 'Gyn') || str_contains($departmentName, 'Obs') => $departmentImages['Gynecology'],
                                        str_contains($departmentName, 'Psych') => $departmentImages['Psychiatry'],
                                        str_contains($departmentName, 'Pulm') || str_contains($departmentName, 'Chest') => $departmentImages['Pulmonology'],
                                        str_contains($departmentName, 'Gastro') => $departmentImages['Gastroenterology'],
                                        str_contains($departmentName, 'Neph') || str_contains($departmentName, 'Kidney') => $departmentImages['Nephrology'],
                                        str_contains($departmentName, 'Surg') => $departmentImages['General Surgery'],
                                        str_contains($departmentName, 'Emerg') => $departmentImages['Emergency'],
                                        default => 'https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=1200&auto=format&fit=crop',
                                    };
                                @endphp

                                <img src="{{ $departmentImage }}"
                                    alt="{{ $department->name_en }}">

                                <div class="department-overlay">
                                    <span>{{ __('ui.home.specialized_care') }}</span>
                                </div>
                            </div>

                            <div class="department-body">
                                <div class="department-meta">
                                    <div class="department-icon">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                    <span>{{ $department->name_en }}</span>
                                </div>

                                <div class="department-sub">{{ __('ui.home.department_sub') }}</div>

                                <div class="department-footer">
                                    <a href="{{ route('specialties.doctors', $department->id) }}">
                                        {{ __('ui.home.view_department') }} <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </section>

            <section class="diagnostic-services reveal">
                <article class="diagnostic-service" id="radiology-section">
                    <img src="https://images.unsplash.com/photo-1516549655169-df83a0774514?q=80&w=1200&auto=format&fit=crop"
                        alt="Radiology department">
                    <div class="diagnostic-content">
                        <span><i class="bi bi-image"></i>{{ __('ui.nav.radiology') }}</span>
                        <h3>{{ __('ui.nav.radiology') }}</h3>
                        <p>Modern imaging services for accurate diagnosis, follow-up, and coordinated patient care.</p>
                        <a href="{{ route('radiology_results.index') }}" class="btn-main mt-3">View Results</a>
                    </div>
                </article>

                <article class="diagnostic-service" id="laboratory-section">
                    <img src="https://images.unsplash.com/photo-1579154204601-01588f351e67?q=80&w=1200&auto=format&fit=crop"
                        alt="Laboratory department">
                    <div class="diagnostic-content">
                        <span><i class="bi bi-prescription2"></i>{{ __('ui.nav.laboratory') }}</span>
                        <h3>{{ __('ui.nav.laboratory') }}</h3>
                        <p>Reliable laboratory testing with secure results available through each patient dashboard.</p>
                        <a href="{{ route('laboratory_results.index') }}" class="btn-main mt-3">View Results</a>
                    </div>
                </article>
            </section>

            <section class="overview">
                <div class="overview-text reveal-left">
                    <h2>{{ __('ui.common.hospital') }} <span>{{ __('ui.home.overview') }}</span></h2>
                </div>

                <div class="overview-grid reveal-right">
                    <div class="overview-column top-shift">
                        <div class="overview-card blue-icon">
                            <div class="overview-icon"><i class="bi bi-people"></i></div>
                            <div class="num count-num" data-target="{{ $patientsCount }}">0</div>
                            <div class="label">{{ __('ui.home.total_patients') }}</div>
                        </div>

                        <div class="overview-card teal-icon">
                            <div class="overview-icon"><i class="bi bi-person-badge"></i></div>
                            <div class="num count-num" data-target="{{ $doctorsCount }}">0</div>
                            <div class="label">{{ __('ui.home.doctors') }}</div>
                        </div>
                    </div>

                    <div class="overview-column">
                        <div class="overview-card">
                            <div class="overview-icon"><i class="bi bi-calendar-event"></i></div>
                            <div class="num count-num" data-target="{{ $appointmentsCount }}">0</div>
                            <div class="label">{{ __('ui.home.total_appointments') }}</div>
                        </div>

                        <div class="overview-card purple-icon">
                            <div class="overview-icon"><i class="bi bi-calendar-check"></i></div>
                            <div class="num count-num" data-target="{{ $todayAppointments }}">0</div>
                            <div class="label">{{ __('ui.home.appointments_today') }}</div>
                        </div>
                    </div>

                    <div class="overview-column bottom-shift">
                        <div class="overview-card teal-icon">
                            <div class="overview-icon"><i class="bi bi-hospital"></i></div>
                            <div class="num count-num" data-target="{{ $roomsCount }}">0</div>
                            <div class="label">{{ __('ui.home.total_rooms') }}</div>
                        </div>

                        <div class="overview-card sky-icon">
                            <div class="overview-icon"><i class="bi bi-door-open"></i></div>
                            <div class="num count-num" data-target="{{ $availableRooms }}">0</div>
                            <div class="label">{{ __('ui.home.available_rooms') }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="news-section reveal">
                <h2 class="section-title">{{ __('ui.home.latest_news') }}</h2>
                <div class="news-list stagger-children">
                    <div class="news-card">
                        <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=900&auto=format&fit=crop"
                            alt="news">
                        <div class="news-body">
                            <div class="news-date">20-12-2025</div>
                            <div class="news-title">International Medical Conference</div>
                            <p class="news-text">A major medical conference bringing together leading doctors and
                                specialists to discuss the latest developments in healthcare.</p>
                        </div>
                    </div>

                    <div class="news-card">
                        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=900&auto=format&fit=crop"
                            alt="news">
                        <div class="news-body">
                            <div class="news-date">17-12-2025</div>
                            <div class="news-title">Oncology Departments Updated</div>
                            <p class="news-text">Several medical service units have been upgraded to provide better care
                                and support for patients.</p>
                        </div>
                    </div>

                    <div class="news-card">
                        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=900&auto=format&fit=crop"
                            alt="news">
                        <div class="news-body">
                            <div class="news-date">17-12-2025</div>
                            <div class="news-title">Improved Patient Experience</div>
                            <p class="news-text">The hospital has enhanced work environments and treatment services to
                                improve patient comfort and healthcare quality.</p>
                        </div>
                    </div>

                    <div class="news-card">
                        <img src="https://images.unsplash.com/photo-1516549655169-df83a0774514?q=80&w=900&auto=format&fit=crop"
                            alt="news">
                        <div class="news-body">
                            <div class="news-date">14-12-2025</div>
                            <div class="news-title">New Medical Unit Opened</div>
                            <p class="news-text">A new service unit has been introduced to support patients and
                                accelerate diagnosis and follow-up processes.</p>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>

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
                        <iframe src="https://www.google.com/maps?q=New%20Beni%20Suef%20City&z=13&output=embed"
                            loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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

    <div class="ai-assistant-wrap">

        <div class="ai-bubble" id="aiBubble">
            {{ __('ui.home.ai_help') }}
        </div>

        <a href="{{ url('/chat') }}" class="ai-robot-btn">
            <img src="{{ asset('images/ai-robot-medical.png') }}" class="robot-img">
        </a>

    </div>
    <script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.getElementById('page-loader').classList.add('hidden');
        }, 800);
    });

    window.addEventListener('load', () => {
        const label = document.getElementById('aiLabel');

        setTimeout(() => {
            label.style.opacity = "1";
            label.style.transform = "translateY(0)";
        }, 1500);

        // تختفي بعد شوية
        setTimeout(() => {
            label.style.opacity = "0";
        }, 5000);
    });

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                revealObserver.unobserve(e.target);
            }
        });
    }, {
        threshold: 0.12
    });
    window.addEventListener('load', () => {
        const bubble = document.getElementById('aiBubble');

        // تظهر بعد ما الصفحة تفتح
        setTimeout(() => {
            bubble.classList.add('show');
        }, 1500);

        // تختفي بعد شوية
        setTimeout(() => {
            bubble.classList.remove('show');
        }, 6000);
    });

    document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .stagger-children').forEach(el => {
        revealObserver.observe(el);
    });

    function animateCounter(el) {
        const target = parseInt(el.dataset.target);
        const suffix = el.dataset.suffix || '';
        const dur = 1600;
        const step = 16;
        const inc = target / (dur / step);
        let current = 0;

        const timer = setInterval(() => {
            current += inc;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = Math.floor(current).toLocaleString() + suffix;
        }, step);
    }

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                animateCounter(e.target);
                counterObserver.unobserve(e.target);
            }
        });
    }, {
        threshold: 0.3
    });

    document.querySelectorAll('.count-num').forEach(el => counterObserver.observe(el));

    const departmentsSlider = document.getElementById('departmentsSlider');
    const deptPrev = document.getElementById('deptPrev');
    const deptNext = document.getElementById('deptNext');

    if (deptPrev && deptNext && departmentsSlider) {
        deptNext.addEventListener('click', () => departmentsSlider.scrollBy({
            left: 362,
            behavior: 'smooth'
        }));
        deptPrev.addEventListener('click', () => departmentsSlider.scrollBy({
            left: -362,
            behavior: 'smooth'
        }));
    }

    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        navbar.style.boxShadow = window.scrollY > 10 ?
            '0 6px 24px rgba(0,0,0,0.18)' :
            '0 3px 12px rgba(0,0,0,0.08)';
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
