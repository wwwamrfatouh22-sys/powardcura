<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Patient</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #1e88e5, #90caf9);
            font-family: Arial;
        }

        .card-form {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
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
        }

        .title {
            margin-bottom: 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="card-form">

    <h4 class="title">Edit Patient</h4>

    <form action="{{ route('admin.patients.update', $patient->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ $patient->full_name }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Date of Birth</label>
            <input type="date" name="dob" value="{{ $patient->dob }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ $patient->phone }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control">
                <option {{ $patient->gender == 'Male' ? 'selected' : '' }}>Male</option>
                <option {{ $patient->gender == 'Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Medical Condition</label>
            <input type="text" name="medical_condition" value="{{ $patient->medical_condition }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Last Visit</label>
            <input type="datetime-local" name="last_visit"
                   value="{{ \Carbon\Carbon::parse($patient->last_visit)->format('Y-m-d\TH:i') }}"
                   class="form-control">
        </div>

        <button class="btn btn-primary w-100">Update Patient</button>
    </form>

</div>

</body>
</html>
