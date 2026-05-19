<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>NUH Admin - Staff</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Inter, Arial, sans-serif; }
    :root {
        --primary: #114a9f;
        --primary-dark: #0b3f8c;
        --text-dark: #1f2937;
        --text-muted: #6b7280;
        --danger: #ff3b3b;
        --success: #0f4aa2;
        --busy: #dbe4f0;
        --shadow-soft: 0 12px 28px rgba(34, 52, 84, 0.16);
    }
    body {
        background:
            radial-gradient(circle at 75% 52%, rgba(43, 154, 255, .92) 0%, rgba(107, 188, 255, .72) 23%, rgba(200, 225, 245, .60) 44%, rgba(236, 240, 244, .92) 70%, #f1f1f1 100%);
        min-height: 100vh;
        color: var(--text-dark);
        overflow-x: hidden;
    }
    a { text-decoration: none; color: inherit; }
    .sidebar {
        position: fixed; top: 18px; left: 18px; width: 280px; height: calc(100vh - 36px);
        z-index: 1100; transform: translateX(-120%); transition: transform .35s cubic-bezier(.4, 0, .2, 1);
    }
    .sidebar.open { transform: translateX(0); }
    .sidebar-panel {
        height: 100%; background: rgba(247, 247, 247, 0.97); border-radius: 34px;
        box-shadow: 0 18px 36px rgba(0, 0, 0, 0.14); padding: 28px 18px 18px;
        display: flex; flex-direction: column; backdrop-filter: blur(2px);
    }
    .sidebar-logo { display: flex; justify-content: center; align-items: center; min-height: 86px; margin-bottom: 24px; }
    .sidebar-logo img { max-width: 145px; height: auto; object-fit: contain; }
    .sidebar-nav { margin-top: 8px; display: flex; flex-direction: column; gap: 8px; }
    .sidebar-nav a {
        display: flex; align-items: center; gap: 14px; color: #2f3947; padding: 16px 18px;
        border-radius: 18px; font-size: 16px; font-weight: 500; transition: .22s ease;
    }
    .sidebar-nav a:hover { background: #eceff3; color: #2e3844; transform: translateX(4px); }
    .sidebar-nav a.active, .sidebar-nav a.active:hover {
        background: var(--primary); color: #fff; box-shadow: 0 10px 22px rgba(17, 74, 159, .26); transform: none;
    }
    .sidebar-nav a i { width: 24px; text-align: center; font-size: 20px; }
    .logout-wrap { margin-top: auto; padding-top: 18px; }
    .logout-btn {
        display: flex; width: 100%; border: none; background: var(--danger); color: #fff; padding: 16px 18px;
        border-radius: 14px; font-size: 18px; font-weight: 700; align-items: center; justify-content: center;
    }
    .overlay {
        position: fixed; inset: 0; background: rgba(0, 0, 0, 0.22); opacity: 0; visibility: hidden;
        transition: 0.3s ease; z-index: 1000;
    }
    .overlay.show { opacity: 1; visibility: visible; }
    .main { min-height: 100vh; padding: 22px 28px 40px; }
    .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 40px; }
    .topbar-left { display: flex; align-items: flex-start; gap: 16px; }
    .brand-box img { max-width: 120px; height: auto; object-fit: contain; }
    .menu-toggle {
        width: 46px; height: 46px; border: none; background: transparent; font-size: 28px; color: #4b5563;
        cursor: pointer; border-radius: 12px; display: flex; align-items: center; justify-content: center;
    }
    .menu-toggle:hover { background: rgba(255, 255, 255, .4); }
    .topbar-right { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
    .search-wrapper { position: relative; }
    .search-box {
        width: 320px; height: 50px; border: none; border-radius: 18px; background: #fff;
        padding: 0 48px 0 18px; font-size: 15px; box-shadow: var(--shadow-soft);
    }
    .search-box:focus { outline: none; }
    .search-icon { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #6b7280; }
    .icon-btn {
        width: 46px; height: 46px; border: none; border-radius: 16px; background: #fff; box-shadow: var(--shadow-soft);
        display: flex; align-items: center; justify-content: center; color: #4b5563; font-size: 18px;
    }
    .admin-box { display: flex; align-items: center; gap: 10px; }
    .admin-info { text-align: right; line-height: 1.2; }
    .admin-info .name { font-size: 15px; color: #1f2937; font-weight: 600; }
    .admin-info .role { font-size: 14px; color: #6b7280; }
    .avatar {
        width: 46px; height: 46px; border-radius: 50%; background: #0b4aa7; color: #fff;
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px;
    }
    .page-header {
        max-width: 1150px; margin: 0 auto 22px; display: flex; justify-content: space-between;
        align-items: center; gap: 16px; flex-wrap: wrap;
    }
    .page-title h2 { font-size: 20px; font-weight: 600; margin: 0 0 6px; }
    .page-title p { font-size: 14px; color: #4b5563; margin: 0; }
    .add-btn {
        background: var(--primary); color: #fff; padding: 12px 28px; border-radius: 16px; font-weight: 500;
        display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 10px 18px rgba(15, 74, 162, 0.18);
    }
    .add-btn:hover { background: var(--primary-dark); color: #fff; }
    .card-table {
        max-width: 1150px; margin: 0 auto; background: rgba(248, 248, 248, .98); border-radius: 26px;
        overflow: hidden; box-shadow: var(--shadow-soft);
    }
    .custom-table { margin: 0; }
    .custom-table thead th {
        background: var(--primary); color: #fff; font-size: 13px; font-weight: 600; padding: 14px 16px;
        border: none; white-space: nowrap;
    }
    .custom-table tbody td {
        padding: 13px 16px; font-size: 13px; color: #374151; vertical-align: middle;
        border-color: #e6e6e6; background: rgba(248, 248, 248, .98);
    }
    .custom-table tbody tr:hover td { background: #f2f7ff; }
    .status-badge {
        display: inline-flex; align-items: center; justify-content: center; min-width: 90px; padding: 7px 14px;
        border-radius: 999px; font-size: 13px; font-weight: 500;
    }
    .status-active { background: var(--success); color: #fff; }
    .status-inactive { background: var(--busy); color: #1f2937; }
    .actions-cell { white-space: nowrap; }
    .action-link, .action-btn {
        display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px;
        border: none; background: transparent; color: #374151; border-radius: 8px; transition: 0.2s ease;
    }
    .action-link:hover, .action-btn:hover { background: #f3f4f6; color: var(--primary); }
    .settings-link { background: var(--primary); color: #fff; }
    .settings-link:hover { background: var(--primary-dark); color: #fff; }
    .alert { max-width: 1150px; margin: 0 auto 16px; border-radius: 14px; }
    @media (max-width: 992px) {
        .topbar { flex-direction: column; align-items: stretch; }
        .card-table { overflow-x: auto; }
        .custom-table { min-width: 760px; }
    }
    @media (max-width: 768px) {
        .main { padding: 18px 14px 30px; }
        .search-box { width: 100%; min-width: 230px; }
        .topbar-right { width: 100%; justify-content: space-between; }
        .search-wrapper { flex: 1; min-width: 100%; order: 1; }
        .sidebar { width: 260px; left: 10px; top: 10px; height: calc(100vh - 20px); }
    }
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
                <button class="menu-toggle" id="menuToggle" type="button">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="brand-box">
                    <img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo">
                </div>
            </div>

            <div class="topbar-right">
                <div class="search-wrapper">
                    <input id="searchInput" type="text" class="search-box" placeholder="Search staff...">
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
            <div class="page-title">
                <h2>Staff Management</h2>
                <p>Manage hospital staff accounts, roles, and login access</p>
            </div>

            <a href="{{ route('admin.staff.create') }}" class="add-btn">
                <i class="fa-solid fa-plus"></i><span>Add Staff</span>
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card-table">
            <x-table-filters
                :action="route('admin.staff')"
                :type-options="$roles"
                type-label="Role"
                :status-options="$statuses"
                search-label="Name or Email"
                search-placeholder="Search by name or email..."
                :show-search="true" />

            <table class="table custom-table align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($staff as $staffMember)
                    <tr>
                        <td>{{ $staffMember->id }}</td>
                        <td>{{ $staffMember->displayName() }}</td>
                        <td>{{ $staffMember->email }}</td>
                        <td>{{ $roles[$staffMember->role] ?? ucfirst(str_replace('_', ' ', $staffMember->role ?? '-')) }}</td>
                        <td>
                            <span class="status-badge {{ $staffMember->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                {{ $statuses[$staffMember->status] ?? ucfirst((string) $staffMember->status) }}
                            </span>
                        </td>
                        <td class="actions-cell">
                            <a href="{{ route('admin.staff.edit', $staffMember->id) }}" class="action-link" title="Edit staff">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.staff.delete', $staffMember->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn" title="Delete staff" onclick="return confirm('Delete staff member #{{ $staffMember->id }} only? This action will not mass delete records.')">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No staff members found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="table-filter-pagination px-4 pb-4">
                {{ $staff->links() }}
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

    document.getElementById('searchInput').addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        document.querySelectorAll('.custom-table tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
        });
    });
    </script>
</body>

</html>
