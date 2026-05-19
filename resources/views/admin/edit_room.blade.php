<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Edit Room</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        min-height: 100vh;
        font-family: Arial, Helvetica, sans-serif;
        background: linear-gradient(90deg, #8ecbff 0%, #cfeaff 18%, #edf6ff 38%, #f8f9fb 62%, #f4f4f5 100%);
        background-attachment: fixed;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 16px;
    }

    .card-form {
        width: 100%;
        max-width: 520px;
        background: #f3f3f3;
        border-radius: 34px;
        padding: 32px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
    }

    .title {
        font-size: 22px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 6px;
    }

    .subtitle {
        font-size: 15px;
        color: #6b7280;
        margin-bottom: 26px;
    }

    .form-label {
        font-size: 15px;
        font-weight: 500;
        margin-bottom: 8px;
        color: #1f2937;
    }

    .form-control,
    .form-select {
        height: 58px;
        border-radius: 12px;
        border: 1.8px solid #2f80ed;
        background: #fff;
        font-size: 15px;
        padding: 14px;
        box-shadow: none !important;
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #1e5fcf;
        box-shadow: 0 0 0 0.1rem rgba(30, 95, 207, 0.15) !important;
    }

    .two-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .mb-custom {
        margin-bottom: 18px;
    }

    .btn-save {
        width: 100%;
        height: 52px;
        border: none;
        border-radius: 10px;
        background: #154a91;
        color: #fff;
        font-size: 16px;
        font-weight: 500;
        margin-top: 10px;
        transition: .2s;
    }

    .btn-save:hover {
        background: #123e78;
    }

    .alert {
        border-radius: 14px;
        margin-bottom: 18px;
    }

    @media (max-width:576px) {
        .card-form {
            padding: 24px 20px;
            border-radius: 26px;
        }

        .two-cols {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>

    <div class="card-form">

        <div class="title">Edit Room</div>
        <div class="subtitle">Update room details</div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST">
            @csrf

            <div class="two-cols">
                <div class="mb-custom">
                    <label class="form-label">Room Number</label>
                    <input type="text" name="room_number" value="{{ $room->room_number }}" class="form-control"
                        placeholder="e.g., R-101" required>
                </div>

                <div class="mb-custom">
                    <label class="form-label">Floor</label>
                    <input type="number" name="floor" value="{{ $room->floor }}" class="form-control" placeholder="0"
                        required>
                </div>
            </div>

            <div class="mb-custom">
                <label class="form-label">Room Type</label>
                <select name="type" class="form-select" required>
                    <option value="ICU" {{ $room->type == 'ICU' ? 'selected' : '' }}>ICU</option>
                    <option value="Private Room" {{ $room->type == 'Private Room' ? 'selected' : '' }}>Private Room
                    </option>
                    <option value="General Ward" {{ $room->type == 'General Ward' ? 'selected' : '' }}>General Ward
                    </option>
                    <option value="Operating Room" {{ $room->type == 'Operating Room' ? 'selected' : '' }}>Operating
                        Room</option>
                    <option value="Emergency" {{ $room->type == 'Emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
            </div>

            <div class="mb-custom">
                <label class="form-label">Bed Capacity</label>
                <input type="number" name="capacity" value="{{ $room->capacity }}" class="form-control" placeholder="0"
                    required>
            </div>

            <div class="mb-custom">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="available" {{ $room->status == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ $room->status == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="maintenance" {{ $room->status == 'maintenance' ? 'selected' : '' }}>Maintenance
                    </option>
                </select>
            </div>

            <div class="mb-custom">
                <label class="form-label">Select Patient</label>
                <select name="patient_id" class="form-select">
                    <option value="">Select Patient</option>
                    @foreach($patients as $id => $name)
                    <option value="{{ $id }}" {{ $room->patient_id == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-custom">
                <label class="form-label">Current Patient</label>
                <input type="text" name="current_patient" value="{{ $room->current_patient }}" class="form-control"
                    placeholder="Patient name (optional)">
            </div>

            <button type="submit" class="btn-save">Save Changes</button>

        </form>

    </div>

</body>

</html>