<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Doctor</title>

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
    <h4 class="mb-4 text-center">➕ Add New Doctor</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.doctors.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Specialization</label>
            <input type="text" name="specialization" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Experience (Years)</label>
            <input type="number" name="experience" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <!-- دي أهم واحدة -->
        <div class="mb-3">
            <label>Department</label>
            <select name="department_id" class="form-control" required>
                <option value="">Select Department</option>
                <option value="1">Cardiology</option>
                <option value="2">Neurology</option>
                <option value="3">Orthopedics</option>
                <option value="4">Pediatrics</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="Available">Available</option>
                <option value="Busy">Busy</option>
            </select>
        </div>

        <button class="btn btn-success w-100">
            💾 Add Doctor
        </button>

    </form>
    ```

</div>

</body>
</html>
