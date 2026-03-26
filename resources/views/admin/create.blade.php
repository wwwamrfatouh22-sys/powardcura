<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Patient</title>

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
            max-width: 700px;
            margin: 80px auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .form-control {
            border-radius: 10px;
            height: 45px;
        }

        .btn-success {
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

<div class="card-form">

    <h4 class="mb-4 text-center">➕ Add New Patient</h4>

    <form action="{{ route('admin.patients.store') }}" method="POST">
        @csrf

        <!-- Name -->
        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <!-- National ID -->
        <div class="mb-3">
            <label>National ID</label>
            <input type="text" name="national_id" class="form-control" required>
        </div>

        <!-- DOB -->
        <div class="mb-3">
            <label>Date of Birth</label>
            <input type="date" name="dob" class="form-control" required>
        </div>

        <!-- Phone -->
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>

        <!-- Gender -->
        <div class="mb-3">
            <label>Gender</label>
            <select name="gender" class="form-control">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <!-- File Number -->
        <div class="mb-3">
            <label>File Number</label>
            <input type="text" name="file_number" class="form-control">
        </div>

        <!-- Medical Condition -->
        <div class="mb-3">
            <label>Medical Condition</label>
            <input type="text" name="medical_condition" class="form-control">
        </div>

        <!-- Last Visit -->
        <div class="mb-3">
            <label>Last Visit</label>
            <input type="datetime-local" name="last_visit" class="form-control">
        </div>

        <button class="btn btn-success w-100">Add Patient</button>
    </form>

</div>

</body>
</html>
