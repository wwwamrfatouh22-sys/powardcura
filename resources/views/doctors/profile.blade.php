<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #5dade2, #1e69de);
            overflow-x: hidden;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            color: white;
        }

        .left-section { display: flex; align-items: center; gap: 14px; }
        .menu-btn { font-size: 28px; cursor: pointer; }
        .brand { font-size: 22px; font-weight: bold; }

        .right-section { display: flex; align-items: center; gap: 18px; }

        .search-box {
            background: #f3f4f6;
            padding: 10px 18px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            width: 200px;
        }

        .icon-circle {
            width: 42px;
            height: 42px;
            background: #f3f4f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            font-size: 18px;
        }

        .profile-link {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: bold;
            cursor: pointer;
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: #123a6f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .container { padding: 30px 80px; }

        .card {
            background: #f3f4f6;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            margin-bottom: 35px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-box {
            background: #f3f4f6;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; text-align: left; }
        th { border-bottom: 2px solid #ccc; }
        tr { border-bottom: 1px solid #ddd; }

        .btn {
            background: #123a6f;
            color: white;
            padding: 8px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }

        /* ===== PROFILE MODAL ===== */

        .profile-modal{
            position:fixed;
            inset:0;
            background:rgba(0,0,0,0.5);
            display:none;
            justify-content:center;
            align-items:center;
            z-index:2000;
        }

        .profile-modal.active{
            display:flex;
        }

        .profile-card{
            background:#f3f4f6;
            width:600px;
            padding:40px;
            border-radius:25px;
            box-shadow:0 20px 40px rgba(0,0,0,0.3);
            text-align:center;
        }

        .avatar-large{
            width:120px;
            height:120px;
            background:#123a6f;
            border-radius:50%;
            margin:0 auto 20px;
            color:white;
            font-size:50px;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .modal-actions{
            margin-top:25px;
            display:flex;
            justify-content:center;
            gap:20px;
        }

        .btn-cancel{
            background:#ddd;
            border:none;
            padding:8px 18px;
            border-radius:8px;
            cursor:pointer;
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

        .sidebar-logo img {
            width: 120px;
        }

        .menu a {
            display: block;
            padding: 12px 18px;
            margin-bottom: 15px;
            border-radius: 12px;
            text-decoration: none;
            color: black;
            font-weight: 600;
        }

        .menu a.active,
        .menu a:hover {
            background: #123a6f;
            color: white;
        }

        /* ===== OVERLAY ===== */

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
        .btn-primary {
            background: #123a6f;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;   /* دي اللي بتشيل الخط */
            display: inline-block;
            font-weight: bold;
        }

        .btn-primary:hover {
            background: #0f2f57;
        }
        .btn-cancel {
            background: #e5e7eb;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
        }
        .brand-wrapper{
            display:flex;
            flex-direction:column;
            line-height:1.2;
        }

        .sub-brand{
            font-size:14px;
            font-weight:normal;
        }
    </style>
</head>
<body>

@php
    $doctor = auth()->guard('doctor')->user();
@endphp
<div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('images/logo_Image.png') }}" alt="NUH">

    </div>

    <div class="menu">

        <a href="{{ route('doctor.profile') }}" class="active">Dashboard</a>

        <a href="{{ route('doctor.appointments') }}">
            Appointments
        </a>

        <a href="{{ route('doctor.signature') }}">
            Electronic Signature
        </a>
    </div>
</div>

<!-- OVERLAY -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- TOPBAR -->
<div class="topbar">
    <div class="left-section">
        <div class="menu-btn" onclick="openSidebar()">☰</div>
        <div class="brand-wrapper">
            <div class="brand">NUH</div>
            <div class="sub-brand">Doctor Dashboard</div>
        </div>
    </div>

    <div class="right-section">
        <div class="search-box">
            🔍
            <input type="text" placeholder="Search patients...">
        </div>


        <div class="icon-circle">🔔</div>

        <div class="profile-link" onclick="openProfileModal()">
            {{ $doctor->name }}
            <div class="avatar">
                {{ strtoupper(substr($doctor->name,0,1)) }}
            </div>
        </div>
    </div>
</div>

<!-- CONTENT -->
<div class="container">

    <div class="card">
        <h1>Welcome, {{ $doctor->name }}</h1>
        <p>Department: {{ $doctor->department->name ?? 'No Department' }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <h3>Today's Appointments</h3>
            <p>{{ $appointmentsCount }}</p>
        </div>

        <div class="stat-box">
            <h3>Total Patients</h3>
            <p>4</p>
        </div>

        <div class="stat-box">
            <h3>Signed Documents</h3>
            <p>10</p>
        </div>
    </div>

    <div class="card">
        <h2>Recent Patients</h2>

        <table>
            <tr>
                <th>Patient Name</th>
                <th>Patient Email</th>
                <th>Diagnosis</th>
                <th>Action</th>
            </tr>

            @foreach($recentAppointments as $appointment)
                <tr>
                    <td>{{ $appointment->first_name }} {{ $appointment->last_name }}</td>
                    <td>{{ $appointment->email }}</td>
                    <td>{{ $appointment->reason ?? 'N/A' }}</td>
                    <td><a href="#" class="btn">View</a></td>
                </tr>
            @endforeach
        </table>
    </div>

</div>

<!-- PROFILE MODAL -->
<div id="profileModal" class="profile-modal">
    <div class="profile-card">

        <div class="avatar-large">
            {{ strtoupper(substr($doctor->name,0,1)) }}
        </div>

        <h2>DR: {{ $doctor->name }}</h2>

        <div>
            <p>🕒 {{ $doctor->experience ?? 0 }} years experience</p>
            <p>{{ $doctor->gender ?? 'N/A' }} |
                {{ $doctor->age ?? '-' }} Years |
                {{ $doctor->nationality ?? 'EGY' }}</p>
        </div>

        <p style="margin-top:20px;">
            {{ $doctor->description ?? 'No description available.' }}
        </p>

        <div class="modal-actions">
            <a href="{{ route('doctor.leave.form') }}" class="btn-primary">
                Leave Requests
            </a>

            <button onclick="closeProfileModal()" class="btn-cancel">
                Cancel
            </button>
        </div>
    </div>
</div>

{{--<script>--}}
{{--    function openProfileModal(){--}}
{{--        document.getElementById('profileModal').classList.add('active');--}}
{{--    }--}}

{{--    function closeProfileModal(){--}}
{{--        document.getElementById('profileModal').classList.remove('active');--}}
{{--    }--}}
{{--</script>--}}
<script>
    function openSidebar(){
        document.getElementById('sidebar').classList.add('active');
        document.getElementById('overlay').classList.add('active');
    }

    function closeSidebar(){
        document.getElementById('sidebar').classList.remove('active');
        document.getElementById('overlay').classList.remove('active');
    }

    function openProfileModal(){
        document.getElementById('profileModal').classList.add('active');
    }

    function closeProfileModal(){
        document.getElementById('profileModal').classList.remove('active');
    }
</script>
</body>
</html>
