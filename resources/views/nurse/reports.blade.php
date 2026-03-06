<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Reports</title>
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

        .high{background:#1e63d5;}
        .normal{background:#9ca3af;}
        .urgent{background:#2563eb;}

        .completed{background:#1e63d5;}
        .pending{background:#9ca3af;}
        .review{background:#6c757d;}
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <ul>
        <li><a href="{{ route('nurse.dashboard') }}">🏠 Dashboard</a></li>
        <li><a href="{{ route('patients.index') }}">👥 Patients</a></li>
        <li><a href="{{ route('nurse.appointments') }}">📅 Appointments</a></li>
        <li>
            <a href="{{ route('nurse.reports') }}" class="active">
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
                <div class="subtitle">Medical Reports</div>
            </div>
        </div>
        <div>👩‍⚕ Nurse Emily</div>
    </div>

    <div class="content">
        <div class="card">

            <h2>Medical Reports</h2>
            <p>All reports - {{ $reports->count() }} total</p>

            <table>
                <thead>
                <tr>
                    <th>Report ID</th>
                    <th>Report Type</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Department</th>
                    <th>Generated Date</th>
                    <th>Priority</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>RPT-{{ str_pad($report->id,3,'0',STR_PAD_LEFT) }}</td>

                        <td class="d-flex align-items-center gap-2">

                            <!-- أيقونة ملف -->
                            <i class="bi bi-file-earmark-text text-secondary"></i>

                            <!-- اسم التقرير -->
                            <span>{{ $report->report_type }}</span>

                            <!-- Checkbox -->
                            <input type="checkbox"
                                   class="review-toggle ms-auto"
                                   data-id="{{ $report->id }}"
                                {{ $report->is_reviewed ? 'checked' : '' }}>

                        </td>
                        <td style="display:flex;align-items:center;">
                            <div class="avatar">
                                {{ strtoupper(substr($report->patient->full_name ?? 'P',0,1)) }}
                            </div>
                            {{ $report->patient->full_name ?? '-' }}
                        </td>

                        <td>{{ $report->doctor->name ?? '-' }}</td>
                        <td>{{ $report->department_id }}</td>

                        <td>{{ \Carbon\Carbon::parse($report->created_at)->format('M d, Y') }}</td>

                        <td>
                            @if($report->priority == 'High')
                                <span class="badge high">High</span>
                            @elseif($report->priority == 'Urgent')
                                <span class="badge urgent">Urgent</span>
                            @else
                                <span class="badge normal">Normal</span>
                            @endif
                        </td>

                        <td>
                            @if($report->status == 'Completed')
                                <span class="badge completed">Completed</span>
                            @elseif($report->status == 'Pending')
                                <span class="badge pending">Pending</span>
                            @else
                                <span class="badge review">In Review</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:20px;">
                            No Reports Found
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>

        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.review-toggle').forEach(function(checkbox) {

        checkbox.addEventListener('change', function() {

            let reportId = this.dataset.id;

            fetch(`/nurse/reports/${reportId}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Updated');
                });

        });

    });
</script>

</body>
</html>
