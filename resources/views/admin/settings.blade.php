<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>NUH Admin - Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary: #114a9f;
        --text: #243445;
        --muted: #667085;
        --danger: #ff5a52;
        --card-bg: rgba(248, 248, 248, 0.97);
        --border: rgba(17, 74, 159, 0.18);
        --shadow: 0 18px 36px rgba(34, 52, 84, 0.18);
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        font-family: 'Inter', Arial, sans-serif;
        color: var(--text);
        min-height: 100vh;
        overflow-x: hidden;
        background: radial-gradient(circle at 72% 52%, rgba(49, 157, 255, 0.95) 0%, rgba(110, 189, 255, 0.72) 20%, rgba(196, 224, 248, 0.62) 42%, rgba(235, 240, 245, 0.95) 70%, #f2f2f2 100%);
    }

    a { text-decoration: none; color: inherit; }
    button, input, select { font-family: inherit; }
    .layout { min-height: 100vh; position: relative; }
    .sidebar {
        width: 290px; padding: 22px 18px; position: fixed; top: 0; left: 0; bottom: 0; z-index: 1200;
        transform: translateX(-112%); transition: transform .35s cubic-bezier(.4, 0, .2, 1);
    }
    .sidebar.active { transform: translateX(0); }
    .sidebar-panel {
        height: 100%; background: rgba(247, 247, 247, 0.97); border-radius: 34px; box-shadow: 0 16px 35px rgba(0, 0, 0, 0.14);
        display: flex; flex-direction: column; padding: 28px 18px 18px; backdrop-filter: blur(2px);
    }
    .sidebar-logo { display: flex; justify-content: center; align-items: center; min-height: 78px; margin-bottom: 26px; }
    .sidebar-logo img { max-width: 145px; height: auto; object-fit: contain; }
    .menu { list-style: none; display: flex; flex-direction: column; gap: 8px; }
    .menu a {
        display: flex; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 18px; color: #2e3844;
        transition: .22s ease; position: relative; overflow: hidden;
    }
    .menu a:hover { background: #eceff3; transform: translateX(4px); }
    .menu li.active a { background: var(--primary); color: #fff; box-shadow: 0 10px 20px rgba(17, 74, 159, 0.28); }
    .menu i { width: 24px; text-align: center; font-size: 21px; }
    .logout-wrap { margin-top: auto; padding-top: 18px; }
    .logout-btn {
        width: 100%; border: none; border-radius: 14px; background: var(--danger); color: #fff; font-size: 18px; font-weight: 700;
        padding: 16px 18px; display: flex; align-items: center; justify-content: center;
    }
    .overlay { position: fixed; inset: 0; background: rgba(0, 0, 0, .18); opacity: 0; pointer-events: none; transition: .25s ease; z-index: 1100; }
    .overlay.show { opacity: 1; pointer-events: auto; }
    .main { min-height: 100vh; padding: 24px 28px 42px; }
    .topbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 20px; margin-bottom: 34px; }
    .topbar-left, .topbar-right { display: flex; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
    .menu-btn {
        width: 46px; height: 46px; border: none; background: transparent; color: #4b5563; font-size: 28px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
    }
    .menu-btn:hover { background: rgba(255,255,255,.55); }
    .brand-block h1 { font-size: 21px; font-weight: 600; margin-bottom: 6px; }
    .brand-block p { color: #4b5563; font-size: 14px; }
    .icon-btn, .avatar {
        width: 46px; height: 46px; border-radius: 16px; background: #f8f8f8; box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        display: flex; align-items: center; justify-content: center;
    }
    .icon-btn { border: none; color: #4f5965; font-size: 20px; }
    .profile-link { display: flex; align-items: center; gap: 12px; }
    .profile-info { text-align: left; }
    .profile-info strong { display: block; font-size: 15px; }
    .profile-info span { display: block; margin-top: 4px; font-size: 13px; color: var(--muted); }
    .avatar { background: #124d9d; color: #fff; border-radius: 999px; font-weight: 700; font-size: 18px; }
    .content { max-width: 1180px; margin: 0 auto; }
    .page-intro { margin-bottom: 24px; }
    .page-intro h2 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
    .page-intro p { color: #4b5563; font-size: 14px; }
    .settings-form { display: grid; gap: 18px; }
    .card {
        background: var(--card-bg); border-radius: 24px; box-shadow: var(--shadow); padding: 24px;
    }
    .section-title { display: flex; align-items: flex-start; gap: 14px; margin-bottom: 22px; }
    .section-title i {
        width: 42px; height: 42px; border-radius: 14px; background: var(--primary); color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 19px; flex-shrink: 0;
    }
    .section-title h3 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
    .section-title p { color: var(--muted); font-size: 14px; }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .field { display: grid; gap: 9px; }
    .field.full { grid-column: 1 / -1; }
    .field label { font-size: 14px; font-weight: 600; color: #425466; }
    .text-input, .select-input {
        width: 100%; height: 54px; border-radius: 14px; border: 1.5px solid #3b82f6; background: #fff;
        padding: 0 16px; font-size: 15px; color: #374151; outline: none;
    }
    .file-input { padding-top: 14px; }
    .hint { color: var(--muted); font-size: 12px; line-height: 1.5; }
    .logo-preview { display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--border); border-radius: 16px; background: #fff; }
    .logo-preview img { max-width: 96px; max-height: 48px; object-fit: contain; }
    .actions-bar { display: flex; justify-content: flex-end; }
    .save-btn {
        border: none; background: var(--primary); color: #fff; padding: 14px 28px; border-radius: 16px; font-size: 15px; font-weight: 700;
        box-shadow: 0 10px 20px rgba(17, 74, 159, .22); cursor: pointer;
    }
    .filters-bar {
        display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; align-items: end; margin-bottom: 18px;
    }
    .filter-field { display: grid; gap: 7px; }
    .filter-field label { font-size: 12px; font-weight: 700; color: #425466; }
    .filter-input {
        width: 100%; height: 40px; border-radius: 12px; border: 1px solid #cbd5e1; background: #fff;
        padding: 0 12px; font-size: 13px; color: #374151; outline: none;
    }
    .filter-btn, .small-action {
        height: 40px; border: none; border-radius: 12px; padding: 0 14px; font-size: 13px; font-weight: 700; cursor: pointer;
    }
    .filter-btn { background: var(--primary); color: #fff; }
    .table-scroll { overflow-x: auto; border-radius: 18px; border: 1px solid rgba(17, 74, 159, 0.12); }
    .users-table { width: 100%; min-width: 900px; border-collapse: collapse; background: #fff; }
    .users-table th {
        background: var(--primary); color: #fff; text-align: left; font-size: 13px; font-weight: 700; padding: 14px 16px;
    }
    .users-table td {
        border-top: 1px solid #e5e7eb; padding: 13px 16px; font-size: 13px; vertical-align: middle;
    }
    .users-table tbody tr:nth-child(even) td { background: #f8fafc; }
    .role-pill, .status-pill {
        display: inline-flex; align-items: center; justify-content: center; border-radius: 999px; padding: 6px 10px; font-weight: 700; font-size: 12px;
    }
    .role-pill { background: #e8f1ff; color: var(--primary); }
    .status-active { background: #dcfce7; color: #166534; }
    .status-deleted { background: #fee2e2; color: #991b1b; }
    .action-row { display: flex; gap: 8px; flex-wrap: wrap; }
    .small-action { height: 34px; }
    .delete-action { background: #fee2e2; color: #991b1b; }
    .restore-action { background: #dcfce7; color: #166534; }
    .empty-row { text-align: center; color: var(--muted); padding: 28px 16px !important; }
    .pagination-wrap { margin-top: 18px; }
    .pagination { display: flex; flex-wrap: wrap; gap: 6px; list-style: none; }
    .page-link, .page-item span {
        display: inline-flex; min-width: 34px; height: 34px; align-items: center; justify-content: center;
        border-radius: 10px; background: #fff; color: var(--primary); padding: 0 10px; border: 1px solid #d7e2f0; font-size: 13px;
    }
    .page-item.active .page-link { background: var(--primary); color: #fff; }
    .page-item.disabled span { color: #94a3b8; }
    .alert {
        margin-bottom: 18px; padding: 14px 16px; border-radius: 14px; box-shadow: var(--shadow);
    }
    .alert-success { background: #ebf8ef; color: #166534; }
    .alert-danger { background: #fff1f2; color: #9f1239; }
    .error-text { color: #b42318; font-size: 12px; margin-top: 2px; }
    @media (max-width: 980px) {
        .main { padding: 20px 16px 34px; }
        .topbar { flex-direction: column; }
        .form-grid { grid-template-columns: 1fr; }
        .filters-bar { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 640px) {
        .profile-link { width: 100%; justify-content: space-between; }
        .card { padding: 20px; border-radius: 20px; }
        .filters-bar { grid-template-columns: 1fr; }
    }
        .settings-link { background: var(--primary); color: #fff; }
    .settings-link:hover { background: var(--primary-dark); color: #fff; }
    </style>
</head>

<body>
    @php
        $adminLabel = $adminName ?: 'Administrator';
        $logoPath = $settings['logo_path'] ?: 'images/logo_Image.png';
    @endphp

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-panel">
                <div class="sidebar-logo">
                    <img src="{{ asset($logoPath) }}" alt="NUH">
                </div>

                <ul class="menu">
                    <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2"></i>Dashboard</a></li>
                    <li class="{{ request()->routeIs('admin.appointments*') ? 'active' : '' }}"><a href="{{ route('admin.appointments') }}"><i class="bi bi-calendar-event"></i>Appointments</a></li>
                    <li class="{{ request()->routeIs('admin.doctors*') ? 'active' : '' }}"><a href="{{ route('admin.doctors') }}"><i class="bi bi-heart-pulse"></i>Doctors</a></li>
                    <li class="{{ request()->routeIs('admin.patients*') ? 'active' : '' }}"><a href="{{ route('admin.patients') }}"><i class="bi bi-people"></i>Patients</a></li>
                    <li class="{{ request()->routeIs('admin.staff*') ? 'active' : '' }}"><a href="{{ route('admin.staff') }}"><i class="bi bi-person-badge"></i>Staff</a></li>
                    <li class="{{ request()->routeIs('admin.rooms*') ? 'active' : '' }}"><a href="{{ route('admin.rooms') }}"><i class="bi bi-building"></i>Rooms</a></li>
                    <li class="{{ request()->routeIs('admin.departments*') ? 'active' : '' }}"><a href="{{ route('admin.departments') }}"><i class="bi bi-hospital"></i>Departments</a></li>
                </ul>

                <div class="logout-wrap">
                    <a href="{{ url('/') }}" class="logout-btn">Log out</a>
                </div>
            </div>
        </aside>

        <div class="overlay" id="overlay"></div>

        <main class="main">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-btn" id="menuBtn" type="button" aria-label="Open menu"><i class="bi bi-list"></i></button>
                    <div class="brand-block">
                        <h1>{{ $settings['app_name'] }}</h1>
                        <p>Admin Dashboard</p>
                    </div>
                </div>

                <div class="topbar-right">
                    <button class="icon-btn" type="button"><i class="bi bi-bell"></i></button>
                    <a href="{{ route('admin.settings') }}" class="icon-btn settings-link" title="Settings" aria-label="Settings"><i class="bi bi-gear-fill"></i></a>
                    <div class="profile-link">
                        <div class="profile-info">
                            <strong>{{ $adminLabel }}</strong>
                            <span>{{ $admin?->email ?? 'Administrator' }}</span>
                        </div>
                        <div class="avatar">{{ strtoupper(substr($adminLabel, 0, 1)) }}</div>
                    </div>
                </div>
            </header>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <div class="page-intro">
                    <h2>Settings</h2>
                    <p>Essential system, language, and admin profile preferences.</p>
                </div>

                <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="settings-form">
                    @csrf

                    <section class="card">
                        <div class="section-title">
                            <i class="bi bi-building"></i>
                            <div>
                                <h3>General</h3>
                                <p>Basic identity used across the admin area.</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="field">
                                <label for="system_name">System name</label>
                                <input id="system_name" name="system_name" class="text-input" type="text" value="{{ old('system_name', $settings['system_name']) }}" required>
                                @error('system_name')<div class="error-text">{{ $message }}</div>@enderror
                            </div>

                            <div class="field">
                                <label for="app_name">App name</label>
                                <input id="app_name" name="app_name" class="text-input" type="text" value="{{ old('app_name', $settings['app_name']) }}" required>
                                @error('app_name')<div class="error-text">{{ $message }}</div>@enderror
                            </div>

                            <div class="field">
                                <label for="theme">Theme</label>
                                <select id="theme" name="theme" class="select-input">
                                    @foreach($themeOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('theme', $settings['theme']) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="field">
                                <label for="logo">Logo</label>
                                <input id="logo" name="logo" class="text-input file-input" type="file" accept="image/*">
                                <div class="hint">Optional. Upload an image up to 2 MB.</div>
                                @error('logo')<div class="error-text">{{ $message }}</div>@enderror
                            </div>

                            <div class="field full">
                                <div class="logo-preview">
                                    <img src="{{ asset($logoPath) }}" alt="Current logo">
                                    <span class="hint">Current logo</span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="card">
                        <div class="section-title">
                            <i class="bi bi-translate"></i>
                            <div>
                                <h3>Language</h3>
                                <p>Choose the default interface language.</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="field">
                                <label for="default_language">Default language</label>
                                <select id="default_language" name="default_language" class="select-input" required>
                                    @foreach($languageOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(old('default_language', $settings['default_language']) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('default_language')<div class="error-text">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </section>

                    <section class="card">
                        <div class="section-title">
                            <i class="bi bi-person-circle"></i>
                            <div>
                                <h3>Profile</h3>
                                <p>Update the signed-in admin account.</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="field">
                                <label for="admin_name">Admin name</label>
                                <input id="admin_name" name="admin_name" class="text-input" type="text" value="{{ old('admin_name', $adminName) }}" required>
                                @error('admin_name')<div class="error-text">{{ $message }}</div>@enderror
                            </div>

                            <div class="field">
                                <label for="admin_email">Admin email</label>
                                <input id="admin_email" name="admin_email" class="text-input" type="email" value="{{ old('admin_email', $admin?->email) }}" required>
                                @error('admin_email')<div class="error-text">{{ $message }}</div>@enderror
                            </div>

                            <div class="field">
                                <label for="current_password">Current password</label>
                                <input id="current_password" name="current_password" class="text-input" type="password" autocomplete="current-password">
                                <div class="hint">Required only when changing password.</div>
                                @error('current_password')<div class="error-text">{{ $message }}</div>@enderror
                            </div>

                            <div class="field">
                                <label for="password">New password</label>
                                <input id="password" name="password" class="text-input" type="password" autocomplete="new-password">
                                @error('password')<div class="error-text">{{ $message }}</div>@enderror
                            </div>

                            <div class="field">
                                <label for="password_confirmation">Confirm new password</label>
                                <input id="password_confirmation" name="password_confirmation" class="text-input" type="password" autocomplete="new-password">
                            </div>
                        </div>
                    </section>

                    <div class="actions-bar">
                        <button type="submit" class="save-btn">Save Settings</button>
                    </div>
                </form>

                <section class="card" style="margin-top: 24px;">
                    <div class="section-title">
                        <i class="bi bi-people"></i>
                        <div>
                            <h3>User Management</h3>
                            <p>View doctors, patients, and staff in one safe management table.</p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('admin.settings') }}" class="filters-bar">
                        <div class="filter-field">
                            <label for="user_role">Role</label>
                            <select id="user_role" name="user_role" class="filter-input">
                                <option value="all" @selected($filters['role'] === 'all')>All</option>
                                <option value="doctor" @selected($filters['role'] === 'doctor')>Doctor</option>
                                <option value="patient" @selected($filters['role'] === 'patient')>Patient</option>
                                <option value="staff" @selected($filters['role'] === 'staff')>Staff</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label for="user_date">Date</label>
                            <select id="user_date" name="user_date" class="filter-input">
                                <option value="all" @selected($filters['date'] === 'all')>All</option>
                                <option value="today" @selected($filters['date'] === 'today')>Today</option>
                                <option value="week" @selected($filters['date'] === 'week')>This week</option>
                                <option value="month" @selected($filters['date'] === 'month')>This month</option>
                            </select>
                        </div>

                        <div class="filter-field">
                            <label for="user_status">Status</label>
                            <select id="user_status" name="user_status" class="filter-input">
                                <option value="active" @selected($filters['status'] === 'active')>Active</option>
                                <option value="deleted" @selected($filters['status'] === 'deleted')>Deleted</option>
                                <option value="all" @selected($filters['status'] === 'all')>All</option>
                            </select>
                        </div>

                        <button type="submit" class="filter-btn">Apply Filters</button>
                    </form>

                    <div class="table-scroll">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user['id'] }}</td>
                                        <td>{{ $user['name'] }}</td>
                                        <td>{{ $user['email'] }}</td>
                                        <td>{{ $user['phone'] }}</td>
                                        <td><span class="role-pill">{{ $user['role'] }}</span></td>
                                        <td>
                                            <span class="status-pill {{ $user['status'] === 'Deleted' ? 'status-deleted' : 'status-active' }}">
                                                {{ $user['status'] }}
                                            </span>
                                        </td>
                                        <td>{{ $user['created_at'] }}</td>
                                        <td>
                                            <div class="action-row">
                                                @if($user['status'] === 'Deleted')
                                                    <form method="POST" action="{{ route('admin.settings.users.restore', ['role' => $user['role_key'], 'id' => $user['id'], 'user_role' => $filters['role'], 'user_date' => $filters['date'], 'user_status' => $filters['status'], 'page' => request('page')]) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="small-action restore-action" onclick="return confirm('Restore this user?')">Restore</button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('admin.settings.users.destroy', ['role' => $user['role_key'], 'id' => $user['id'], 'user_role' => $filters['role'], 'user_date' => $filters['date'], 'user_status' => $filters['status'], 'page' => request('page')]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="small-action delete-action" onclick="return confirm('Delete this user? This will soft delete the account and keep related records.')">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="empty-row">No users found for the selected filters.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="pagination-wrap">
                            {{ $users->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>

    <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuBtn = document.getElementById('menuBtn');

    menuBtn.addEventListener('click', function () {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
        menuBtn.querySelector('i').className = sidebar.classList.contains('active') ? 'bi bi-x-lg' : 'bi bi-list';
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
        menuBtn.querySelector('i').className = 'bi bi-list';
    });
    </script>
</body>

</html>
