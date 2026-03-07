<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NUH Administrative Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            margin:0;
            font-family:'Segoe UI',sans-serif;
            background:linear-gradient(135deg,#cfe6ff,#2a82c9);
            min-height:100vh;
            overflow-x:hidden;
        }

        /* Top Navbar */

        .top-navbar{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:15px 40px;
        }

        .search-box{
            background:white;
            border-radius:30px;
            padding:8px 20px;
            width:320px;
        }

        .search-box input{
            border:none;
            outline:none;
            width:100%;
        }

        .right-nav{
            display:flex;
            align-items:center;
            gap:20px;
        }

        .notification{
            background:white;
            width:40px;
            height:40px;
            border-radius:50%;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        .user-avatar{
            width:40px;
            height:40px;
            border-radius:50%;
            background:#1e4b8c;
            color:white;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        /* Sidebar */

        .sidebar{
            width:250px;
            height:100vh;
            position:fixed;
            left:-250px;
            top:0;
            background:white;
            padding:30px 20px;
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
            gap:8px;
        }

        .menu-links a{
            padding:12px 18px;
            border-radius:14px;
            text-decoration:none;
            color:#444;
        }

        .menu-links a:hover{
            background:#eef6ff;
        }

        .active-link{
            background:#dbeaff;
            color:#154484!important;
        }

        .logout-btn{
            background:none;
            border:none;
            color:#e74c3c;
            padding:12px 18px;
            text-align:left;
        }

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
            padding:25px 40px 40px 40px;
            border-bottom-left-radius:25px;
            border-bottom-right-radius:25px;
        }

        .header-top{
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .logo-area{
            display:flex;
            align-items:center;
            gap:12px;
        }

        .menu-icon{
            font-size:26px;
            cursor:pointer;
        }

        .logo-title{
            margin:0;
            font-weight:600;
        }

        .sub-title{
            font-size:14px;
            color:#444;
        }

        /* Main */

        .main{
            padding:40px;
        }

        .btn-add{
            background:#154484;
            color:white;
            border-radius:25px;
            padding:10px 22px;
            border:none;
            cursor:pointer;
        }

        .card-box{
            background:white;
            border-radius:20px;
            overflow:hidden;
            box-shadow:0 20px 50px rgba(0,0,0,0.1);
        }

        .table thead{
            background:#154484;
            color:white;
        }

        .btn-approve{
            background:#2ecc71;
            color:white;
            border-radius:25px;
            border:none;
            padding:6px 18px;
        }

        .btn-reject{
            background:#e74c3c;
            color:white;
            border-radius:25px;
            border:none;
            padding:6px 18px;
        }

    </style>
</head>

<body>

<div class="overlay" onclick="closeSidebar()"></div>

<!-- Top Navbar -->

<div class="top-navbar">

    <div class="search-box">
        <input type="text" placeholder="Search...">
    </div>

    <div class="right-nav">

        <div class="notification">🔔</div>

        <div>
            <strong>super</strong><br>
            <small>Administrative</small>
        </div>

        <div class="user-avatar">R</div>

    </div>
</div>

<!-- Sidebar -->

<div class="sidebar" id="mainSidebar">

    <div class="logo-container">
        <img src="{{ asset('images/logo_Image.png') }}">
    </div>

    <div class="menu-links">

        <a class="active-link">Leave Requests</a>

        <a href="{{ route('staff.medical.positions') }}">
            Medical Positions
        </a>

        <a href="{{ route('staff.administrative.positions') }}">
            Administrative Positions
        </a>

        <a href="{{ route('staff.training.programs') }}">
            Training Programs
        </a>

        <a href="{{ route('staff.complaints') }}">
            Complaints
        </a>

        <form method="POST" action="{{ route('staff.logout') }}">
            @csrf
            <button class="logout-btn">Logout</button>
        </form>

    </div>

</div>

<!-- Header -->

<div class="header-section">

    <div class="header-top">

        <div class="logo-area">

            <div class="menu-icon" onclick="toggleSidebar()">☰</div>

            <div>
                <h3 class="logo-title">NUH</h3>
                <span class="sub-title">Administrative Dashboard</span>
            </div>

        </div>

        <a href="{{ route('staff.leave.create') }}" class="btn-add">
            + Add New Leave Request
        </a>
    </div>

</div>

<!-- Main -->

<div class="main">

    <h2 style="font-weight:600;">Leave Requests Management</h2>
    <p style="color:#444;margin-bottom:30px;">Manage and track all leave requests</p>

    <div class="card-box">

        <table class="table mb-0">

            <thead>
            <tr>
                <th>Doctor Name</th>
                <th>Department</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Reason</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>

            @foreach($leaveRequests as $request)

                <tr>

                    <td>{{ $request->doctor->name ?? '-' }}</td>
                    <td>{{ $request->doctor->department->name_en ?? '-' }}</td>
                    <td>{{ $request->start_date }}</td>
                    <td>{{ $request->end_date }}</td>
                    <td>{{ $request->reason }}</td>

                    <td class="d-flex gap-2">

                        <form method="POST" action="{{ route('staff.leave.approve',$request->id) }}">
                            @csrf
                            <button class="btn-approve">Approve</button>
                        </form>

                        <form method="POST" action="{{ route('staff.leave.reject',$request->id) }}">
                            @csrf
                            <button class="btn-reject">Reject</button>
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

        const sidebar = document.getElementById("mainSidebar");

        sidebar.classList.toggle("active");

        document.querySelector('.overlay').classList.toggle('active');

    }

    function closeSidebar(){

        document.getElementById("mainSidebar").classList.remove("active");

        document.querySelector('.overlay').classList.remove('active');

    }

</script>

</body>
</html>
