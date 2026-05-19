<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
    :root {
        --primary: #114a9f;
        --primary-dark: #0a3d86;
        --text: #2f3a45;
        --muted: #6b7280;
        --danger: #ff3b3f;
        --radius-xl: 28px;
        --radius-lg: 22px;
        --sidebar-width: 280px;
        --card-bg: rgba(248, 248, 248, 0.96);
        --shadow: 0 14px 28px rgba(34, 52, 84, 0.16);
        --shadow-hover: 0 20px 38px rgba(34, 52, 84, 0.22);
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
        font-family: 'Inter', Arial, sans-serif;
        color: var(--text);
        background:
            radial-gradient(circle at 78% 55%, rgba(52, 160, 255, 0.95) 0%, rgba(122, 191, 255, 0.75) 18%, rgba(199, 224, 245, 0.62) 42%, rgba(237, 241, 245, 0.95) 72%, #f1f1f1 100%);
        min-height: 100vh;
        overflow-x: hidden;
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
    @endphp

    /* page loader */
    #page-loader {
        position: fixed;
        inset: 0;
        background: #0a3d86;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 18px;
        transition: opacity .45s ease, visibility .45s ease;
    }

    #page-loader.hidden {
        opacity: 0;
        visibility: hidden;
    }

    .loader-ring {
        width: 54px;
        height: 54px;
        border: 4px solid rgba(255, 255, 255, .18);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.9s linear infinite;
    }

    .loader-text {
        color: rgba(255, 255, 255, .78);
        font-size: 13px;
        letter-spacing: 2px;
        font-weight: 600;
        animation: pulseText 1.2s ease-in-out infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    @keyframes pulseText {

        0%,
        100% {
            opacity: .4;
        }

        50% {
            opacity: 1;
        }
    }

    .dashboard-page {
        min-height: 100vh;
        position: relative;
    }

    /* sidebar */
    .sidebar {
        width: var(--sidebar-width);
        padding: 18px 16px;
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
        background: rgba(247, 247, 247, 0.98);
        border-radius: 30px;
        box-shadow: 0 18px 36px rgba(0, 0, 0, .14);
        display: flex;
        flex-direction: column;
        padding: 26px 18px 18px;
        backdrop-filter: blur(2px);
    }

    .sidebar-top-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 24px;
        min-height: 76px;
    }

    .sidebar-top-logo img {
        max-width: 138px;
        height: auto;
        object-fit: contain;
    }

    .menu {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .menu li a {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 15px 18px;
        border-radius: 18px;
        font-size: 16px;
        color: #2e3844;
        transition: .25s ease;
        position: relative;
        overflow: hidden;
    }

    .menu li a::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent 30%, rgba(255, 255, 255, .38) 50%, transparent 70%);
        transform: translateX(-100%);
        transition: transform .45s ease;
    }

    .menu li a:hover::before {
        transform: translateX(100%);
    }

    .menu li a:hover {
        background: #edf3fb;
        transform: translateX(4px);
    }

    .menu li.active a {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 10px 20px rgba(17, 74, 159, .26);
    }

    .menu li.active a::before {
        display: none;
    }

    .menu i {
        width: 22px;
        text-align: center;
        font-size: 20px;
        transition: .2s ease;
    }

    .menu li a:hover i {
        transform: scale(1.12);
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
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logout-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .18), transparent);
        transform: translateX(-100%);
        transition: transform .4s ease;
    }

    .logout-btn:hover::before {
        transform: translateX(100%);
    }

    .logout-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(255, 59, 63, .30);
    }

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

    .main-content {
        min-height: 100vh;
        padding: 26px 28px 42px;
        position: relative;
        z-index: 1;
    }

    .topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        padding: 0 0 18px;
        animation: slideDown .6s ease both;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-18px);
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
        border: none;
        background: transparent;
        transition: .25s ease;
    }

    .menu-btn:hover {
        background: rgba(255, 255, 255, .45);
        transform: scale(1.08) rotate(90deg);
    }

    .brand-block h1 {
        margin: 0;
        font-size: 21px;
        font-weight: 600;
    }

    .brand-block p {
        margin: 6px 0 0;
        font-size: 14px;
        color: #4b5563;
    }

    .right-section {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .search-wrap {
        position: relative;
        width: 322px;
        max-width: 100%;
    }

    .search-input {
        width: 100%;
        height: 46px;
        padding: 0 48px 0 18px;
        border: none;
        outline: none;
        border-radius: 18px;
        background: #f8f8f8;
        box-shadow: var(--shadow);
        font-size: 15px;
        transition: .25s ease;
    }

    .search-input:focus {
        background: #fff;
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .search-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 22px;
    }

    .icon-btn {
        width: 46px;
        height: 46px;
        border: none;
        border-radius: 16px;
        background: #f8f8f8;
        box-shadow: var(--shadow);
        color: #4b5563;
        font-size: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: .25s ease;
        position: relative;
    }

    .icon-btn:hover {
        transform: translateY(-3px);
        background: #fff;
    }

    .profile-link {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: .2s ease;
    }

    .profile-link:hover {
        transform: translateY(-2px);
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
        border-radius: 50%;
        background: #124d9d;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        animation: avatarPulse 3s ease-in-out infinite;
    }

    @keyframes avatarPulse {

        0%,
        100% {
            box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        }

        50% {
            box-shadow: 0 10px 28px rgba(17, 74, 159, .42);
        }
    }

    .content {
        max-width: 1090px;
        margin: 10px auto 0;
        padding-bottom: 36px;
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

    .welcome {
        background: var(--card-bg);
        border-radius: 28px;
        box-shadow: var(--shadow);
        padding: 32px 34px;
        margin-bottom: 34px;
        animation-delay: .1s;
    }

    .welcome h2 {
        margin: 0 0 10px;
        font-size: 22px;
        font-weight: 700;
    }

    .welcome p {
        margin: 0;
        color: #64748b;
        font-size: 16px;
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 34px;
    }

    .stat-box {
        background: var(--card-bg);
        border-radius: 26px;
        box-shadow: var(--shadow);
        padding: 26px 24px;
        transition: .28s ease;
    }

    .stat-box:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-hover);
    }

    .stat-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .stat-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        background: #124d9d;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        transition: .25s ease;
    }

    .stat-box:hover .stat-icon {
        transform: rotate(-8deg) scale(1.08);
    }

    .stat-badge {
        font-size: 13px;
        font-weight: 700;
        color: #114a9f;
        background: #e8f0ff;
        padding: 6px 10px;
        border-radius: 999px;
        transition: .25s ease;
    }

    .stat-box:hover .stat-badge {
        background: #114a9f;
        color: #fff;
    }

    .stat-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stat-text {
        color: #5a6675;
        font-size: 14px;
        margin-bottom: 14px;
    }

    .stat-number {
        font-size: 42px;
        font-weight: 800;
        color: #114a9f;
        line-height: 1;
        animation: fadeScale .55s ease;
    }

    @keyframes fadeScale {
        from {
            opacity: 0;
            transform: scale(.92);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .table-card {
        background: var(--card-bg);
        border-radius: 26px;
        box-shadow: var(--shadow);
        padding: 20px 18px 10px;
        transition: .25s ease;
    }

    .table-card:hover {
        box-shadow: var(--shadow-hover);
    }

    .table-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 18px 8px;
        color: #2f3943;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 760px;
    }

    th,
    td {
        padding: 18px 16px;
        text-align: left;
        vertical-align: middle;
    }

    thead th {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        border-bottom: 1px solid #cdd2d7;
    }

    tbody tr {
        border-bottom: 1px solid #d8dde3;
        transition: .22s ease;
    }

    tbody tr:hover {
        background: #f3f8ff;
        transform: scale(1.003);
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

    @media (max-width: 900px) {
        .stats {
            grid-template-columns: repeat(2, 1fr);
        }

        .topbar {
            flex-direction: column;
            align-items: stretch;
        }

        .right-section {
            justify-content: space-between;
        }
    }

    @media (max-width: 620px) {
        .stats {
            grid-template-columns: 1fr;
        }

        .main-content {
            padding: 18px 16px 28px;
        }

        .right-section {
            flex-direction: column;
            align-items: stretch;
        }

        .search-wrap {
            width: 100%;
        }

        .welcome,
        .stat-box,
        .table-card {
            border-radius: 22px;
        }
    }
    </style>
</head>

<body>

    <div id="page-loader">
        <div class="loader-ring"></div>
        <div class="loader-text">NUH DOCTOR</div>
    </div>

    <div class="dashboard-page">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-panel">
                <div class="sidebar-top-logo">
                    <img src="{{ asset('images/logo_Image.png') }}" alt="NUH Logo">
                </div>

                <ul class="menu">
                    <li class="active">
                        <a href="{{ route('doctor.profile') }}">
                            <i class="bi bi-grid-1x2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
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
                    <a href="{{ url('/') }}" class="logout-btn">Log out</a>
                </div>
            </div>
        </aside>

        <div class="overlay" id="overlay"></div>

        <main class="main-content">
            <header class="topbar">
                <div class="left-section">
                    <button class="menu-btn" id="menuBtn" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="brand-block">
                        <h1>NUH</h1>
                        <p>Doctor Dashboard</p>
                    </div>
                </div>

                <div class="right-section">
                    <div class="search-wrap">
                        <input class="search-input" id="searchInput" type="text" placeholder="Search patients...">
                        <i class="bi bi-search search-icon"></i>
                    </div>

                    <button class="icon-btn" type="button">
                        <i class="bi bi-bell"></i>
                    </button>

                    <div class="profile-link">
                        <div class="profile-info">
                            <strong>{{ $doctor->name }}</strong>
                            <span>{{ $doctor->department->name_en ?? 'Doctor' }}</span>
                        </div>
                        <div class="avatar">
                            {{ strtoupper(substr($doctor->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <section class="content">

                <div class="welcome reveal">
                    <h2>Welcome, Dr. {{ $doctor->name }}</h2>
                    <p>Department: {{ $doctor->department->name_en ?? 'No Department' }}</p>
                </div>

                <div class="stats">
                    <div class="stat-box reveal">
                        <div class="stat-head">
                            <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
                            <div class="stat-badge">Today</div>
                        </div>
                        <div class="stat-title">Today's Appointments</div>
                        <div class="stat-text">Appointments scheduled for today</div>
                        <div class="stat-number">{{ $todayAppointmentsCount ?? 0 }}</div>
                    </div>

                    <div class="stat-box reveal">
                        <div class="stat-head">
                            <div class="stat-icon"><i class="bi bi-calendar-event"></i></div>
                            <div class="stat-badge">Total</div>
                        </div>
                        <div class="stat-title">Total Appointments</div>
                        <div class="stat-text">All appointments assigned to you</div>
                        <div class="stat-number">{{ $appointmentsCount ?? 0 }}</div>
                    </div>

                    <div class="stat-box reveal">
                        <div class="stat-head">
                            <div class="stat-icon"><i class="bi bi-people"></i></div>
                            <div class="stat-badge">Patients</div>
                        </div>
                        <div class="stat-title">Total Patients</div>
                        <div class="stat-text">Patients you handled recently</div>
                        <div class="stat-number">{{ $patientsCount ?? 0 }}</div>
                    </div>
                </div>

                <div class="table-card reveal">
                    <div class="table-title">Recent Patients</div>

                    <div class="table-responsive">
                        <table id="patientsTable">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Patient Email</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAppointments as $appointment)
                                @php $canComplete = strtolower((string) $appointment->status) === 'confirmed'; @endphp
                                <tr>
                                    <td>{{ $appointment->patient?->full_name ?? trim(($appointment->first_name ?? '') . ' ' . ($appointment->last_name ?? '')) ?: 'N/A' }}</td>
                                    <td>{{ $appointment->patient?->email ?? $appointment->email ?? 'N/A' }}</td>
                                    <td>{{ $appointment->reason ?? 'N/A' }}</td>
                                    <td>{{ $appointment->status ?? 'Pending' }}</td>
                                    <td>
                                        @if($canComplete)
                                            <form method="POST" action="{{ route('doctor.appointments.complete', $appointment) }}">
                                                @csrf
                                                <button type="submit" class="complete-btn">Mark Completed</button>
                                            </form>
                                        @else
                                            <span class="complete-muted">{{ $appointment->isCompleted() ? 'Completed' : 'Not ready' }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" style="text-align:center;">No recent patients found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="table-card reveal" style="margin-top: 24px; display: none;" hidden aria-hidden="true">
                    <div class="table-title">Latest Patient Feedback</div>

                    <div class="table-responsive">
                        <table id="ratingsTable">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ratings as $rating)
                                @php
                                    $patientName = $rating->patient?->full_name
                                        ?? $rating->appointment?->patient?->full_name
                                        ?? trim((($rating->appointment?->first_name ?? '') . ' ' . ($rating->appointment?->last_name ?? '')))
                                        ?: 'N/A';
                                    $stars = str_repeat('★', (int) $rating->rating) . str_repeat('☆', 5 - (int) $rating->rating);
                                @endphp
                                <tr>
                                    <td>{{ $patientName }}</td>
                                    <td class="rating-stars">{{ $stars }}</td>
                                    <td class="rating-comment">{{ $rating->comment ?: 'Patient left a star rating without a written comment.' }}</td>
                                    <td>{{ $rating->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="empty-state">No ratings submitted yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </main>
    </div>

    <script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.getElementById('page-loader').classList.add('hidden');
        }, 600);
    });

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuBtn = document.getElementById('menuBtn');

    menuBtn.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
        const icon = menuBtn.querySelector('i');
        icon.className = sidebar.classList.contains('active') ? 'bi bi-x-lg' : 'bi bi-list';
    });

    overlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
        menuBtn.querySelector('i').className = 'bi bi-list';
    });

    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('#patientsTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });

    const revealObserver = new IntersectionObserver(entries => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                entry.target.style.transitionDelay = (index * 0.08) + 's';
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15
    });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
    </script>

</body>

</html>
