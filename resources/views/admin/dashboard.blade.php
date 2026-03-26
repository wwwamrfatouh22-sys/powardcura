<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <style>

        body{
            font-family: Arial, sans-serif;
            margin:0;
            background: linear-gradient(90deg,#f5f7fa,#4facfe);
        }

        /* SIDEBAR */

        .sidebar{
            position:fixed;
            top:0;
            left:0;
            width:240px;
            height:100%;
            background:#f3f4f6;
            padding:25px 20px;
            transform:translateX(-100%);
            transition:0.3s;
            z-index:1000;
            box-shadow:2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.active{
            transform:translateX(0);
        }

        .logo{
            margin-bottom:25px;
        }

        .logo h2{
            margin:0;
        }

        .logo span{
            font-size:13px;
            color:#777;
        }

        /* MENU */

        .menu{
            list-style:none;
            padding:0;
            margin-top:20px;
        }

        .menu li{
            display:flex;
            align-items:center;
            gap:12px;
            padding:12px 15px;
            margin-bottom:12px;
            border-radius:12px;
            cursor:pointer;
            font-size:15px;
            color:#333;
        }

        .menu li:hover{
            background:#e5e7eb;
        }

        .menu li.active{
            background:#0d3b66;
            color:white;
            box-shadow:0 4px 10px rgba(0,0,0,0.2);
        }

        /* HEADER */

        .header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:20px 40px;
            background:white;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }

        .menu-btn{
            font-size:22px;
            cursor:pointer;
            margin-right:20px;
        }

        .search{
            padding:10px;
            border-radius:20px;
            border:1px solid #ddd;
            width:250px;
        }

        .profile{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .avatar{
            width:35px;
            height:35px;
            border-radius:50%;
            background:#0d3b66;
            color:white;
            display:flex;
            align-items:center;
            justify-content:center;
        }

        /* CONTENT */

        .container{
            padding:40px;
            transition:0.3s;
        }

        .container.shift{
            margin-left:240px;
        }

        .welcome{
            background:white;
            padding:25px;
            border-radius:15px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
            margin-bottom:30px;
        }

        .dashboard-grid{
            display:grid;
            grid-template-columns:repeat(3,1fr);
            gap:25px;
        }

        .card{
            background:white;
            padding:30px;
            border-radius:15px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
        }

        .card-icon{
            background:#0d3b66;
            width:45px;
            height:45px;
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            color:white;
            margin-bottom:10px;
        }

        .card-number{
            font-size:26px;
            font-weight:bold;
        }

        .card-text{
            color:#666;
        }

    </style>
</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar" id="sidebar">

    <div class="logo">
        <h2>NUH</h2>
        <span>Admin Dashboard</span>
    </div>

    <div class="logo-box">
        <img src="{{ asset('images/logo_Image.png') }}" alt="NUH Logo">
    </div>

    <ul class="menu">

        <li class="active">🏠 Dashboard</li>
        <li>
            <a href="{{ route('admin.appointments') }}">📅 Appointments</a>
        </li>
        <li>👥 Patients</li>
        <li>🩺 Doctors</li>
        <li>🏥 Rooms</li>
        <li>🏢 Departments</li>
        <li>⚙️ Settings</li>

    </ul>

</div>

<!-- HEADER -->

<div class="header">

    <div style="display:flex;align-items:center;gap:10px">

        <div class="menu-btn" onclick="toggleSidebar()">☰</div>

        <input class="search" placeholder="Search patients...">

    </div>

    <div class="profile">
        <div>
            <strong>Admin Robert</strong><br>
            <span style="font-size:12px;color:gray">Administrator</span>
        </div>

        <div class="avatar">
            R
        </div>
    </div>

</div>

<!-- CONTENT -->

<div class="container" id="main">

    <div class="welcome">
        <h2>Welcome to Hospital Dashboard</h2>
        <p>Overview of your hospital management system</p>
    </div>

    <div class="dashboard-grid">

        <div class="card">
            <div class="card-icon">👥</div>
            <div class="card-number">{{ $totalPatients }}</div>
            <div class="card-text">Total Patients</div>
        </div>

        <div class="card">
            <div class="card-icon">👨‍⚕️</div>
            <div class="card-number">{{ $totalDoctors }}</div>
            <div class="card-text">Total Doctors</div>
        </div>

        <div class="card">
            <div class="card-icon">📅</div>
            <div class="card-number">{{ $totalAppointments }}</div>
            <div class="card-text">Total Appointments</div>
        </div>

        <div class="card">
            <div class="card-icon">🏥</div>
            <div class="card-number">{{ $totalRooms }}</div>
            <div class="card-text">Total Rooms</div>
        </div>

        <div class="card">
            <div class="card-icon">🛏</div>
            <div class="card-number">{{ $availableRooms }}</div>
            <div class="card-text">Available Rooms</div>
        </div>

        <div class="card">
            <div class="card-icon">📆</div>
            <div class="card-number">{{ $todayAppointments }}</div>
            <div class="card-text">Today Appointments</div>
        </div>

    </div>

</div>

<script>

    function toggleSidebar(){
        let sidebar=document.getElementById("sidebar")
        let main=document.getElementById("main")

        sidebar.classList.toggle("active")
        main.classList.toggle("shift")
    }

    document.addEventListener("click", function(e){

        let sidebar = document.getElementById("sidebar")
        let menuBtn = document.querySelector(".menu-btn")

        if(
            sidebar.classList.contains("active") &&
            !sidebar.contains(e.target) &&
            !menuBtn.contains(e.target)
        ){
            sidebar.classList.remove("active")
            document.getElementById("main").classList.remove("shift")
        }

    })

</script>

</body>
</html>
