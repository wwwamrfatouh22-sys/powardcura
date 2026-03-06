<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Appointments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #5dade2, #1e69de);
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            color: white;
        }

        .left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-icon {
            font-size: 28px;
            cursor: pointer;
        }


        .brand {
            font-size: 22px;
            font-weight: bold;
        }

        .right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .search-box {
            background: #f3f4f6;
            padding: 10px 18px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            width: 200px;
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
            font-weight: bold;
        }


        .avatar {
            width: 40px;
            height: 40px;
            background: #123a6f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        /* Fix topbar classes (same design) */
        .left-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-btn {
            font-size: 28px;
            cursor: pointer;
            color: white;
        }

        .brand-wrapper .brand {
            font-size: 22px;
            font-weight: bold;
            line-height: 1.1;
        }

        .brand-wrapper .sub-brand {
            font-size: 14px;
            opacity: 0.9;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            left: -280px;
            top: 0;
            width: 260px;
            height: 100vh;
            background: #ffffff;
            padding: 25px;
            transition: 0.3s;
            z-index: 1000;
            box-shadow: 5px 0 25px rgba(0,0,0,0.2);
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }

        .sidebar.active {
            left: 0;
        }

        .sidebar-logo {
            margin-bottom: 30px;
        }

        .menu a {
            display: block;
            padding: 12px 18px;
            margin-bottom: 15px;
            border-radius: 12px;
            text-decoration: none;
            color: black;
            font-weight: 600;
            transition: 0.3s;
        }

        .menu a:hover {
            background: #123a6f;
            color: white;
        }

        .menu a.active {
            background: #123a6f;
            color: white;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.35);
            display: none;
            z-index: 900;
        }

        .overlay.active {
            display: block;
        }

        .container {
            padding: 20px 60px 60px;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 40px;
            opacity: 0.9;
        }

        .section-title {
            text-align: center;
            margin: 40px 0 20px;
            font-size: 22px;
            font-weight: bold;
        }

        .table-box {
            background: #f3f4f6;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
            margin-bottom: 50px;
        }

        .table-header {
            background: #123a6f;
            color: white;
            padding: 18px;
            font-weight: bold;
            display: grid;
            grid-template-columns: 2.5fr 1fr 2fr 1fr;
        }

        .row {
            display: grid;
            grid-template-columns: 2.5fr 1fr 2fr 1fr;
            align-items: center;
            padding: 18px;
            border-bottom: 1px solid #ddd;
        }

        .row:last-child {
            border-bottom: none;
        }

        .patient {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .circle {
            width: 40px;
            height: 40px;
            background: #123a6f;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn {
            background: #123a6f;
            color: white;
            padding: 8px 18px;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
        }

    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="sidebar-logo">
        <img src="{{ asset('images/logo_Image.png') }}" style="width:140px;">
    </div>

    <div class="menu">
        <a href="{{ route('doctor.profile') }}">
            Dashboard
        </a>

        <a href="{{ route('doctor.appointments') }}" class="active">
            Appointments
        </a>

        <a href="{{ route('doctor.signature') }}">
            Electronic Signature
        </a>

    </div>
</div>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<header class="topbar">
    <div class="left-section">
        <div class="menu-btn" onclick="openSidebar()">☰</div>

        <div class="brand-wrapper">
            <div class="brand">NUH</div>
            <div class="sub-brand">Doctor Dashboard</div>
        </div>
    </div>

    <div class="right">
        <div class="search-box">
            🔍
            <input type="text" placeholder="Search patients...">
        </div>

        <div class="icon-circle">🔔</div>

        <a href="{{ route('doctor.profile') }}" class="profile-link">
            <span class="doctor-name">Doctor Ahmed</span>
            <div class="avatar">A</div>
        </a>

    </div>

</header>

<div class="container">

    <h1>Doctor Appointments</h1>
    <div class="subtitle">hospital appointments & Private clinics</div>

    <!-- Hospital -->
    <div class="section-title">Hospital</div>

    <div class="table-box">
        <div class="table-header">
            <div>Patient Name</div>
            <div>Time</div>
            <div>Reason for Visit</div>
            <div>Action</div>
        </div>

        @forelse($hospitalAppointments as $appointment)
            <div class="row">
                <div class="patient">
                    <div class="circle">
                        {{ strtoupper(substr($appointment->first_name,0,1)) }}
                    </div>
                    {{ $appointment->first_name }} {{ $appointment->last_name }}
                </div>

                <div>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</div>

                <div>{{ $appointment->reason }}</div>

                <div><a href="#" class="btn">view</a></div>
            </div>
        @empty
            <div class="row">
                <div style="grid-column: 1 / -1;">No hospital appointments</div>
            </div>
        @endforelse
    </div>
    <!-- Private Clinics -->
    <div class="section-title">Private Clinics</div>

    <div class="table-box">
        <div class="table-header">
            <div>Patient Name</div>
            <div>Time</div>
            <div>Reason for Visit</div>
            <div>Action</div>
        </div>

        @forelse($privateAppointments as $appointment)
            <div class="row">
                <div class="patient">
                    <div class="circle">
                        {{ strtoupper(substr($appointment->first_name,0,1)) }}
                    </div>
                    {{ $appointment->first_name }} {{ $appointment->last_name }}
                </div>

                <div>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</div>

                <div>{{ $appointment->reason }}</div>

                <div><a href="#" class="btn">view</a></div>
            </div>
        @empty
            <div class="row">
                <div style="grid-column: 1 / -1;">No private appointments</div>
            </div>
        @endforelse
    </div>
<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('active');
        document.getElementById('overlay').classList.add('active');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('active');
        document.getElementById('overlay').classList.remove('active');
    }
</script>
</body>
</html>
