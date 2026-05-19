<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('ui.doctor.appointments_title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
    :root {
        --primary: #114a9f;
        --primary-dark: #0a3d86;
        --text: #2f3a45;
        --muted: #6b7280;
        --danger: #ff3b3f;
        --shadow: 0 18px 36px rgba(34, 52, 84, 0.18);
        --radius-xl: 28px;
        --radius-lg: 22px;
        --sidebar-width: 290px;
        --soft-bg: #f7f7f7;
        --card-bg: #f8f8f8;
        --border: #d8dde6;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: {{ app()->isLocale('ar') ? "'Cairo', Arial, sans-serif" : "'Inter', Arial, sans-serif" }};
        min-height: 100vh;
        color: var(--text);
        overflow-x: hidden;
        background: radial-gradient(circle at 72% 52%, rgba(49, 157, 255, 0.95) 0%, rgba(110, 189, 255, 0.72) 20%, rgba(196, 224, 248, 0.62) 42%, rgba(235, 240, 245, 0.95) 70%, #f2f2f2 100%);
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    button,
    input,
    textarea {
        font-family: inherit;
    }

    @php $doctor=auth()->guard('doctor')->user();

    @endphp .page {
        min-height: 100vh;
        position: relative;
    }

    /* SIDEBAR */
    .sidebar {
        width: var(--sidebar-width);
        padding: 22px 18px;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1200;
        transform: translateX(-112%);
        transition: transform .35s cubic-bezier(.4, 0, .2, 1);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .sidebar-panel {
        height: 100%;
        background: rgba(247, 247, 247, 0.97);
        border-radius: 34px;
        box-shadow: 0 16px 35px rgba(0, 0, 0, 0.14);
        display: flex;
        flex-direction: column;
        padding: 28px 18px 18px;
        backdrop-filter: blur(2px);
    }

    .sidebar-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 78px;
        margin-bottom: 26px;
    }

    .sidebar-logo img {
        max-width: 145px;
        height: auto;
        object-fit: contain;
    }

    .menu {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin: 0;
        padding: 0;
    }

    .menu a {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: none;
        background: transparent;
        border-radius: 18px;
        font-size: 16px;
        color: #2e3844;
        cursor: pointer;
        transition: .22s ease;
        position: relative;
        overflow: hidden;
    }

    .menu a::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg,
                transparent 30%,
                rgba(255, 255, 255, 0.32) 50%,
                transparent 70%);
        transform: translateX(-100%);
        transition: transform .4s ease;
        pointer-events: none;
    }

    .menu a:hover::before {
        transform: translateX(100%);
    }

    .menu a:hover {
        background: rgba(255, 255, 255, 0.35);
        color: #2e3844;
        transform: translateX(4px);
    }

    .menu li.active a {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 10px 20px rgba(17, 74, 159, 0.28);
    }

    .menu li.active a::before {
        display: none;
    }

    .menu i {
        font-size: 21px;
        width: 24px;
        text-align: center;
        transition: .22s ease;
    }

    .menu a:hover i {
        transform: scale(1.15);
    }

    .logout-wrap {
        margin-top: auto;
        padding-top: 18px;
    }

    .logout-btn {
        width: 100%;
        border: none;
        border-radius: 14px;
        background: var(--danger);
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        padding: 16px 18px;
        cursor: pointer;
        transition: .25s ease;
        position: relative;
        overflow: hidden;
    }

    .logout-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.18), transparent);
        transform: translateX(-100%);
        transition: transform .4s ease;
    }

    .logout-btn:hover::before {
        transform: translateX(100%);
    }

    .logout-btn:hover {
        filter: brightness(.94);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 59, 63, .3);
    }

    /* OVERLAY */
    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .18);
        opacity: 0;
        pointer-events: none;
        transition: .25s ease;
        z-index: 1100;
    }

    .overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    /* MAIN */
    .main-content {
        min-height: 100vh;
        padding: 24px 28px 40px;
    }

    .topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        padding: 0 0 8px;
        margin-bottom: 28px;
        animation: slideDown .6s ease both;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .left-section {
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .menu-btn {
        font-size: 28px;
        cursor: pointer;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4b5563;
        transition: .25s ease;
        border: none;
        background: transparent;
    }

    .menu-btn:hover {
        background: rgba(255, 255, 255, 0.55);
        transform: scale(1.08) rotate(90deg);
    }

    .brand-wrapper h1 {
        margin: 0;
        font-size: 21px;
        font-weight: 600;
        letter-spacing: .2px;
    }

    .brand-wrapper p {
        margin: 6px 0 0;
        font-size: 14px;
        color: #4b5563;
    }

    .right {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .search-box {
        position: relative;
        width: 322px;
        max-width: 100%;
    }

    .search-box input {
        width: 100%;
        height: 46px;
        padding: 0 48px 0 18px;
        border: none;
        outline: none;
        border-radius: 18px;
        background: #f8f8f8;
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        font-size: 15px;
        color: #374151;
        transition: .3s ease;
    }

    .search-box input:focus {
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(34, 52, 84, 0.22);
        background: #fff;
    }

    .search-box i {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 22px;
        pointer-events: none;
    }

    .icon-circle {
        width: 46px;
        height: 46px;
        background: #f8f8f8;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        color: #4f5965;
        font-size: 21px;
        transition: .25s ease;
        position: relative;
    }

    .icon-circle:hover {
        transform: translateY(-3px);
        background: #fff;
    }

    .icon-circle::after {
        content: "";
        position: absolute;
        top: 9px;
        right: 9px;
        width: 8px;
        height: 8px;
        background: #ff3b3f;
        border-radius: 50%;
        border: 2px solid #f8f8f8;
        animation: notifPulse 2s ease-in-out infinite;
    }

    @keyframes notifPulse {

        0%,
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 59, 63, .4);
        }

        50% {
            transform: scale(1.15);
            box-shadow: 0 0 0 5px rgba(255, 59, 63, 0);
        }
    }

    .profile-link {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #1f2937;
        font-size: 15px;
        font-weight: 500;
    }

    .profile-info {
        text-align: left;
    }

    .profile-info strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
    }

    .profile-info span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 13px;
    }

    .avatar {
        width: 44px;
        height: 44px;
        background: #124d9d;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        transition: .25s ease;
        animation: avatarPulse 3s ease-in-out infinite;
    }

    .avatar:hover {
        transform: scale(1.1) rotate(5deg);
    }

    @keyframes avatarPulse {

        0%,
        100% {
            box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        }

        50% {
            box-shadow: 0 10px 28px rgba(17, 74, 159, .45);
        }
    }

    /* PAGE HEADER */
    .page-header {
        max-width: 1080px;
        margin: 10px auto 26px;
        padding: 32px 34px;
        background: rgba(248, 248, 248, 0.96);
        border-radius: var(--radius-xl);
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        border-left: 4px solid var(--primary);
        opacity: 0;
        transform: translateY(24px);
        animation: fadeUp .7s ease .2s forwards;
    }

    .page-header h1 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #2f3943;
    }

    .subtitle {
        font-size: 16px;
        color: #64748b;
    }

    /* SECTION */
    .content-wrap {
        max-width: 1080px;
        margin: 0 auto;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #26313b;
        margin: 30px 0 14px;
        padding-left: 6px;
    }

    .table-box {
        background: rgba(248, 248, 248, 0.97);
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        margin-bottom: 28px;
        opacity: 0;
        transform: translateY(24px);
        transition: .3s ease;
    }

    .table-box.visible {
        animation: fadeUp .65s ease forwards;
    }

    .table-box:hover {
        box-shadow: 0 22px 44px rgba(34, 52, 84, 0.22);
    }

    .table-scroll {
        overflow-x: auto;
    }

    .table-header,
    .row {
        display: grid;
        grid-template-columns: 2fr 1.1fr 1.5fr .8fr 1.5fr 1fr 2.4fr;
        align-items: center;
        min-width: 1180px;
    }

    .table-header {
        background: #114a9e;
        color: #fff;
        font-weight: 700;
        font-size: 15px;
        padding: 18px 24px;
    }

    .row {
        padding: 16px 24px;
        border-bottom: 1px solid #d9d9d9;
        background: #f7f7f7;
        transition: .25s ease;
    }

    .row:last-child {
        border-bottom: none;
    }

    .row:hover {
        background: #f1f6ff;
        transform: scale(1.004);
    }

    .private-table-header,
    .private-row {
        display: grid;
        grid-template-columns: 1.35fr 1fr 1.45fr 1.35fr 1fr 1fr 1.45fr 1.1fr 1fr 1.2fr 1fr 2.4fr;
        align-items: center;
        min-width: 1980px;
    }

    .private-table-header {
        background: #0d3f87;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        padding: 18px 24px;
    }

    .private-row {
        padding: 16px 24px;
        border-bottom: 1px solid #d9d9d9;
        background: #f7f7f7;
        transition: .25s ease;
    }

    .private-row:last-child {
        border-bottom: none;
    }

    .private-row:hover {
        background: #edf4ff;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-soft.private {
        background: #dbeafe;
        color: #124d9d;
    }

    .badge-soft.payment {
        background: #ecfdf3;
        color: #166534;
    }

    .badge-soft.status {
        background: #f3f4f6;
        color: #374151;
    }

    .patient {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 15px;
        color: #26313b;
        font-weight: 500;
    }

    .circle {
        width: 40px;
        height: 40px;
        background: #114a9e;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 10px 18px rgba(17, 74, 158, .18);
    }

    .cell {
        font-size: 14px;
        color: #35424f;
    }

    .date-cell {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .date-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
    }

    .date-badge.today {
        background: #dcfce7;
        color: #166534;
    }

    .date-badge.tomorrow {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .btn {
        background: #114a9e;
        color: #fff;
        padding: 10px 22px;
        border-radius: 14px;
        text-align: center;
        font-weight: 700;
        display: inline-block;
        min-width: 90px;
        box-shadow: 0 10px 20px rgba(17, 74, 159, .2);
        transition: .25s ease;
    }

    .btn:hover {
        background: #0d3f87;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(17, 74, 159, .3);
    }

    .complete-btn {
        border: 0;
        border-radius: 10px;
        background: #114a9e;
        color: #fff;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        box-shadow: 0 8px 16px rgba(17, 74, 159, .2);
        transition: .2s ease;
        white-space: nowrap;
    }

    .complete-btn:hover {
        background: #0d3f87;
        transform: translateY(-1px);
    }

    .complete-muted {
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
    }

    .diagnostic-actions {
        display: grid;
        gap: 8px;
    }

    .diagnostic-toggle-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .request-toggle,
    .result-link {
        border: 0;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
        cursor: pointer;
        transition: .2s ease;
    }

    .request-toggle {
        background: #e8f1ff;
        color: #114a9e;
    }

    .result-link {
        background: #ecfdf3;
        color: #166534;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .diagnostic-panel {
        display: none;
        padding: 10px;
        border: 1px solid #d8dde6;
        border-radius: 14px;
        background: #fff;
        gap: 8px;
    }

    .diagnostic-panel.open {
        display: grid;
    }

    .diagnostic-panel input,
    .diagnostic-panel select,
    .diagnostic-panel textarea {
        width: 100%;
        border: 1px solid #d8dde6;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 12px;
    }

    .diagnostic-panel textarea {
        min-height: 62px;
        resize: vertical;
    }

    .diagnostic-submit {
        border: 0;
        border-radius: 10px;
        background: #114a9e;
        color: #fff;
        padding: 8px 10px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
    }

    .request-history {
        display: grid;
        gap: 5px;
        font-size: 12px;
        color: #4b5563;
    }

    .request-history-item {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .priority-urgent {
        background: #fee2e2;
        color: #b91c1c;
    }

    .reveal {
        opacity: 0;
        transform: translateY(28px);
        transition: opacity .65s ease, transform .65s ease;
    }

    .reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(28px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* RESPONSIVE */
    @media (max-width:992px) {
        .main-content {
            padding: 22px 18px 40px;
        }

        .topbar {
            flex-direction: column;
            align-items: stretch;
            margin-bottom: 22px;
        }

        .right {
            justify-content: space-between;
        }

        .search-box {
            width: 100%;
        }
    }

    @media (max-width:768px) {
        .page-header {
            padding: 24px 20px;
        }

        .page-header h1 {
            font-size: 20px;
        }

        .section-title {
            font-size: 18px;
        }
    }

    @media (max-width:620px) {
        .main-content {
            padding: 18px 16px 28px;
        }

        .right {
            flex-direction: column;
            align-items: stretch;
        }

        .profile-link {
            justify-content: space-between;
        }

        .page-header,
        .table-box {
            border-radius: 22px;
        }
    }
    </style>
</head>

<body>

    <div class="page">

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-panel">
                <div class="sidebar-logo">
                    <img src="{{ asset('images/logo_Image.png') }}" alt="NUH">
                </div>

                <ul class="menu">
                    <li>
                        <a href="{{ route('doctor.profile') }}">
                            <i class="bi bi-grid-1x2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li class="active">
                        <a href="{{ route('doctor.appointments') }}">
                            <i class="bi bi-calendar-event"></i>
                            <span>Appointments</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('doctor.signature') }}">
                            <i class="bi bi-pen"></i>
                            <span>Electronic Signature</span>
                        </a>
                    </li>
                </ul>

                <div class="logout-wrap">
                    <a href="{{ url('/') }}" class="logout-btn"
                        style="display:flex;align-items:center;justify-content:center;">
                        Log out
                    </a>
                </div>
            </div>
        </aside>

        <!-- OVERLAY -->
        <div class="overlay" id="overlay"></div>

        <!-- MAIN -->
        <main class="main-content">
            <header class="topbar">
                <div class="left-section">
                    <button class="menu-btn" id="menuBtn" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="brand-wrapper">
                        <h1>NUH</h1>
                        <p>{{ __('ui.doctor.dashboard') }}</p>
                    </div>
                </div>

                <div class="right">
                    <div class="search-box">
                        <input type="text" placeholder="{{ __('ui.doctor.search_patients') }}">
                        <i class="bi bi-search"></i>
                    </div>

                    <button class="icon-circle" type="button" aria-label="Notifications">
                        <i class="bi bi-bell"></i>
                    </button>

                    <a href="{{ route('doctor.profile') }}" class="profile-link">
                        <div class="profile-info">
                            <strong>{{ $doctor->name ?? 'Doctor Ahmed' }}</strong>
                            <span>{{ $doctor->department->name_en ?? 'Doctor' }}</span>
                        </div>
                        <div class="avatar">
                            {{ strtoupper(substr($doctor->name ?? 'A',0,1)) }}
                        </div>
                    </a>
                </div>
            </header>

            <div class="page-header">
                <h1>{{ __('ui.doctor.appointments_title') }}</h1>
                <div class="subtitle">{{ __('ui.doctor.appointments_subtitle') }}</div>
            </div>

            <div class="content-wrap">
                <div class="section-title">{{ __('ui.doctor.appointments') }}</div>

                <div class="table-box reveal">
                    <x-table-filters
                        :action="route('doctor.appointments')"
                        :type-options="['hospital' => 'Hospital booking', 'private' => 'Private clinic booking']"
                        :status-options="['Pending' => 'Pending', 'Confirmed' => 'Confirmed', 'Completed' => 'Completed']" />
                    <div class="table-scroll">
                        <div class="table-header">
                            <div>{{ __('ui.doctor.patient_name') }}</div>
                            <div>{{ __('ui.doctor.patient_id') }}</div>
                            <div>{{ __('ui.common.date') }}</div>
                            <div>{{ __('ui.common.time') }}</div>
                            <div>{{ __('ui.doctor.reason_visit') }}</div>
                            <div>{{ __('ui.common.booking_status') }}</div>
                            <div>Action</div>
                        </div>

                        <div id="doctorAppointmentsRows">
                            @forelse($doctorAppointments as $appointment)
                            @php
                            $patientName = trim(($appointment->first_name ?? '') . ' ' . ($appointment->last_name ?? ''));
                            $patientName = $patientName !== '' ? $patientName : ($appointment->patient?->full_name ?? 'Unknown Patient');
                            $patientCode = $appointment->patient?->file_number
                            ? 'P-' . $appointment->patient?->file_number
                            : 'APT-' . str_pad($appointment->id, 4, '0', STR_PAD_LEFT);
                            $initial = strtoupper(substr($patientName, 0, 1));
                            $canComplete = strtolower((string) $appointment->status) === 'confirmed';
                            @endphp
                            <div class="row">
                                <div class="patient">
                                    <div class="circle">{{ $initial }}</div>
                                    <span>{{ $patientName }}</span>
                                </div>
                                <div class="cell">{{ $patientCode }}</div>
                                <div class="cell date-cell">
                                    <span>{{ \Carbon\Carbon::parse($appointment->date)->format('D d M') }}</span>
                                    @if($appointment->isToday)
                                        <span class="date-badge today">Today</span>
                                    @elseif($appointment->isTomorrow)
                                        <span class="date-badge tomorrow">Tomorrow</span>
                                    @endif
                                </div>
                                <div class="cell">{{ $appointment->time }}</div>
                                <div class="cell">{{ $appointment->reason ?? (app()->isLocale('ar') ? 'استشارة عامة' : 'General consultation') }}</div>
                                <div class="cell"><span class="badge-soft status">{{ $appointment->status ?? __('ui.common.pending') }}</span></div>
                                <div class="cell">
                                    <div class="diagnostic-actions">
                                    @if($canComplete)
                                        <form method="POST" action="{{ route('doctor.appointments.complete', $appointment) }}">
                                            @csrf
                                            <button type="submit" class="complete-btn">Mark Completed</button>
                                        </form>
                                    @else
                                        <span class="complete-muted">{{ $appointment->isCompleted() ? 'Completed' : 'Not ready' }}</span>
                                    @endif
                                        <div class="diagnostic-toggle-row">
                                            <button type="button" class="request-toggle" data-panel="lab-{{ $appointment->id }}">Request Lab Test</button>
                                            <button type="button" class="request-toggle" data-panel="radiology-{{ $appointment->id }}">Request Radiology</button>
                                        </div>

                                        <form id="lab-{{ $appointment->id }}" class="diagnostic-panel" method="POST" action="{{ route('doctor.diagnostics.store', ['appointment' => $appointment, 'type' => 'lab']) }}">
                                            @csrf
                                            <select name="request_type" required>
                                                <option value="">Choose lab test</option>
                                                <option value="Complete Blood Count">Complete Blood Count</option>
                                                <option value="Blood Glucose">Blood Glucose</option>
                                                <option value="Liver Function Test">Liver Function Test</option>
                                                <option value="Kidney Function Test">Kidney Function Test</option>
                                                <option value="Urinalysis">Urinalysis</option>
                                                <option value="Culture Test">Culture Test</option>
                                            </select>
                                            <select name="priority" required>
                                                <option value="normal">Normal</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                            <textarea name="notes" placeholder="Clinical notes"></textarea>
                                            <button type="submit" class="diagnostic-submit">Send Lab Request</button>
                                        </form>

                                        <form id="radiology-{{ $appointment->id }}" class="diagnostic-panel" method="POST" action="{{ route('doctor.diagnostics.store', ['appointment' => $appointment, 'type' => 'radiology']) }}">
                                            @csrf
                                            <select name="request_type" required>
                                                <option value="">Choose radiology type</option>
                                                <option value="Chest X-Ray">Chest X-Ray</option>
                                                <option value="Ultrasound">Ultrasound</option>
                                                <option value="CT Scan">CT Scan</option>
                                                <option value="MRI">MRI</option>
                                                <option value="Mammography">Mammography</option>
                                                <option value="Doppler">Doppler</option>
                                            </select>
                                            <select name="priority" required>
                                                <option value="normal">Normal</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                            <textarea name="notes" placeholder="Clinical notes"></textarea>
                                            <button type="submit" class="diagnostic-submit">Send Radiology Request</button>
                                        </form>

                                        <div class="request-history">
                                            @foreach($appointment->labRequests as $request)
                                                <div class="request-history-item">
                                                    <span class="badge-soft {{ $request->priority === 'urgent' ? 'priority-urgent' : 'status' }}">Lab: {{ $request->request_type }}</span>
                                                    <span>{{ ucfirst($request->status) }}</span>
                                                    @if($request->uploaded_result)
                                                        <a class="result-link" href="{{ route('doctor.diagnostics.download', ['type' => 'lab', 'id' => $request->id]) }}">Download</a>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @foreach($appointment->radiologyRequests as $request)
                                                <div class="request-history-item">
                                                    <span class="badge-soft {{ $request->priority === 'urgent' ? 'priority-urgent' : 'status' }}">Radiology: {{ $request->request_type }}</span>
                                                    <span>{{ ucfirst($request->status) }}</span>
                                                    @if($request->uploaded_result)
                                                        <a class="result-link" href="{{ route('doctor.diagnostics.download', ['type' => 'radiology', 'id' => $request->id]) }}">Download</a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="row">
                                <div class="cell" style="grid-column:1/-1;text-align:center;">{{ app()->isLocale('ar') ? 'لا توجد مواعيد' : 'No appointments found' }}</div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="table-filter-pagination" style="padding: 0 20px 20px;">
                        {{ $doctorAppointments->links() }}
                    </div>
                </div>

                <div class="section-title">{{ __('ui.doctor.private_clinic_bookings') }}</div>

                <div class="table-box reveal">
                    <x-table-filters
                        :action="route('doctor.appointments')"
                        :type-options="['hospital' => 'Hospital booking', 'private' => 'Private clinic booking']"
                        :status-options="['Pending' => 'Pending', 'Confirmed' => 'Confirmed', 'Completed' => 'Completed']" />
                    <div class="table-scroll">
                        <div class="private-table-header">
                            <div>{{ __('ui.doctor.patient_name') }}</div>
                            <div>{{ __('ui.common.phone') }}</div>
                            <div>{{ __('ui.common.email') }}</div>
                            <div>{{ __('ui.doctor.booking_date') }}</div>
                            <div>{{ __('ui.doctor.booking_time') }}</div>
                            <div>{{ __('ui.doctor.location') }}</div>
                            <div>{{ __('ui.common.reason') }}</div>
                            <div>{{ __('ui.common.payment_status') }}</div>
                            <div>{{ __('ui.common.booking_status') }}</div>
                            <div>{{ __('ui.common.notes') }}</div>
                            <div>{{ __('ui.common.created_at') }}</div>
                            <div>Action</div>
                        </div>

                        <div id="privateClinicAppointmentsRows">
                            @forelse($privateClinicAppointments as $appointment)
                            @php
                            $patientName = trim(($appointment->first_name ?? '') . ' ' . ($appointment->last_name ?? ''));
                            $patientName = $patientName !== '' ? $patientName : ($appointment->patient?->full_name ?? 'Unknown Patient');
                            $canComplete = strtolower((string) $appointment->status) === 'confirmed';
                            @endphp
                            <div class="private-row">
                                <div class="patient">
                                    <div class="circle">{{ strtoupper(substr($patientName, 0, 1)) }}</div>
                                    <span>{{ $patientName }}</span>
                                </div>
                                <div class="cell">{{ $appointment->phone ?? __('ui.common.n_a') }}</div>
                                <div class="cell">{{ $appointment->email ?? __('ui.common.n_a') }}</div>
                                <div class="cell date-cell">
                                    <span>{{ $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('D d M') : __('ui.common.n_a') }}</span>
                                    @if($appointment->isToday)
                                        <span class="date-badge today">Today</span>
                                    @elseif($appointment->isTomorrow)
                                        <span class="date-badge tomorrow">Tomorrow</span>
                                    @endif
                                </div>
                                <div class="cell">{{ $appointment->time ? substr((string) $appointment->time, 0, 5) : __('ui.common.n_a') }}</div>
                                <div class="cell"><span class="badge-soft private">{{ \App\Support\PrivateClinicBookingSupport::typeLabel($appointment->type) }}</span></div>
                                <div class="cell">{{ $appointment->reason ?: (app()->isLocale('ar') ? 'استشارة عامة' : 'General consultation') }}</div>
                                <div class="cell"><span class="badge-soft payment">{{ $appointment->payment_status ? \Illuminate\Support\Str::headline(str_replace('_', ' ', $appointment->payment_status)) : __('ui.common.pending') }}</span></div>
                                <div class="cell"><span class="badge-soft status">{{ $appointment->status ?? __('ui.common.pending') }}</span></div>
                                <div class="cell">{{ $appointment->clinic_notes ?: __('ui.common.n_a') }}</div>
                                <div class="cell">{{ $appointment->created_at ? $appointment->created_at->format('M j, Y g:i A') : __('ui.common.n_a') }}</div>
                                <div class="cell">
                                    <div class="diagnostic-actions">
                                    @if($canComplete)
                                        <form method="POST" action="{{ route('doctor.appointments.complete', $appointment) }}">
                                            @csrf
                                            <button type="submit" class="complete-btn">Mark Completed</button>
                                        </form>
                                    @else
                                        <span class="complete-muted">{{ $appointment->isCompleted() ? 'Completed' : 'Not ready' }}</span>
                                    @endif
                                        <div class="diagnostic-toggle-row">
                                            <button type="button" class="request-toggle" data-panel="private-lab-{{ $appointment->id }}">Request Lab Test</button>
                                            <button type="button" class="request-toggle" data-panel="private-radiology-{{ $appointment->id }}">Request Radiology</button>
                                        </div>

                                        <form id="private-lab-{{ $appointment->id }}" class="diagnostic-panel" method="POST" action="{{ route('doctor.diagnostics.store', ['appointment' => $appointment, 'type' => 'lab']) }}">
                                            @csrf
                                            <select name="request_type" required>
                                                <option value="">Choose lab test</option>
                                                <option value="Complete Blood Count">Complete Blood Count</option>
                                                <option value="Blood Glucose">Blood Glucose</option>
                                                <option value="Liver Function Test">Liver Function Test</option>
                                                <option value="Kidney Function Test">Kidney Function Test</option>
                                                <option value="Urinalysis">Urinalysis</option>
                                                <option value="Culture Test">Culture Test</option>
                                            </select>
                                            <select name="priority" required>
                                                <option value="normal">Normal</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                            <textarea name="notes" placeholder="Clinical notes"></textarea>
                                            <button type="submit" class="diagnostic-submit">Send Lab Request</button>
                                        </form>

                                        <form id="private-radiology-{{ $appointment->id }}" class="diagnostic-panel" method="POST" action="{{ route('doctor.diagnostics.store', ['appointment' => $appointment, 'type' => 'radiology']) }}">
                                            @csrf
                                            <select name="request_type" required>
                                                <option value="">Choose radiology type</option>
                                                <option value="Chest X-Ray">Chest X-Ray</option>
                                                <option value="Ultrasound">Ultrasound</option>
                                                <option value="CT Scan">CT Scan</option>
                                                <option value="MRI">MRI</option>
                                                <option value="Mammography">Mammography</option>
                                                <option value="Doppler">Doppler</option>
                                            </select>
                                            <select name="priority" required>
                                                <option value="normal">Normal</option>
                                                <option value="urgent">Urgent</option>
                                            </select>
                                            <textarea name="notes" placeholder="Clinical notes"></textarea>
                                            <button type="submit" class="diagnostic-submit">Send Radiology Request</button>
                                        </form>

                                        <div class="request-history">
                                            @foreach($appointment->labRequests as $request)
                                                <div class="request-history-item">
                                                    <span class="badge-soft {{ $request->priority === 'urgent' ? 'priority-urgent' : 'status' }}">Lab: {{ $request->request_type }}</span>
                                                    <span>{{ ucfirst($request->status) }}</span>
                                                    @if($request->uploaded_result)
                                                        <a class="result-link" href="{{ route('doctor.diagnostics.download', ['type' => 'lab', 'id' => $request->id]) }}">Download</a>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @foreach($appointment->radiologyRequests as $request)
                                                <div class="request-history-item">
                                                    <span class="badge-soft {{ $request->priority === 'urgent' ? 'priority-urgent' : 'status' }}">Radiology: {{ $request->request_type }}</span>
                                                    <span>{{ ucfirst($request->status) }}</span>
                                                    @if($request->uploaded_result)
                                                        <a class="result-link" href="{{ route('doctor.diagnostics.download', ['type' => 'radiology', 'id' => $request->id]) }}">Download</a>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="private-row">
                                <div class="cell" style="grid-column:1/-1;text-align:center;">{{ __('ui.doctor.no_private_bookings') }}</div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="table-filter-pagination" style="padding: 0 20px 20px;">
                        {{ $privateClinicAppointments->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuBtn = document.getElementById('menuBtn');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
        menuBtn.querySelector('i').className = sidebar.classList.contains('active') ? 'bi bi-x-lg' : 'bi bi-list';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
        menuBtn.querySelector('i').className = 'bi bi-list';
    }

    menuBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);

    const revealObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15
    });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    document.querySelectorAll('.request-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const panel = document.getElementById(button.dataset.panel);
            if (panel) {
                panel.classList.toggle('open');
            }
        });
    });

    const topbar = document.querySelector('.topbar');
    window.addEventListener('scroll', () => {
        topbar.style.transition = 'box-shadow .3s ease';
        topbar.style.boxShadow = window.scrollY > 10 ?
            '0 6px 24px rgba(34,52,84,0.15)' :
            'none';
    });

    // Server-rendered rows come from the authenticated doctor's query in DoctorController.
    </script>

</body>

</html>
