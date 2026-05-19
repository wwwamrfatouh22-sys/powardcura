<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>NUH Admin - Rooms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Inter, Arial, sans-serif;
    }

    html {
        scroll-behavior: smooth;
    }

    :root {
        --primary: #114a9f;
        --primary-dark: #0b3f8c;
        --text-dark: #1f2937;
        --text-muted: #6b7280;
        --danger: #ff3b3b;
        --shadow-soft: 0 12px 28px rgba(34, 52, 84, 0.16);
        --shadow-card: 0 18px 36px rgba(0, 0, 0, 0.14);
    }

    body {
        background:
            radial-gradient(circle at 75% 52%, rgba(43, 154, 255, .92) 0%, rgba(107, 188, 255, .72) 23%, rgba(200, 225, 245, .60) 44%, rgba(236, 240, 244, .92) 70%, #f1f1f1 100%);
        min-height: 100vh;
        color: var(--text-dark);
        overflow-x: hidden;
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 18px;
        left: 18px;
        width: 280px;
        height: calc(100vh - 36px);
        z-index: 1100;
        transform: translateX(-120%);
        transition: transform .35s cubic-bezier(.4, 0, .2, 1);
    }

    .sidebar.open {
        transform: translateX(0);
    }

    .sidebar-panel {
        height: 100%;
        background: rgba(247, 247, 247, 0.97);
        border-radius: 34px;
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.14);
        padding: 28px 18px 18px;
        display: flex;
        flex-direction: column;
        backdrop-filter: blur(2px);
    }

    .sidebar-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 86px;
        margin-bottom: 24px;
    }

    .sidebar-logo img {
        max-width: 145px;
        height: auto;
        object-fit: contain;
    }

    .sidebar-nav {
        margin-top: 8px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sidebar-nav a {
        display: flex;
        align-items: center;
        gap: 14px;
        color: #2f3947;
        padding: 16px 18px;
        border-radius: 18px;
        font-size: 16px;
        font-weight: 500;
        transition: .22s ease;
        position: relative;
        overflow: hidden;
    }

    .sidebar-nav a::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg,
                transparent 30%,
                rgba(255, 255, 255, 0.35) 50%,
                transparent 70%);
        transform: translateX(-100%);
        transition: transform .45s ease;
        pointer-events: none;
    }

    .sidebar-nav a:hover::before {
        transform: translateX(100%);
    }

    .sidebar-nav a:hover {
        background: #eceff3;
        color: #2e3844;
        transform: translateX(4px);
    }

    .sidebar-nav a.active,
    .sidebar-nav a.active:hover {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 10px 22px rgba(17, 74, 159, .26);
        transform: none;
    }

    .sidebar-nav a.active::before {
        display: none;
    }

    .sidebar-nav a i {
        width: 24px;
        text-align: center;
        font-size: 20px;
        transition: .22s ease;
    }

    .sidebar-nav a:hover i {
        transform: scale(1.12);
    }

    .sidebar-nav a.active i {
        transform: none;
    }

    .logout-wrap {
        margin-top: auto;
        padding-top: 18px;
    }

    .logout-btn {
        display: flex;
        width: 100%;
        border: none;
        background: var(--danger);
        color: #fff;
        padding: 16px 18px;
        border-radius: 14px;
        font-size: 18px;
        font-weight: 700;
        align-items: center;
        justify-content: center;
        transition: .25s ease;
        position: relative;
        overflow: hidden;
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
        box-shadow: 0 10px 20px rgba(255, 59, 63, .3);
    }

    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.22);
        opacity: 0;
        visibility: hidden;
        transition: 0.3s ease;
        z-index: 1000;
    }

    .overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .main {
        min-height: 100vh;
        padding: 22px 28px 40px;
    }

    .topbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 40px;
        animation: slideDown .6s ease both;
    }

    .topbar-left {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .brand-box img {
        max-width: 120px;
        height: auto;
        object-fit: contain;
    }


    .menu-toggle {
        width: 46px;
        height: 46px;
        border: none;
        background: transparent;
        font-size: 28px;
        color: #4b5563;
        cursor: pointer;
        border-radius: 12px;
        transition: .25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .menu-toggle:hover {
        background: rgba(255, 255, 255, .4);
        transform: scale(1.08) rotate(90deg);
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .search-wrapper {
        position: relative;
    }

    .search-box {
        width: 320px;
        height: 50px;
        border: none;
        border-radius: 18px;
        background: #fff;
        padding: 0 48px 0 18px;
        font-size: 15px;
        box-shadow: var(--shadow-soft);
        transition: .3s ease;
    }

    .search-box:focus {
        outline: none;
        transform: translateY(-2px);
        box-shadow: 0 16px 32px rgba(34, 52, 84, 0.22);
    }

    .search-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 18px;
    }

    .icon-btn {
        width: 46px;
        height: 46px;
        border: none;
        border-radius: 16px;
        background: #fff;
        box-shadow: var(--shadow-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4b5563;
        font-size: 18px;
        cursor: pointer;
        transition: .25s ease;
    }

    .icon-btn:hover {
        transform: translateY(-3px);
        background: #fff;
    }

    .admin-box {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .admin-info {
        text-align: right;
        line-height: 1.2;
    }

    .admin-info .name {
        font-size: 15px;
        color: #1f2937;
        font-weight: 600;
    }

    .admin-info .role {
        font-size: 14px;
        color: #6b7280;
    }

    .avatar {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        background: #0b4aa7;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        animation: avatarPulse 3s ease-in-out infinite;
        transition: .25s ease;
    }

    .avatar:hover {
        transform: scale(1.1) rotate(5deg);
    }

    .page-header {
        max-width: 1150px;
        margin: 0 auto 22px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .page-title {
        opacity: 0;
        transform: translateY(24px);
    }

    .page-title.visible {
        animation: fadeUp .65s ease forwards;
    }

    .page-title h2 {
        font-size: 20px;
        font-weight: 600;
        margin: 0 0 6px;
    }

    .page-title p {
        font-size: 14px;
        color: #4b5563;
        margin: 0;
    }

    .add-btn {
        background: var(--primary);
        color: #fff;
        padding: 12px 28px;
        border-radius: 16px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 10px 18px rgba(15, 74, 162, 0.18);
        transition: 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .add-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .18), transparent);
        transform: translateX(-100%);
        transition: transform .4s ease;
    }

    .add-btn:hover::before {
        transform: translateX(100%);
    }

    .add-btn:hover {
        background: var(--primary-dark);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(17, 74, 159, .3);
    }

    .card-table {
        max-width: 1150px;
        margin: 0 auto;
        background: rgba(248, 248, 248, .98);
        border-radius: 26px;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
        opacity: 0;
        transform: translateY(28px);
        transition: .3s ease;
    }

    .card-table.visible {
        animation: fadeUp .75s ease forwards;
    }

    .card-table:hover {
        box-shadow: 0 22px 44px rgba(34, 52, 84, 0.22);
    }

    .custom-table {
        margin: 0;
    }

    .custom-table thead th {
        background: var(--primary);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        padding: 18px 20px;
        border: none;
        white-space: nowrap;
    }

    .custom-table tbody tr {
        animation: rowEnter .55s ease both;
    }

    .custom-table tbody tr:nth-child(1) {
        animation-delay: .04s;
    }

    .custom-table tbody tr:nth-child(2) {
        animation-delay: .08s;
    }

    .custom-table tbody tr:nth-child(3) {
        animation-delay: .12s;
    }

    .custom-table tbody tr:nth-child(4) {
        animation-delay: .16s;
    }

    .custom-table tbody tr:nth-child(5) {
        animation-delay: .20s;
    }

    .custom-table tbody tr:nth-child(6) {
        animation-delay: .24s;
    }

    .custom-table tbody tr:nth-child(7) {
        animation-delay: .28s;
    }

    .custom-table tbody tr:nth-child(8) {
        animation-delay: .32s;
    }

    .custom-table tbody td {
        padding: 18px 20px;
        font-size: 14px;
        color: #374151;
        vertical-align: middle;
        border-color: #e6e6e6;
        background: rgba(248, 248, 248, .98);
        transition: .25s ease;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    .custom-table tbody tr:hover td {
        background: #f2f7ff;
        transform: scale(1.002);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 88px;
        padding: 7px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
    }

    .status-occupied {
        background: #1560c4;
        color: #fff;
    }

    .status-available {
        background: #0f4aa2;
        color: #fff;
    }

    .status-maintenance {
        background: #dbe4f0;
        color: #1f2937;
    }

    .actions-cell {
        white-space: nowrap;
    }

    .action-link,
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border: none;
        background: transparent;
        color: #374151;
        border-radius: 8px;
        transition: 0.2s ease;
        text-decoration: none;
    }

    .action-link:hover,
    .action-btn:hover {
        background: #f3f4f6;
        color: var(--primary);
        transform: scale(1.08);
    }

    .action-btn {
        padding: 0;
    }

    .alert {
        max-width: 1150px;
        margin: 0 auto 16px;
        border-radius: 14px;
        animation: fadeUp .5s ease;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes rowEnter {
        from {
            opacity: 0;
            transform: translateY(16px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
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

    @keyframes avatarPulse {

        0%,
        100% {
            box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        }

        50% {
            box-shadow: 0 10px 26px rgba(17, 74, 159, .42);
        }
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

    @media (max-width: 992px) {
        .topbar {
            flex-direction: column;
            align-items: stretch;
        }

        .topbar-right {
            justify-content: flex-end;
        }

        .page-header,
        .card-table,
        .alert {
            max-width: 100%;
        }

        .card-table {
            overflow-x: auto;
        }

        .custom-table {
            min-width: 1050px;
        }
    }

    @media (max-width: 768px) {
        .main {
            padding: 18px 14px 30px;
        }

        .search-box {
            width: 100%;
            min-width: 230px;
        }

        .topbar-right {
            width: 100%;
            justify-content: space-between;
        }

        .search-wrapper {
            flex: 1;
            min-width: 100%;
            order: 1;
        }

        .sidebar {
            width: 260px;
            left: 10px;
            top: 10px;
            height: calc(100vh - 20px);
        }
    }
        .settings-link { background: var(--primary); color: #fff; }
    .settings-link:hover { background: var(--primary-dark); color: #fff; }
    </style>
</head>

<body>

    <div class="sidebar" id="sidebar">
    <div class="sidebar-panel">
        <div class="sidebar-logo"><img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo"></div>
        <div class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a>
            <a href="{{ route('admin.appointments') }}" class="{{ request()->routeIs('admin.appointments*') ? 'active' : '' }}"><i class="fa-regular fa-calendar"></i><span>Appointments</span></a>
            <a href="{{ route('admin.doctors') }}" class="{{ request()->routeIs('admin.doctors*') ? 'active' : '' }}"><i class="fa-solid fa-stethoscope"></i><span>Doctors</span></a>
            <a href="{{ route('admin.patients') }}" class="{{ request()->routeIs('admin.patients*') ? 'active' : '' }}"><i class="fa-solid fa-user-group"></i><span>Patients</span></a>
            <a href="{{ route('admin.staff') }}" class="{{ request()->routeIs('admin.staff*') ? 'active' : '' }}"><i class="fa-solid fa-user-tie"></i><span>Staff</span></a>
            <a href="{{ route('admin.rooms') }}" class="{{ request()->routeIs('admin.rooms*') ? 'active' : '' }}"><i class="fa-regular fa-hospital"></i><span>Rooms</span></a>
            <a href="{{ route('admin.departments') }}" class="{{ request()->routeIs('admin.departments*') ? 'active' : '' }}"><i class="fa-regular fa-building"></i><span>Departments</span></a>
        </div>
        <div class="logout-wrap"><a href="{{ url('/') }}" class="logout-btn">Log out</a></div>
    </div>
</div><div class="overlay" id="overlay"></div>

    <div class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="brand-box">
                    <img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo">

                </div>
            </div>

            <div class="topbar-right">
                <div class="search-wrapper">
                    <input id="searchInput" type="text" class="search-box" placeholder="Search rooms...">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                </div>

                <button class="icon-btn" type="button"><i class="fa-regular fa-bell"></i></button>
                <a href="{{ route('admin.settings') }}" class="icon-btn settings-link" title="Settings" aria-label="Settings"><i class="fa-solid fa-gear"></i></a>
                <div class="admin-box">
                    <div class="admin-info">
                        <div class="name">Admin Robert</div>
                        <div class="role">Administrator</div>
                    </div>
                    <div class="avatar">R</div>
                </div>
            </div>
        </div>

        <div class="page-header">
            <div class="page-title reveal">
                <h2>Rooms Management</h2>
                <p>Manage and track all hospital rooms</p>
            </div>

            <a href="{{ route('admin.rooms.create') }}" class="add-btn">
                <i class="fa-solid fa-plus"></i>
                <span>Add New Room</span>
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card-table reveal">
            <x-table-filters
                :action="route('admin.rooms')"
                :type-options="['ICU' => 'ICU', 'Private Room' => 'Private Room', 'General Ward' => 'General Ward', 'Operating Room' => 'Operating Room', 'Emergency' => 'Emergency']"
                :status-options="['available' => 'Available', 'occupied' => 'Occupied', 'maintenance' => 'Maintenance']"
                type-label="Room Type" />
            <table class="table custom-table align-middle">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Room Type</th>
                        <th>Floor</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Current Patient</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($rooms as $room)
                    <tr>
                        <td>{{ $room->room_number }}</td>
                        <td>{{ $room->type }}</td>
                        <td>Floor {{ $room->floor }}</td>
                        <td>{{ $room->capacity }} beds</td>

                        <td>
                            @php $status = strtolower($room->status); @endphp

                            @if($status === 'occupied')
                            <span class="status-badge status-occupied">Occupied</span>
                            @elseif($status === 'available')
                            <span class="status-badge status-available">Available</span>
                            @else
                            <span class="status-badge status-maintenance">Maintenance</span>
                            @endif
                        </td>

                        <td>{{ $room->patient->full_name ?? $room->current_patient ?? '-' }}</td>

                        <td class="actions-cell">
                            <a href="{{ route('admin.rooms.edit', $room->id) }}" class="action-link">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>

                            <form action="{{ route('admin.rooms.delete', $room->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" onclick="return confirm('Delete room #{{ $room->id }} only?')">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="table-filter-pagination px-4 pb-4">
                {{ $rooms->links() }}
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', function() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });

    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll('.custom-table tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(value) ? '' : 'none';
        });
    });

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
    </script>

</body>

</html>
