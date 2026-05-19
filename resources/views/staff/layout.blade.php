<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Staff Workspace')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --brand: #0f4c96;
            --brand-dark: #0a3970;
            --brand-soft: rgba(15, 76, 150, 0.10);
            --bg-top: #dff0ff;
            --bg-bottom: #f7fbff;
            --surface: rgba(255, 255, 255, 0.94);
            --surface-muted: rgba(248, 251, 255, 0.96);
            --border-soft: rgba(123, 150, 184, 0.20);
            --text-main: #223146;
            --text-muted: #6b7a8d;
            --success: #1e9b5a;
            --warning: #d68b00;
            --danger: #d94b4b;
            --shadow-soft: 0 16px 40px rgba(36, 65, 104, 0.14);
            --shadow-hover: 0 24px 54px rgba(36, 65, 104, 0.18);
            --radius-xl: 28px;
            --radius-lg: 22px;
            --radius-md: 16px;
            --sidebar-width: 290px;
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
            color: var(--text-main);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(56, 149, 255, 0.26), transparent 26%),
                radial-gradient(circle at 84% 16%, rgba(15, 76, 150, 0.14), transparent 18%),
                linear-gradient(180deg, var(--bg-top) 0%, var(--bg-bottom) 44%, #eef5fb 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .staff-shell {
            min-height: 100vh;
            position: relative;
        }

        .staff-sidebar {
            position: fixed;
            top: 18px;
            left: 18px;
            bottom: 18px;
            width: var(--sidebar-width);
            z-index: 1100;
            transform: translateX(-120%);
            transition: transform .35s cubic-bezier(.4, 0, .2, 1);
        }

        .staff-sidebar.open {
            transform: translateX(0);
        }

        .staff-sidebar-panel {
            height: 100%;
            border-radius: 34px;
            padding: 26px 18px 18px;
            background: rgba(248, 250, 252, 0.97);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: 0 22px 44px rgba(22, 33, 51, 0.16);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .staff-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 84px;
        }

        .staff-logo img {
            max-width: 145px;
            height: auto;
            object-fit: contain;
        }

        .staff-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .staff-nav a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 15px 18px;
            border-radius: 18px;
            font-size: 15px;
            font-weight: 600;
            color: #324153;
            transition: .22s ease;
        }

        .staff-nav a:hover {
            background: rgba(227, 236, 247, 0.95);
            transform: translateX(4px);
        }

        .staff-nav a.active {
            background: var(--brand);
            color: #fff;
            box-shadow: 0 12px 28px rgba(15, 76, 150, 0.24);
        }

        .staff-nav i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            margin-top: auto;
        }

        .btn-logout {
            width: 100%;
            border: 0;
            border-radius: 16px;
            padding: 14px 18px;
            background: rgba(217, 75, 75, 0.12);
            color: var(--danger);
            font-weight: 700;
            transition: .22s ease;
        }

        .btn-logout:hover {
            background: rgba(217, 75, 75, 0.18);
        }

        .staff-overlay {
            position: fixed;
            inset: 0;
            background: rgba(14, 23, 38, 0.28);
            opacity: 0;
            visibility: hidden;
            transition: .25s ease;
            z-index: 1000;
        }

        .staff-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .staff-main {
            min-height: 100vh;
            padding: 22px 26px 40px;
        }

        .staff-topbar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 28px;
        }

        .topbar-group {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .menu-toggle {
            width: 48px;
            height: 48px;
            border: 0;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.76);
            color: #47617f;
            box-shadow: var(--shadow-soft);
            font-size: 24px;
            transition: .22s ease;
        }

        .menu-toggle:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-hover);
        }

        .brand-mark {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark img {
            width: 54px;
            height: 54px;
            object-fit: contain;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.76);
            padding: 8px;
            box-shadow: var(--shadow-soft);
        }

        .brand-mark h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .brand-mark p {
            margin: 4px 0 0;
            font-size: 13px;
            color: var(--text-muted);
        }

        .profile-chip {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.78);
            box-shadow: var(--shadow-soft);
        }

        .profile-chip strong {
            display: block;
            font-size: 14px;
            font-weight: 700;
        }

        .profile-chip span {
            display: block;
            font-size: 12px;
            color: var(--text-muted);
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--brand) 0%, #2475d0 100%);
            color: #fff;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 24px rgba(15, 76, 150, 0.28);
        }

        .page-wrap {
            max-width: 1260px;
            margin: 0 auto;
        }

        .page-hero {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 18px;
            margin-bottom: 24px;
            padding: 28px 30px;
            border-radius: var(--radius-xl);
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.88) 0%, rgba(238, 246, 255, 0.94) 100%);
            border: 1px solid rgba(255, 255, 255, 0.55);
            box-shadow: var(--shadow-soft);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            margin-bottom: 14px;
            border-radius: 999px;
            background: var(--brand-soft);
            color: var(--brand);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .page-hero h2 {
            margin: 0 0 8px;
            font-size: 34px;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .page-hero p {
            margin: 0;
            max-width: 760px;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .page-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .card-surface {
            border: 1px solid var(--border-soft);
            border-radius: var(--radius-lg);
            background: var(--surface);
            box-shadow: var(--shadow-soft);
        }

        .card-surface:hover {
            box-shadow: var(--shadow-hover);
        }

        .card-surface .card-body {
            padding: 24px;
        }

        .stat-card {
            height: 100%;
            padding: 24px;
        }

        .stat-card .label {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-card .value {
            font-size: 2.4rem;
            line-height: 1;
            margin-bottom: 14px;
            font-weight: 700;
            color: var(--brand-dark);
        }

        .section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .section-title h3,
        .section-title h4 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .section-title p,
        .section-title span {
            margin: 0;
            color: var(--text-muted);
            font-size: 14px;
        }

        .btn-brand,
        .btn-soft,
        .btn-success-soft,
        .btn-danger-soft,
        .btn-warning-soft {
            border: 0;
            border-radius: 14px;
            padding: 11px 18px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: .22s ease;
        }

        .btn-brand {
            background: linear-gradient(135deg, var(--brand) 0%, #1b64b9 100%);
            color: #fff;
            box-shadow: 0 14px 28px rgba(15, 76, 150, 0.24);
        }

        .btn-brand:hover {
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-soft {
            background: rgba(15, 76, 150, 0.10);
            color: var(--brand);
        }

        .btn-soft:hover {
            background: rgba(15, 76, 150, 0.16);
            color: var(--brand-dark);
        }

        .btn-soft.active,
        .show > .btn-soft {
            background: var(--brand);
            color: #fff;
            box-shadow: 0 14px 28px rgba(15, 76, 150, 0.20);
        }

        .btn-success-soft {
            background: rgba(30, 155, 90, 0.14);
            color: var(--success);
        }

        .btn-danger-soft {
            background: rgba(217, 75, 75, 0.14);
            color: var(--danger);
        }

        .btn-warning-soft {
            background: rgba(214, 139, 0, 0.14);
            color: var(--warning);
        }

        .form-label {
            font-weight: 700;
            color: #30445c;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select,
        textarea.form-control {
            border-radius: 14px;
            border: 1px solid #d8e3ef;
            padding: 12px 14px;
            background: #fbfdff;
        }

        .form-control:focus,
        .form-select:focus,
        textarea.form-control:focus {
            border-color: rgba(15, 76, 150, 0.45);
            box-shadow: 0 0 0 4px rgba(15, 76, 150, 0.10);
            background: #fff;
        }

        .table-shell {
            overflow: hidden;
        }

        .table-responsive {
            margin: 0;
        }

        .table-modern {
            margin: 0;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, var(--brand) 0%, #1c66bc 100%);
            color: #fff;
            border: 0;
            padding: 18px 18px;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .table-modern tbody td {
            padding: 18px;
            border-color: rgba(216, 227, 239, 0.9);
            vertical-align: middle;
            color: #314155;
            background: rgba(255, 255, 255, 0.84);
        }

        .table-modern tbody tr:hover td {
            background: #f4f9ff;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: 0;
        }

        .info-stack {
            display: grid;
            gap: 16px;
        }

        .info-tile {
            padding: 18px 20px;
            border-radius: 18px;
            background: var(--surface-muted);
            border: 1px solid rgba(216, 227, 239, 0.95);
        }

        .info-tile strong {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        .info-tile span,
        .info-tile div {
            color: var(--text-main);
        }

        .status-badge,
        .priority-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pending {
            background: rgba(214, 139, 0, 0.14);
            color: var(--warning);
        }

        .status-progress {
            background: rgba(15, 76, 150, 0.14);
            color: var(--brand);
        }

        .status-resolved,
        .status-approved {
            background: rgba(30, 155, 90, 0.14);
            color: var(--success);
        }

        .status-rejected {
            background: rgba(217, 75, 75, 0.14);
            color: var(--danger);
        }

        .priority-high {
            background: rgba(217, 75, 75, 0.14);
            color: var(--danger);
        }

        .priority-medium {
            background: rgba(214, 139, 0, 0.14);
            color: var(--warning);
        }

        .action-cluster {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }

        .action-cluster form {
            margin: 0;
        }

        .table-note {
            color: var(--text-muted);
            font-size: 13px;
        }

        .empty-state {
            padding: 42px 18px;
            text-align: center;
            color: var(--text-muted);
        }

        .modal-content {
            border: 0;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 26px 50px rgba(29, 49, 78, 0.22);
        }

        .modal-header {
            border-bottom: 1px solid rgba(216, 227, 239, 0.9);
            padding: 20px 22px;
        }

        .modal-body {
            padding: 22px;
        }

        .modal-footer {
            border-top: 1px solid rgba(216, 227, 239, 0.9);
            padding: 18px 22px;
        }

        @media (max-width: 992px) {
            .staff-main {
                padding: 18px 14px 32px;
            }

            .staff-topbar,
            .page-hero {
                flex-direction: column;
            }

            .page-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .brand-mark h1 {
                font-size: 18px;
            }

            .page-hero {
                padding: 22px 20px;
            }

            .page-hero h2 {
                font-size: 28px;
            }

            .card-surface .card-body,
            .stat-card {
                padding: 20px;
            }
        }
    </style>
    @yield('head')
</head>
<body>
@php($activeNav = $activeNav ?? 'dashboard')
<div class="staff-shell">
    <aside class="staff-sidebar" id="staffSidebar">
        <div class="staff-sidebar-panel">
            <div class="staff-logo">
                <img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo">
            </div>

            <nav class="staff-nav">
                @if(in_array(auth('staff')->user()?->role, ['radiology_lab', 'radiology', 'laboratory', 'lab'], true))
                <a href="{{ route('staff.radiology_lab') }}" class="{{ $activeNav === 'radiology_lab' ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Diagnostics Dashboard</span>
                </a>
                    @if(in_array(auth('staff')->user()?->role, ['radiology_lab', 'laboratory', 'lab'], true))
                    <a href="{{ route('staff.radiology_lab', ['section' => 'laboratory']) }}" class="">
                        <i class="bi bi-prescription2"></i>
                        <span>Lab Requests</span>
                    </a>
                    @endif
                    @if(in_array(auth('staff')->user()?->role, ['radiology_lab', 'radiology'], true))
                    <a href="{{ route('staff.radiology_lab', ['section' => 'radiology']) }}" class="">
                        <i class="bi bi-image"></i>
                        <span>Radiology Requests</span>
                    </a>
                    @endif
                @else
                <a href="{{ route('staff.dashboard') }}" class="{{ $activeNav === 'dashboard' ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('staff.jobs.index') }}" class="{{ $activeNav === 'jobs' ? 'active' : '' }}">
                    <i class="bi bi-briefcase"></i>
                    <span>Manage Jobs</span>
                </a>
                <a href="{{ route('staff.job.applications') }}" class="{{ $activeNav === 'applications' ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Job Applications</span>
                </a>
                <a href="{{ route('staff.training.programs') }}" class="{{ $activeNav === 'training' ? 'active' : '' }}">
                    <i class="bi bi-mortarboard"></i>
                    <span>Training Programs</span>
                </a>
                <a href="{{ route('staff.complaints') }}" class="{{ $activeNav === 'complaints' ? 'active' : '' }}">
                    <i class="bi bi-chat-left-text"></i>
                    <span>Complaints</span>
                </a>
                @endif
            </nav>

            <div class="sidebar-footer">
                <form method="POST" action="{{ route('staff.logout') }}">
                    @csrf
                    <button class="btn-logout" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </aside>

    <div class="staff-overlay" id="staffOverlay"></div>

    <div class="staff-main">
        <div class="staff-topbar">
            <div class="topbar-group">
                <button type="button" class="menu-toggle" id="staffMenuToggle" aria-label="Toggle navigation">
                    <i class="bi bi-list"></i>
                </button>

                <div class="brand-mark">
                    <img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo">
                    <div>
                        <h1>NUH Staff Workspace</h1>
                        <p>{{ in_array(auth('staff')->user()?->role, ['radiology_lab', 'radiology', 'laboratory', 'lab'], true) ? 'Diagnostic request and result workflow.' : 'Consistent staff tools aligned with the main hospital interface.' }}</p>
                    </div>
                </div>
            </div>

            <div class="profile-chip">
                <div>
                    <strong>Staff Team</strong>
                    <span>Operational Review Panel</span>
                </div>
                <span class="avatar">S</span>
            </div>
        </div>

        <div class="page-wrap">
            @yield('hero')

            @if(session('success'))
                <div class="alert alert-success card-surface border-0 mb-4">{{ session('success') }}</div>
            @endif

            @if(session('ok'))
                <div class="alert alert-success card-surface border-0 mb-4">{{ session('ok') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger card-surface border-0 mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

@yield('modals')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const staffSidebar = document.getElementById('staffSidebar');
    const staffOverlay = document.getElementById('staffOverlay');
    const staffMenuToggle = document.getElementById('staffMenuToggle');

    if (staffMenuToggle && staffSidebar && staffOverlay) {
        const closeStaffSidebar = () => {
            staffSidebar.classList.remove('open');
            staffOverlay.classList.remove('show');
        };

        staffMenuToggle.addEventListener('click', () => {
            staffSidebar.classList.toggle('open');
            staffOverlay.classList.toggle('show');
        });

        staffOverlay.addEventListener('click', closeStaffSidebar);
    }
</script>
@yield('scripts')
</body>
</html>
