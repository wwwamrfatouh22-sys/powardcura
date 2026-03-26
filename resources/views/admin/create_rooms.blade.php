<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Room</title>

    ```
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1e88e5, #90caf9);
            font-family: Arial;
        }

        .card-form {
            background: #fff;
            border-radius: 20px;
            padding: 35px;
            max-width: 600px;
            margin: 80px auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .form-control {
            border-radius: 10px;
            height: 45px;
        }

        .btn-success {
            border-radius: 10px;
            padding: 10px;
            font-weight: bold;
        }
    </style>
    ```

</head>

<body>

<div class="card-form">

    ```
    <h4 class="mb-4 text-center">➕ Add New Room</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.rooms.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Room Number</label>
            <input type="text" name="room_number" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Patient Name</label>
            <input type="text" name="current_patient" class="form-control" placeholder="Enter patient name">
        </div>

        <div class="mb-3">
            <label>Room Type</label>
            <select name="type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="ICU">ICU</option>
                <option value="Private Room">Private Room</option>
                <option value="General Ward">General Ward</option>
                <option value="Operating Room">Operating Room</option>
                <option value="Emergency">Emergency</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Floor</label>
            <input type="number" name="floor" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Capacity (Beds)</label>
            <input type="number" name="capacity" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        <button class="btn btn-success w-100">
            💾 Add Room
        </button>

    </form>
    ```

</div>

</body>
</html>
