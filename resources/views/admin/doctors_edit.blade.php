<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor</title>

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
    </style>
</head>

<body>

<div class="card-form">

    <h4 class="mb-4">Edit Doctor</h4>

    <form action="{{ route('admin.doctors.update', $doctor->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ $doctor->name }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Specialization</label>
            <input type="text" name="specialization" value="{{ $doctor->specialization }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Experience</label>
            <input type="number" name="experience" value="{{ $doctor->experience }}" class="form-control">
        </div>


        <div class="mb-3">
            <label>Email</label>
            <input type="text" name="email" value="{{ $doctor->email }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option {{ $doctor->status == 'Available' ? 'selected' : '' }}>Available</option>
                <option {{ $doctor->status == 'Busy' ? 'selected' : '' }}>Busy</option>
            </select>
        </div>

        <button class="btn btn-primary w-100">Update Doctor</button>
    </form>

</div>

</body>
</html>
