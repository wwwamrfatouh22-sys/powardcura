<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>NUH Admin - Departments</title>
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
        gap: 14px;
        flex-wrap: wrap;
    }

    .search-wrapper {
        position: relative;
    }

    .search-box {
        width: 320px;
        height: 46px;
        border: none;
        border-radius: 18px;
        background: #fff;
        padding: 0 48px 0 20px;
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
        width: 44px;
        height: 44px;
        border: none;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #374151;
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
        gap: 12px;
    }

    .admin-info {
        text-align: left;
        line-height: 1.25;
    }

    .admin-info .name {
        font-size: 15px;
        color: #111827;
        font-weight: 600;
    }

    .admin-info .role {
        font-size: 14px;
        color: #6b7280;
    }

    .avatar {
        width: 44px;
        height: 44px;
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
        max-width: 1135px;
        margin: 0 auto 28px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
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
        padding: 13px 26px;
        border-radius: 18px;
        font-weight: 500;
        font-size: 16px;
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
        max-width: 1135px;
        margin: 0 auto;
        background: rgba(248, 248, 248, .98);
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 16px 34px rgba(0, 0, 0, 0.14);
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
        padding: 16px 20px;
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
        color: #2f3947;
        vertical-align: middle;
        border: none;
        background: rgba(248, 248, 248, .98);
        transition: .25s ease;
    }

    .custom-table tbody tr:hover td {
        background: #f2f7ff;
        transform: scale(1.002);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 92px;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-active {
        background: #1560c4;
        color: #fff;
    }

    .status-inactive {
        background: #d93025;
        color: #fff;
    }

    .actions-cell {
        white-space: nowrap;
        text-align: center;
    }

    .department-cell {
        min-width: 260px;
    }

    .department-stack {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .department-title {
        font-size: 16px;
        font-weight: 700;
        color: #14213d;
    }

    .department-meta {
        display: inline-flex;
        width: fit-content;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: #5b6573;
        background: #edf4ff;
        border-radius: 999px;
        padding: 6px 10px;
    }

    .department-row td {
        background: #eef4ff !important;
        border-bottom: 1px solid #dbe8fb;
    }

    .department-row:hover td {
        background: #e7f0ff !important;
        transform: none !important;
    }

    .doctor-row td {
        background: rgba(248, 248, 248, .98);
    }

    .doctor-label-cell {
        padding-left: 34px !important;
    }

    .doctor-indent {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: #47607d;
        font-size: 13px;
        font-weight: 600;
    }

    .doctor-indent::before {
        content: "";
        width: 18px;
        height: 2px;
        border-radius: 999px;
        background: #9bb6df;
    }

    .doctor-name {
        display: block;
        color: #1f2937;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .doctor-meta {
        display: block;
        color: #6b7280;
        font-size: 12px;
    }

    .doctor-empty {
        color: #6b7280;
        font-style: italic;
    }

    .head-cell strong {
        display: block;
        color: #1f2937;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .head-cell span {
        color: #6b7280;
        font-size: 12px;
    }

    .staff-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 96px;
        padding: 8px 14px;
        border-radius: 999px;
        background: #eef5ff;
        color: #174a96;
        font-weight: 700;
        font-size: 13px;
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
        color: #1f2937;
        border-radius: 8px;
        transition: 0.2s ease;
        text-decoration: none;
        margin-right: 6px;
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
        max-width: 1135px;
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
            margin-bottom: 50px;
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
            min-width: 900px;
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
                    <input type="text" id="searchInput" class="search-box" placeholder="Search departments...">
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
                <h2>Departments Management</h2>
                <p>Manage all hospital departments</p>
            </div>

            <a href="{{ route('admin.departments.create') }}" class="add-btn">
                <i class="fa-solid fa-plus"></i>
                <span>Add New Department</span>
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <div class="card-table reveal">
            <x-table-filters
                :action="route('admin.departments')"
                :type-options="[]"
                :status-options="['active' => 'Active', 'inactive' => 'Inactive']"
                :show-type="false" />
            <table class="table custom-table align-middle">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Head of Department</th>
                        <th>Staff Count</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="departmentsTableBody">
                    @foreach($departments as $dept)
                    <tr class="department-row" data-department-id="{{ $dept->id }}">
                        <td class="department-cell">
                            <div class="department-stack">
                                <span class="department-title">{{ $dept->name_en }}</span>
                                <span class="department-meta">ID #{{ $dept->id }}</span>
                            </div>
                        </td>
                        <td class="head-cell">
                            <strong>{{ $dept->head_name ?? $dept->doctor->name ?? 'No head assigned' }}</strong>
                            <span>{{ $dept->doctor?->name ? 'Head linked to doctor record' : 'No head linked to a doctor record' }}</span>
                        </td>
                        <td><span class="staff-pill">{{ $dept->staff_count ?? 0 }} staff</span></td>
                        <td>
                            <span
                                class="status-badge {{ $dept->status == 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ ucfirst($dept->status) }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.departments.edit', $dept->id) }}" class="action-link">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>

                            <form action="{{ route('admin.departments.delete', $dept->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete department #{{ $dept->id }} only? Related records will be preserved by database safeguards.')" type="submit" class="action-btn">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @forelse($dept->doctors as $doctor)
                    <tr class="doctor-row" data-parent-id="{{ $dept->id }}">
                        <td class="doctor-label-cell">
                            <span class="doctor-indent">Doctor</span>
                        </td>
                        <td>
                            <span class="doctor-name">{{ $doctor->name }}</span>
                            <span class="doctor-meta">{{ $doctor->specialization ?: 'No specialization listed' }}</span>
                        </td>
                        <td>
                            <span class="doctor-meta">
                                {{ $doctor->experience !== null ? $doctor->experience . ' years experience' : ($doctor->email ?: 'No contact details') }}
                            </span>
                        </td>
                        <td>
                            <span
                                class="status-badge {{ $doctor->status === 'Available' ? 'status-active' : 'status-inactive' }}">
                                {{ $doctor->status ?: 'Unknown' }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="action-link">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr class="doctor-row" data-parent-id="{{ $dept->id }}">
                        <td class="doctor-label-cell">
                            <span class="doctor-indent">Doctor</span>
                        </td>
                        <td class="doctor-empty">No doctors assigned to this department yet.</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endforelse
                    @endforeach
                </tbody>
            </table>
            <div class="table-filter-pagination px-4 pb-4">
                {{ $departments->links() }}
            </div>
        </div>
    </div>

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

    function applyDepartmentSearch() {
        const value = searchInput.value.trim().toLowerCase();
        const departmentRows = departmentsTableBody.querySelectorAll('.department-row');

        departmentRows.forEach(departmentRow => {
            const departmentId = departmentRow.dataset.departmentId;
            const doctorRows = departmentsTableBody.querySelectorAll(
                `.doctor-row[data-parent-id="${departmentId}"]`);
            const departmentMatches = value === '' || departmentRow.innerText.toLowerCase().includes(value);
            let hasVisibleDoctor = false;

            doctorRows.forEach(doctorRow => {
                const doctorMatches = value === '' || doctorRow.innerText.toLowerCase().includes(value);
                doctorRow.style.display = doctorMatches ? '' : 'none';
                hasVisibleDoctor = hasVisibleDoctor || doctorMatches;
            });

            departmentRow.style.display = departmentMatches || hasVisibleDoctor ? '' : 'none';
        });
    }

    searchInput.addEventListener('keyup', applyDepartmentSearch);

    const departmentsTableBody = document.getElementById('departmentsTableBody');
    const csrfToken = '{{ csrf_token() }}';

    async function fetchDepartments() {
        departmentsTableBody.innerHTML =
            '<tr><td colspan="5" class="text-center py-4 text-muted">Loading departments...</td></tr>';

        try {
            const response = await fetch('/api/departments');
            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            const result = await response.json();
            const departments = Array.isArray(result.data) ? result.data : [];

            if (!departments.length) {
                departmentsTableBody.innerHTML =
                    '<tr><td colspan="5" class="text-center py-4 text-muted">No departments found.</td></tr>';
                return;
            }

            departmentsTableBody.innerHTML = departments.map(dept => {
                const status = (dept.status ?? 'active').toLowerCase();
                const statusLabel = status === 'active' ? 'Active' : 'Inactive';
                const statusClass = status === 'active' ? 'status-active' : 'status-inactive';
                const staffCount = Number.isFinite(Number(dept.staff_count)) ? `${dept.staff_count} staff` :
                    '0 staff';
                const nameEn = dept.name_en ?? '-';
                const headName = dept.head_name ?? 'No head assigned';
                const doctors = Array.isArray(dept.doctors) ? dept.doctors : [];
                const doctorRows = doctors.length ? doctors.map(doctor => {
                    const doctorStatus = (doctor.status ?? '').toLowerCase() === 'available' ?
                        'Available' : 'Busy';
                    const doctorStatusClass = doctorStatus === 'Available' ? 'status-active' :
                        'status-inactive';
                    const doctorMeta = doctor.specialization ?? 'No specialization listed';
                    const doctorExtra = doctor.experience !== null && doctor.experience !==
                        undefined ?
                        `${doctor.experience} years experience` :
                        (doctor.email ?? '');

                    return `
                    <tr class="doctor-row" data-parent-id="${dept.id}">
                        <td class="doctor-label-cell">
                            <span class="doctor-indent">Doctor</span>
                        </td>
                        <td>
                            <span class="doctor-name">${doctor.name ?? 'Unnamed doctor'}</span>
                            <span class="doctor-meta">${doctorMeta}</span>
                        </td>
                        <td><span class="doctor-meta">${doctorExtra || ''}</span></td>
                        <td><span class="status-badge ${doctorStatusClass}">${doctorStatus}</span></td>
                        <td class="actions-cell">
                            <a href="/admin/doctors/edit/${doctor.id}" class="action-link">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                        </td>
                    </tr>`;
                }).join('') : `
                    <tr class="doctor-row" data-parent-id="${dept.id}">
                        <td class="doctor-label-cell">
                            <span class="doctor-indent">Doctor</span>
                        </td>
                        <td class="doctor-empty">No doctors assigned to this department yet.</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>`;

                return `
                    <tr class="department-row" data-department-id="${dept.id}">
                        <td class="department-cell">
                            <div class="department-stack">
                                <span class="department-title">${nameEn}</span>
                                <span class="department-meta">ID #${dept.id}</span>
                            </div>
                        </td>
                        <td class="head-cell">
                            <strong>${headName}</strong>
                            <span>${dept.doctor_name ? 'Head linked to doctor record' : 'No head linked to a doctor record'}</span>
                        </td>
                        <td><span class="staff-pill">${staffCount}</span></td>
                        <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                        <td class="actions-cell">
                            <a href="/admin/departments/edit/${dept.id}" class="action-link">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="/admin/departments/delete/${dept.id}" method="POST" style="display:inline;">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button onclick="return confirm('Delete department #' + dept.id + ' only? Related records will be preserved by database safeguards.')" type="submit" class="action-btn">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    ${doctorRows}
                `;
            }).join('');

            applyDepartmentSearch();
        } catch (error) {
            console.error('Failed to load departments:', error);
            departmentsTableBody.innerHTML =
                '<tr><td colspan="5" class="text-center py-4 text-danger">Failed to load departments data.</td></tr>';
        }
    }

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
