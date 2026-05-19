<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>NUH Admin - {{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary:#114a9f; --primary-dark:#0b3f8c; --danger:#ff3b3b; --shadow-soft:0 12px 28px rgba(34,52,84,.16); }
        * { box-sizing:border-box; font-family:Inter,Arial,sans-serif; }
        body { margin:0; min-height:100vh; background:radial-gradient(circle at 75% 52%, rgba(43,154,255,.92) 0%, rgba(107,188,255,.72) 23%, rgba(200,225,245,.60) 44%, rgba(236,240,244,.92) 70%, #f1f1f1 100%); color:#1f2937; }
        a { text-decoration:none; color:inherit; }
        .sidebar { position:fixed; top:18px; left:18px; width:280px; height:calc(100vh - 36px); z-index:1100; transform:translateX(-120%); transition:.35s; }
        .sidebar.open { transform:translateX(0); }
        .sidebar-panel { height:100%; background:rgba(247,247,247,.97); border-radius:34px; box-shadow:0 18px 36px rgba(0,0,0,.14); padding:28px 18px 18px; display:flex; flex-direction:column; }
        .sidebar-logo { display:flex; justify-content:center; min-height:86px; margin-bottom:24px; }
        .sidebar-logo img { max-width:145px; object-fit:contain; }
        .sidebar-nav { display:flex; flex-direction:column; gap:8px; }
        .sidebar-nav a { display:flex; align-items:center; gap:14px; padding:16px 18px; border-radius:18px; color:#2f3947; font-size:16px; font-weight:500; transition:.22s; }
        .sidebar-nav a:hover { background:#eceff3; transform:translateX(4px); }
        .sidebar-nav a.active { background:var(--primary); color:#fff; box-shadow:0 10px 22px rgba(17,74,159,.26); }
        .sidebar-nav i { width:24px; text-align:center; font-size:20px; }
        .logout-wrap { margin-top:auto; padding-top:18px; }
        .logout-btn { display:flex; justify-content:center; width:100%; background:var(--danger); color:#fff; padding:16px 18px; border-radius:14px; font-size:18px; font-weight:700; }
        .overlay { position:fixed; inset:0; background:rgba(0,0,0,.22); opacity:0; visibility:hidden; transition:.3s; z-index:1000; }
        .overlay.show { opacity:1; visibility:visible; }
        .main { min-height:100vh; padding:22px 28px 40px; }
        .topbar { display:flex; justify-content:space-between; align-items:flex-start; gap:20px; margin-bottom:40px; }
        .topbar-left,.topbar-right { display:flex; align-items:center; gap:16px; flex-wrap:wrap; }
        .brand-box img { max-width:120px; }
        .menu-toggle,.icon-btn { width:46px; height:46px; border:0; border-radius:16px; background:#fff; box-shadow:var(--shadow-soft); color:#4b5563; display:flex; align-items:center; justify-content:center; }
        .menu-toggle { background:transparent; box-shadow:none; font-size:28px; }
        .settings-link { background:var(--primary); color:#fff; }
        .settings-link:hover { background:var(--primary-dark); color:#fff; }
        .admin-box { display:flex; align-items:center; gap:10px; }
        .admin-info { text-align:right; line-height:1.2; }
        .admin-info .name { font-size:15px; font-weight:600; }
        .admin-info .role { font-size:14px; color:#6b7280; }
        .avatar { width:46px; height:46px; border-radius:50%; background:#0b4aa7; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; }
        .page-header,.card-table { max-width:1150px; margin:0 auto 22px; }
        .page-header { display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; }
        .page-title h2 { font-size:20px; font-weight:600; margin:0 0 6px; }
        .page-title p { font-size:14px; color:#4b5563; margin:0; }
        .card-table { background:rgba(248,248,248,.98); border-radius:26px; overflow:hidden; box-shadow:var(--shadow-soft); }
        .custom-table { margin:0; }
        .custom-table thead th { background:var(--primary); color:#fff; font-size:13px; font-weight:600; padding:14px 16px; border:0; white-space:nowrap; }
        .custom-table tbody td { padding:13px 16px; font-size:13px; vertical-align:middle; background:rgba(248,248,248,.98); border-color:#e6e6e6; }
        .custom-table tbody tr:hover td { background:#f2f7ff; }
        @media(max-width:992px){ .card-table{overflow-x:auto;} .custom-table{min-width:760px;} .topbar{flex-direction:column;} }
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
</div>
<div class="overlay" id="overlay"></div>
<div class="main">
    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle" type="button"><i class="fa-solid fa-bars"></i></button>
            <div class="brand-box"><img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo"></div>
        </div>
        <div class="topbar-right">
            <button class="icon-btn" type="button"><i class="fa-regular fa-bell"></i></button>
            <a href="{{ route('admin.settings') }}" class="icon-btn settings-link" title="Settings" aria-label="Settings"><i class="fa-solid fa-gear"></i></a>
            <div class="admin-box"><div class="admin-info"><div class="name">Admin Robert</div><div class="role">Administrator</div></div><div class="avatar">R</div></div>
        </div>
    </div>

    <div class="page-header">
        <div class="page-title"><h2>{{ $title }} Results</h2><p>Review uploaded patient {{ strtolower($title) }} files.</p></div>
    </div>

    <div class="card-table">
        <table class="table custom-table align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>File Number</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $result)
                    <tr>
                        <td>{{ $result->id }}</td>
                        <td>{{ $result->patient?->full_name ?? '-' }}</td>
                        <td>{{ $result->patient?->file_number ?? '-' }}</td>
                        <td>{{ $result->title }}</td>
                        <td>{{ $result->created_at?->format('Y-m-d') }}</td>
                        <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.results.download', ['type' => $type, 'id' => $result->id]) }}">Download</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No results found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 pb-4">{{ $results->links() }}</div>
    </div>
</div>
<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
document.getElementById('menuToggle').addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });
</script>
</body>
</html>
