<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Positions Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>

        body{
            margin:0;
            font-family:'Segoe UI',sans-serif;
            background:linear-gradient(135deg,#cfe6ff,#2a82c9);
            min-height:100vh;
        }

        /* ===== Sidebar ===== */

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

        /* ===== Header ===== */

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

        /* ===== Main ===== */

        .main{
            padding:40px;
        }

        /* ===== Table Card ===== */

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

        /* Buttons */

        .btn-cv{
            background:#154484;
            color:white;
            border-radius:25px;
            padding:6px 18px;
            border:none;
            font-size:14px;
        }

        .btn-approve{
            background:#2ecc71;
            color:white;
            border-radius:25px;
            border:none;
            padding:6px 18px;
            font-size:14px;
        }

        .btn-reject{
            background:#e74c3c;
            color:white;
            border-radius:25px;
            border:none;
            padding:6px 18px;
            font-size:14px;
        }

        .badge-approved{
            background:#2ecc71;
            padding:6px 16px;
            border-radius:20px;
            color:white;
        }

    </style>
</head>

<body>

<div class="overlay" onclick="toggleSidebar()"></div>

<!-- ===== Sidebar (Leave Menu) ===== -->

<div class="sidebar" id="sidebar">

    <div class="logo-container">
        <img src="{{ asset('images/logo_Image.png') }}">
    </div>

    <div class="menu-links">

        <a href="{{ route('staff.leave.index') }}">Leave Requests</a>

        <a href="{{ route('staff.medical.positions') }}" class="active-link">
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

    </div>

</div>

<!-- ===== Header ===== -->

<div class="header-section">

    <div class="header-top">

        <div class="menu-icon" onclick="toggleSidebar()">☰</div>

        <div>
            <h3 class="logo-title">NUH</h3>
            <span class="sub-title">Administrative Dashboard</span>
        </div>

    </div>

    <div class="page-title">

        <h2>Medical Positions Management</h2>
        <p>Manage and track all Medical Positions</p>

    </div>

</div>

<!-- ===== Main ===== -->

<div class="main">

    <div class="card-box">

        <table class="table mb-0">

            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Department</th>
                <th>CV</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>

            @foreach($positions as $position)

                <tr>

                    <td>{{ $position->id }}</td>

                    <td>{{ $position->name }}</td>

                    <td>{{ $position->age }}</td>

                    <td>{{ ucfirst($position->gender) }}</td>

                    <td>{{ $position->phone }}</td>

                    <td>{{ $position->department->name_en ?? '-' }}</td>

                    <td>
                        <a href="{{ asset('storage/'.$position->cv) }}" class="btn-cv">
                            View CV
                        </a>
                    </td>

                    <td class="d-flex gap-2">

                        @if($position->status == 'pending')

                            <form method="POST" action="{{ route('staff.medical.approve',$position->id) }}">
                                @csrf
                                <button class="btn-approve">Approve</button>
                            </form>

                            <form method="POST" action="{{ route('staff.medical.reject',$position->id) }}">
                                @csrf
                                <button class="btn-reject">Reject</button>
                            </form>

                        @elseif($position->status == 'approved')

                            <span class="badge-approved">Approved</span>

                        @endif

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
