<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>

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

        .btn-primary {
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
    <h4 class="mb-4 text-center">✏️ Edit Appointment</h4>

    <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Patient ID</label>
            <input type="number" name="patient_id" class="form-control" value="{{ $appointment->patient_id }}" required>
        </div>

        <div class="mb-3">
            <label>Doctor ID</label>
            <input type="number" name="doctor_id" class="form-control" value="{{ $appointment->doctor_id }}" required>
        </div>

        <div class="mb-3">
            <label>Date</label>
            <input type="datetime-local" name="date" class="form-control"
                   value="{{ \Carbon\Carbon::parse($appointment->date)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Pending" {{ $appointment->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Confirmed" {{ $appointment->status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="Cancelled" {{ $appointment->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button class="btn btn-primary w-100">
            💾 Update Appointment
        </button>

    </form>
    ```

</div>

</body>
</html>
