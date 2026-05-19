<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Complaints Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            margin:0;
            font-family:Arial, Helvetica, sans-serif;
            background:linear-gradient(135deg,#cfe6ff,#2a82c9);
            min-height:100vh;
        }

        .card-soft {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 16px 35px rgba(0,0,0,0.12);
        }

        .nav-chip {
            border-radius:999px;
            padding:8px 14px;
            font-size:13px;
            border:1px solid #d9e3ef;
            background:#fff;
            text-decoration:none;
            color:#0b3a78;
        }

        .nav-chip.active {
            background:#0b3a78;
            color:#fff;
            border-color:#0b3a78;
        }

        /* Sidebar */

        .sidebar{
            width:250px;
            height:100vh;
            position:fixed;
            left:-250px;
            top:0;
            background:#ffffff;
            padding:30px 20px;
            box-shadow:4px 0 25px rgba(0,0,0,0.08);
            transition:0.3s;
            z-index:1001;
            display:flex;
            flex-direction:column;
        }

        .sidebar.active{
            left:0;
        }

        .logo-container{
            text-align:center;
            margin-bottom:40px;
        }

        .logo-container img{
            width:120px;
        }

        .menu-links{
            display:flex;
            flex-direction:column;
            gap:10px;
        }

        .menu-links a{
            padding:14px 18px;
            border-radius:14px;
            text-decoration:none;
            color:#444;
            font-weight:500;
            font-size:16px;
            transition:0.3s;
        }

        .menu-links a:hover{
            background:#eef6ff;
            transform:translateX(5px);
        }

        .active-link{
            background:#dbeaff;
            color:#154484!important;
            font-weight:600;
        }

        /* Overlay */

        .overlay{
            position:fixed;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.3);
            top:0;
            left:0;
            display:none;
            z-index:1000;
        }

        .overlay.active{
            display:block;
        }

        /* Header */

        .header-section{
            background:linear-gradient(135deg,#eaf4ff,#6fa4cf);
            padding:30px 40px;
            border-bottom-left-radius:25px;
            border-bottom-right-radius:25px;
        }

        .header-top{
            display:flex;
            align-items:center;
            gap:15px;
        }

        .menu-icon{
            font-size:30px;
            cursor:pointer;
        }

        .logo-title{
            margin:0;
            font-weight:700;
            font-size:22px;
        }

        .sub-title{
            font-size:14px;
            color:#444;
        }

        .page-title{
            margin-top:40px;
        }

        .page-title h2{
            font-size:28px;
            font-weight:700;
        }

        .page-title p{
            font-size:16px;
            color:#444;
        }

        /* Main */

        .main{
            padding:40px;
        }

        /* Table */

        .card-box{
            background:white;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 25px 60px rgba(0,0,0,0.15);
            margin-top:30px;
        }

        .table thead{
            background:#154484;
            color:white;
            font-size:17px;
        }

        .table th{
            padding:18px;
            font-weight:600;
        }

        .table td{
            padding:18px;
            font-size:16px;
            vertical-align:middle;
        }

        /* Priority */

        .priority-high{
            background:#e74c3c;
            color:white;
            padding:5px 12px;
            border-radius:20px;
        }

        .priority-medium{
            background:#f39c12;
            color:white;
            padding:5px 12px;
            border-radius:20px;
        }

        .priority-low{
            background:#2ecc71;
            color:white;
            padding:5px 12px;
            border-radius:20px;
        }

        .status-badge, .priority-badge {
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .status-pending{
            background:#fff2cc;
            color:#7a5600;
        }

        .status-progress{
            background:#d9ecff;
            color:#0b3a78;
        }

        .status-resolved{
            background:#d9f5df;
            color:#1d6b2a;
        }

        .message-cell {
            min-width: 260px;
            max-width: 420px;
            white-space: normal;
        }

        /* Buttons */

        .btn-resolve{
            background:#2ecc71;
            color:white;
            border-radius:25px;
            padding:6px 18px;
            border:none;
        }

        .btn-escalate{
            background:#e74c3c;
            color:white;
            border-radius:25px;
            padding:6px 18px;
            border:none;
        }

    </style>
</head>

<body>

<div class="overlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->

<div class="sidebar" id="sidebar">

    <div class="logo-container">
        <img src="{{ asset('images/logo_Image.png') }}">
    </div>

    <div class="menu-links">
        <a href="{{ route('staff.medical.positions') }}">Medical Positions</a>

        <a href="{{ route('staff.training.programs') }}">Training Programs</a>

        <a href="{{ route('staff.complaints') }}" class="active-link">
            Complaints
        </a>

    </div>

</div>

<!-- Header -->

<div class="header-section">

    <div class="header-top">

        <div class="menu-icon" onclick="toggleSidebar()">☰</div>

        <div>
            <h3 class="logo-title">NUH</h3>
            <span class="sub-title">Staff Dashboard</span>
        </div>

    </div>

    <div class="page-title">

        <h2>Complaints Management</h2>
        <p>Manage and track all Complaints</p>

    </div>

</div>

<!-- Main -->

<div class="main">

    <div class="card-box">

        <table class="table mb-0">

            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>

            @foreach($complaints as $complaint)

                <tr>

                    <td>{{ $complaint->id }}</td>
                    <td>{{ $complaint->name }}</td>
                    <td>{{ $complaint->email }}</td>
                    <td>{{ $complaint->phone }}</td>
                    <td>
                        @if($complaint->priority == 'high')
                            <span class="priority-high">High</span>
                        @elseif($complaint->priority == 'medium')
                            <span class="priority-medium">Medium</span>
                        @else
                            <span class="priority-low">Low</span>
                        @endif
                    </td>

                    <td>
                        @if($complaint->status == 'pending')
                            <span class="status-pending">Pending</span>
                        @elseif($complaint->status == 'in_progress')
                            <span class="status-progress">In Progress</span>
                        @else
                            <span class="status-resolved">Resolved</span>
                        @endif
                    </td>

                    <td class="d-flex gap-2">

                        <form method="POST" action="{{ route('staff.complaint.resolve',$complaint->id) }}">
                            @csrf
                            <button class="btn-resolve">Resolve</button>
                        </form>

                        <form method="POST" action="{{ route('staff.complaint.escalate',$complaint->id) }}">
                            @csrf
                            <button class="btn-escalate">Escalate</button>
                        </form>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>

<script>

    function toggleSidebar(){
        document.getElementById("sidebar").classList.toggle("active");
        document.querySelector(".overlay").classList.toggle("active");
    }

</script>

</body>
</html>
