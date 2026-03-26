<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NUH Admin - Patients</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #1e88e5, #90caf9);
            font-family: Arial, sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            background: #f5f5f5;
            position: fixed;
            top: 0;
            left: -280px;
            padding: 24px 20px;
            z-index: 1000;
            transition: 0.3s ease;
            box-shadow: 4px 0 18px rgba(0,0,0,0.12);
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar h4 {
            font-weight: bold;
            font-size: 24px;
            margin-bottom: 8px;
        }

        .sidebar p {
            margin-bottom: 28px;
        }

        .sidebar a {
            display: block;
            padding: 14px 14px;
            color: #333;
            text-decoration: none;
            border-radius: 14px;
            margin-bottom: 12px;
            font-size: 16px;
            transition: 0.2s ease;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background: #0d47a1;
            color: white;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.25);
            opacity: 0;
            visibility: hidden;
            transition: 0.3s ease;
            z-index: 999;
        }

        .overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .main {
            padding: 24px 36px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .menu-toggle {
            border: none;
            background: transparent;
            font-size: 30px;
            cursor: pointer;
            color: #1f2937;
            line-height: 1;
        }

        .search-box {
            width: 300px;
            height: 44px;
            border: none;
            border-radius: 8px;
            padding: 0 14px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .search-box:focus {
            outline: none;
        }

        .admin-box {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }

        .card-table {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
        .add-btn {
            background: white;
            color: #1976d2;
            padding: 10px 20px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: 0.3s;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .add-btn:hover {
            background: #f1f1f1;
            transform: translateY(-2px);
            color: #1565c0;
        }
    </style>
</head>

<body>

<div class="sidebar" id="sidebar">
    <h4>NUH</h4>
    <p class="text-muted">Admin Dashboard</p>

    <a href="{{ route('admin.dashboard') }}">🏠 Dashboard</a>
    <a href="{{ route('admin.appointments') }}">📅 Appointments</a>
    <a href="{{ route('admin.patients') }}" class="active">👤 Patients</a>
    <a href="{{ route('admin.doctors') }}">🩺 Doctors</a>
    <a href="#">🏥 Rooms</a>
    <a href="#">🏢 Departments</a>
    <a href="#">⚙️ Settings</a>
</div>

<div class="overlay" id="overlay"></div>

<div class="main">

    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle">☰</button>
            <input type="text" class="form-control search-box" placeholder="Search patients...">
        </div>

        <div class="admin-box">
            <i class="fa fa-bell"></i>
            <strong>Admin Robert</strong>
        </div>
    </div>

    <h4>Patients Management</h4>
    <p class="text-muted">Manage all patients</p>
    <a href="{{ route('admin.patients.create') }}" class="add-btn">
        + Add Patient
    </a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card-table mt-3">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Phone</th>
                <th>Medical Condition</th>
                <th>Last Visit</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($patients as $patient)
                <tr>
                    <td>{{ $patient->id }}</td>
                    <td>{{ $patient->full_name  }}</td>
                    <td>{{ \Carbon\Carbon::parse($patient->dob)->age }}</td>
                    <td>{{ $patient->gender ?? '-' }}</td>
                    <td>{{ $patient->phone }}</td>
                    <td>{{ $patient->medical_condition }}</td>
                    <td>{{ $patient->last_visit }}</td>

                    <td>
                        <a href="{{ route('admin.patients.edit', $patient->id) }}">
                            <i class="fa fa-edit me-2"></i>
                        </a>
                        <form action="{{ route('admin.patients.delete', $patient->id) }}" method="POST" style="display:inline;">
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

</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    menuToggle.addEventListener('click', function () {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    });

    overlay.addEventListener('click', function () {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });
</script>

</body>
</html>
