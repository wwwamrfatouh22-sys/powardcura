<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NUH Admin - Rooms</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: linear-gradient(135deg, #1e88e5, #90caf9);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #ffffff;
            position: fixed;
            top: 0;
            left: -250px;
            padding: 20px;
            color: #333;
            transition: 0.3s;
            z-index: 1000;
            box-shadow: 4px 0 15px rgba(0,0,0,0.08);
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar h4 {
            color: #111;
        }

        .sidebar p {
            color: #888;
            margin-bottom: 25px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin-bottom: 8px;
            color: #444;
            text-decoration: none;
            border-radius: 12px;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: #f2f6ff;
        }

        .sidebar a.active {
            background: #1e88e5;
            color: white;
        }

        /* Overlay */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.3);
            opacity: 0;
            visibility: hidden;
            transition: 0.3s;
            z-index: 999;
        }

        .overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Main */
        .main {
            padding: 25px;
            transition: 0.3s;
        }

        .main.shift {
            margin-left: 250px;
        }

        /* Topbar */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .menu-toggle {
            font-size: 22px;
            background: white;
            border: none;
            border-radius: 8px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .search-box {
            width: 260px;
            height: 40px;
            border-radius: 20px;
            border: none;
            padding: 0 15px;
        }

        /* Card */
        .card-table {
            background: white;
            border-radius: 20px;
            padding: 20px;
        }

        .add-btn {
            background: white;
            color: #1976d2;
            padding: 10px 18px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }

        /* Status */
        .status {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            color: white;
        }

        .occupied { background: #0d47a1; }
        .available { background: #2e7d32; }
        .maintenance { background: #9e9e9e; }

    </style>

</head>

<body>

<div class="sidebar" id="sidebar">
    <h4>NUH</h4>
    <p>Admin Dashboard</p>

    <a href="{{ route('admin.dashboard') }}">
        <i class="fa fa-home"></i> Dashboard
    </a>

    <a href="{{ route('admin.appointments') }}">
        <i class="fa fa-calendar"></i> Appointments
    </a>

    <a href="{{ route('admin.patients') }}">
        <i class="fa fa-user"></i> Patients
    </a>

    <a href="{{ route('admin.doctors') }}">
        <i class="fa fa-user-md"></i> Doctors
    </a>

    <a href="{{ route('admin.rooms') }}" class="active">
        <i class="fa fa-bed"></i> Rooms
    </a>
    <a href="{{ route('admin.departments') }}">
        <i class="fa fa-user-md"></i> Departments
    </a>

    <a href="#">⚙️ Settings</a>

</div>

<div class="overlay" id="overlay"></div>

<div class="main" id="main">

    <div class="topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle">☰</button>
            <input type="text" class="search-box" placeholder="Search rooms...">
        </div>

        <div>
            <i class="fa fa-bell"></i>
            <strong>Admin</strong>
        </div>
    </div>

    <h4>Rooms Management</h4>
    <p class="text-white">Manage and track all hospital rooms</p>

    <a href="{{ route('admin.rooms.create') }}" class="add-btn">
        + Add New Room
    </a>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="card-table mt-3">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>Floor</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Current Patient</th>
                <th>Actions</th>
            </tr>
            </thead>

            <tbody>
            @foreach($rooms as $room)
                <tr>
                    <td>{{ $room->room_number }}</td>
                    <td>{{ $room->type }}</td>
                    <td>Floor {{ $room->floor }}</td>
                    <td>{{ $room->capacity }} beds</td>

                    <td>
                        @php $status = strtolower($room->status); @endphp

                        @if($status === 'occupied')
                            <span class="status occupied">Occupied</span>
                        @elseif($status === 'available')
                            <span class="status available">Available</span>
                        @else
                            <span class="status maintenance">Maintenance</span>
                        @endif
                    </td>

                    <td>
                        {{ $room->patient->full_name ?? $room->current_patient ?? '-' }}
                    </td>

                    <td>
                        <a href="{{ route('admin.rooms.edit', $room->id) }}">
                            <i class="fa fa-edit me-2"></i>
                        </a>

                        <form action="{{ route('admin.rooms.delete', $room->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="border:none;background:none;">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    ```

</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const main = document.getElementById('main');

    menuToggle.onclick = () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
        main.classList.toggle('shift');
    };

    overlay.onclick = () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        main.classList.remove('shift');
    };
</script>

</body>
</html>
