<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Appointments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        *{box-sizing:border-box;margin:0;padding:0;font-family:Arial,sans-serif;}
        body{display:flex;min-height:100vh;background:linear-gradient(to right,#3b82f6,#1e3a8a);}

        /* Sidebar */
        .sidebar{
            width:240px;background:#f3f4f6;padding:25px 15px;
            position:fixed;height:100%;left:-240px;transition:.3s;
        }
        .sidebar.active{left:0;}
        .sidebar ul{list-style:none;}
        .sidebar ul li{margin-bottom:15px;}
        .sidebar ul li a{
            display:flex;align-items:center;gap:10px;
            padding:12px 18px;border-radius:12px;
            text-decoration:none;color:#333;font-weight:500;
            transition:.3s;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active{background:#2b4ea2;color:#fff;}

        /* Main */
        .main{flex:1;margin-left:0;transition:.3s;width:100%;}
        .main.shift{margin-left:240px;}

        .topbar{
            height:90px;background:linear-gradient(to right,#dbeafe,#bfdbfe);
            display:flex;align-items:center;justify-content:space-between;
            padding:0 40px;
        }
        .menu-icon{font-size:24px;cursor:pointer;}
        .logo{font-size:22px;font-weight:bold;}
        .subtitle{font-size:14px;color:#555;}

        .content{padding:40px;}
        .card{
            background:white;border-radius:20px;padding:30px;
            box-shadow:0 15px 35px rgba(0,0,0,0.15);
        }

        table{width:100%;border-collapse:collapse;}
        thead{background:#163D7A;color:white;}
        th,td{padding:14px;}
        tbody tr{border-bottom:1px solid #eee;}

        .avatar{
            width:40px;height:40px;border-radius:50%;
            background:#163D7A;color:white;
            display:flex;align-items:center;justify-content:center;
            font-weight:bold;margin-right:10px;
        }

        .badge{
            padding:6px 14px;border-radius:20px;
            font-size:12px;font-weight:bold;color:white;
        }

        .confirmed{background:#1e63d5;}
        .pending{background:#9ca3af;}
        .completed{background:#6c757d;}
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="{{ route('nurse.dashboard') }}">🏠 Dashboard</a></li>
        <li><a href="{{ route('patients.index') }}">👥 Patients</a></li>
        <li>
            <a href="{{ route('nurse.appointments') }}"
               class="active">📅 Appointments</a>
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

<!-- Main -->
<div class="main" id="main">

    <div class="topbar">
        <div style="display:flex;align-items:center;gap:15px;">
            <div class="menu-icon" onclick="toggleSidebar()">☰</div>
            <div>
                <div class="logo">NUH</div>
                <div class="subtitle">All Appointments</div>
            </div>
        </div>
        <div>👩‍⚕ Nurse Emily</div>
    </div>

    <div class="content">
        <div class="card">

            <h2>All Appointments</h2>
            <p>Scheduled appointments - {{ $appointments->count() }} total</p>

            <table>
                <thead>
                <tr>
                    <th>Patient</th>
                    <th>Appointment ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor</th>
                    <th>Department</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                @forelse($appointments as $appointment)
                    <tr>
                        <td style="display:flex;align-items:center;">
                            <div class="avatar">
                                {{ strtoupper(substr($appointment->patient->full_name ?? 'P',0,1)) }}
                            </div>
                            {{ $appointment->patient->full_name ?? '' }}
                        </td>

                        <td>{{ $appointment->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}</td>
                        <td>{{ $appointment->doctor->name ?? '-' }}</td>
                        <td>{{ $appointment->department->name_en ?? '-' }}</td>
                        <td>{{ $appointment->type ?? 'Consultation' }}</td>

                        <td>
                            @if($appointment->status == 'Confirmed')
                                <span class="badge confirmed">Confirmed</span>

                            @elseif($appointment->status == 'Pending')
                                <span class="badge pending">Pending</span>

                            @elseif($appointment->status == 'Completed')
                                <span class="badge completed">Completed</span>

                            @else
                                <span class="badge pending">
                                {{ $appointment->status ?? 'Pending' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:20px;">
                            No Appointments Found
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
