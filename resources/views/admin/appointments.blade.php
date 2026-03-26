<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            margin-bottom:20px;
        }

        .logo span{
            font-size:13px;
            color:#777;
        }

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
        }

        .menu li:hover{
            background:#e5e7eb;
        }

        .menu li.active{
            background:#0d3b66;
            color:white;
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

        /* PAGE TITLE */

        .welcome{
            display:flex;
            justify-content:space-between;
            align-items:center;
            background:white;
            padding:25px;
            border-radius:15px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
            margin-bottom:30px;
        }

        .add-btn{
            background:#0d3b66;
            color:white;
            padding:10px 20px;
            border-radius:10px;
            cursor:pointer;
        }

        /* TABLE */

        .appointment-table{
            width:100%;
            background:white;
            border-radius:15px;
            overflow:hidden;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
            border-collapse:collapse;
        }

        .appointment-table th{
            background:#0d3b66;
            color:white;
            padding:15px;
            text-align:left;
        }

        .appointment-table td{
            padding:15px;
            border-bottom:1px solid #eee;
        }

        .status{
            padding:6px 12px;
            border-radius:15px;
            font-size:13px;
        }

        .status.completed{
            background:#4caf50;
            color:white;
        }

        .status.confirmed{
            background:#0d3b66;
            color:white;
        }

        .status.pending{
            background:#ddd;
        }

        /* MODAL */

        .modal{
            display:none;
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.4);
            justify-content:center;
            align-items:center;
            z-index:2000;
        }

        .modal-content{
            background:white;
            padding:30px;
            border-radius:12px;
            width:400px;
        }

        .modal-content input,
        .modal-content select{
            width:100%;
            padding:10px;
            margin-bottom:15px;
            border:1px solid #ccc;
            border-radius:8px;
        }

        .modal-content button{
            width:100%;
            padding:12px;
            background:#0d3b66;
            color:white;
            border:none;
            border-radius:8px;
            cursor:pointer;
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

    <ul class="menu">

        <li>
            <a href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
        </li>

        <li class="active">
            📅 Appointments
        </li>

        <li class="{{ request()->routeIs('admin.patients') ? 'active' : '' }}">
            <a href="{{ route('admin.patients') }}">👤 Patients</a>
        </li>
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

        <div class="avatar">R</div>

    </div>

</div>
@if(session('success'))
    <div style="background:#4CAF50;color:white;padding:10px;border-radius:8px;margin-bottom:20px;">
        {{ session('success') }}
    </div>
@endif
<!-- CONTENT -->

<div class="container" id="main">

    <div class="welcome">

        <div>
            <h2>Appointments Management</h2>
            <p>Manage and track all hospital appointments</p>
        </div>

        <div class="add-btn" onclick="openModal()">+ Add New Appointment</div>

    </div>


    <table class="appointment-table">

        <thead>
        <tr>
            <th>Patient Name</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>

        @foreach($appointments as $appointment)

            <tr>

                <td>{{ $appointment->patient->full_name ?? 'Unknown' }}</td>

                <td>{{ $appointment->doctor->name }}</td>

                <td>{{ $appointment->date }}</td>

                <td>{{ $appointment->time }}</td>

                <td>
<span class="status {{ strtolower($appointment->status) }}">
{{ $appointment->status }}
</span>
                </td>

                <td>
                    <!-- Edit -->
                    <a href="{{ route('admin.appointments.edit', $appointment->id) }}">
                        <i class="fa fa-edit me-2"></i>
                    </a>

                    <!-- Delete -->
                    <form action="{{ route('admin.appointments.delete', $appointment->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="border:none; background:none; padding:0;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>


<!-- MODAL -->

<div class="modal" id="appointmentModal">

    <div class="modal-content">

        <h2>Add Appointment</h2>

        <form action="{{ route('admin.appointments.store') }}" method="POST">
            @csrf

            <label>Patient</label>
            <select name="patient_id" required>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}">
                        {{ $patient->full_name }}
                    </option>
                @endforeach
            </select>

            <label>Doctor</label>
            <select name="doctor_id" required>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}">
                        {{ $doctor->name }}
                    </option>
                @endforeach
            </select>

            <label>Date</label>
            <input type="date" name="date" required>

            <label>Time</label>
            <input type="time" name="time" required>

            <label>Status</label>
            <select name="status">
                <option value="Pending">Pending</option>
                <option value="Confirmed">Confirmed</option>
                <option value="Completed">Completed</option>
            </select>

            <button type="submit">Save Appointment</button>

        </form>

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


    function openModal(){
        document.getElementById("appointmentModal").style.display="flex"
    }

    window.onclick=function(e){

        let modal=document.getElementById("appointmentModal")

        if(e.target==modal){
            modal.style.display="none"
        }

    }

</script>

</body>
</html>
