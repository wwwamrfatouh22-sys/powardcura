<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Room</title>

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
    <h4 class="mb-4 text-center">✏️ Edit Room</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Room Number</label>
            <input type="text" name="room_number" value="{{ $room->room_number }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Room Type</label>
            <select name="type" class="form-control" required>
                <option value="ICU" {{ $room->type == 'ICU' ? 'selected' : '' }}>ICU</option>
                <option value="Private Room" {{ $room->type == 'Private Room' ? 'selected' : '' }}>Private Room</option>
                <option value="General Ward" {{ $room->type == 'General Ward' ? 'selected' : '' }}>General Ward</option>
                <option value="Operating Room" {{ $room->type == 'Operating Room' ? 'selected' : '' }}>Operating Room</option>
                <option value="Emergency" {{ $room->type == 'Emergency' ? 'selected' : '' }}>Emergency</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Floor</label>
            <input type="number" name="floor" value="{{ $room->floor }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Capacity (Beds)</label>
            <input type="number" name="capacity" value="{{ $room->capacity }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Available</option>
                <option value="occupied" {{ $room->status == 'occupied' ? 'selected' : '' }}>Occupied</option>
                <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
        </div>

        <!-- Patient Dropdown -->
        <div class="mb-3">
            <label>Select Patient</label>
            <select name="patient_id" class="form-control">
                <option value="">Select Patient</option>
                @foreach($patients as $id => $name)
                    <option value="{{ $id }}" {{ $room->patient_id == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Manual Patient -->
        <div class="mb-3">
            <label>Or Enter Patient Name</label>
            <input type="text" name="current_patient" value="{{ $room->current_patient }}" class="form-control">
        </div>

        <button class="btn btn-success w-100">
            💾 Update Room
        </button>

    </form>
    ```

</div>

</body>
</html>
