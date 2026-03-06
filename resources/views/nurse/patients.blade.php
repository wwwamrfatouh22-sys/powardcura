<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Records</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,sans-serif;}

        body{
            display:flex;
            min-height:100vh;
            background:linear-gradient(to right,#3b82f6,#1e3a8a);
        }

        /* ===== Sidebar ===== */
        .sidebar{
            width:240px;
            background:#f3f4f6;
            padding:25px 15px;
            position:fixed;
            height:100%;
            left:-240px;
            transition:0.3s ease;
        }

        .sidebar.active{
            left:0;
        }

        .sidebar ul{list-style:none;}
        .sidebar ul li{margin-bottom:15px;}

        .sidebar ul li a{
            display:flex;
            align-items:center;
            gap:10px;
            padding:12px 18px;
            border-radius:12px;
            text-decoration:none;
            color:#333;
            font-weight:500;
            transition:0.3s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active{
            background:#2b4ea2;
            color:#fff;
        }

        /* ===== Main ===== */
        .main{
            flex:1;
            margin-left:0;
            transition:0.3s;
            width:100%;
        }

        .main.shift{
            margin-left:240px;
        }

        /* ===== Topbar ===== */
        .topbar{
            height:90px;
            background:linear-gradient(to right,#dbeafe,#bfdbfe);
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0 40px;
        }

        .menu-icon{
            font-size:24px;
            cursor:pointer;
        }

        .logo{
            font-size:22px;
            font-weight:bold;
        }

        .subtitle{
            font-size:14px;
            color:#555;
        }

        /* ===== Content ===== */
        .content{
            padding:40px;
        }

        .card{
            background:white;
            border-radius:20px;
            padding:30px;
            box-shadow:0 15px 35px rgba(0,0,0,0.15);
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        thead{
            background:#1e3a8a;
            color:white;
        }

        th,td{
            padding:14px;
        }

        tbody tr{
            border-bottom:1px solid #eee;
        }

        .badge{
            padding:6px 14px;
            border-radius:20px;
            font-size:12px;
            font-weight:bold;
            color:white;
        }

        .badge-active{background:#1e63d5;}
        .badge-critical{background:#dc3545;}
        .badge-discharged{background:#6c757d;}
    </style>
</head>
<body>

<!-- ===== Sidebar ===== -->
<div class="sidebar" id="sidebar">
    <ul>
        <li>
            <a href="{{ route('nurse.dashboard') }}">
                🏠 Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('patients.index') }}"
               class="active">
                👥 Patients
            </a>
        </li>

        <li>
            <a href="{{ route('nurse.appointments') }}"
               class="{{ request()->routeIs('nurse.appointments') ? 'active' : '' }}">
                📅 Appointments
            </a>
        </li>
        <li>
            <a href="{{ route('nurse.reports') }}"
               class="{{ request()->routeIs('nurse.reports') ? 'active' : '' }}">
                📄 Reports
            </a>
        </li>
        <li><a href="#">⚙ Settings</a></li>
    </ul>
</div>

<!-- ===== Main ===== -->
<div class="main" id="main">

    <!-- Topbar -->
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:15px;">
            <div class="menu-icon" onclick="toggleSidebar()">☰</div>
            <div>
                <div class="logo">NUH</div>
                <div class="subtitle">Patient Records</div>
            </div>
        </div>

        <div>👩‍⚕ Nurse Emily</div>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="card">
            <h2>Patient Records</h2>
            <p>All patients - {{ $patients->count() }} records</p>

            <table>
                <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>National ID</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Blood Type</th>
                    <th>Phone</th>
                    <th>DOB</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                @forelse($patients as $patient)
                    <tr>
                        <td>{{ $patient->full_name }}</td>
                        <td>{{ $patient->national_id }}</td>
                        <td>{{ $patient->age ?? '30' }}</td>
                        <td>{{ $patient->gender ?? 'Male' }}</td>
                        <td>{{ $patient->blood_type ?? 'A+' }}</td>
                        <td>{{ $patient->phone }}</td>
                        <td>{{ \Carbon\Carbon::parse($patient->dob)->format('M d, Y') }}</td>
                        <td>
                            @if($patient->status == 'Critical')
                                <span class="badge badge-critical">Critical</span>
                            @elseif($patient->status == 'Discharged')
                                <span class="badge badge-discharged">Discharged</span>
                            @else
                                <span class="badge badge-active">Active</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:20px;">
                            No Patients Found
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>

</div>

<script>
    function toggleSidebar(){
        document.getElementById("sidebar").classList.toggle("active");
        document.getElementById("main").classList.toggle("shift");
    }
</script>

</body>
</html>
