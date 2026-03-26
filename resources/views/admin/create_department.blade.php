<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Department</title>

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

</head>

<body>

<div class="card-form">

    <h4 class="mb-4 text-center">➕ Add New Department</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.departments.store') }}" method="POST">
        @csrf

        <!-- Name EN -->
        <div class="mb-3">
            <label>Department Name (EN)</label>
            <input type="text" name="name_en" class="form-control" required>
        </div>

        <!-- Name AR -->
        <div class="mb-3">
            <label>Department Name (AR)</label>
            <input type="text" name="name_ar" class="form-control" required>
        </div>

        <!-- Head Doctor -->
        <div class="mb-3">
            <label>Head of Department</label>
            <input type="text" name="head_name" class="form-control" placeholder="Enter doctor name">
        </div>

        <button class="btn btn-success w-100">
            💾 Add Department
        </button>

    </form>

</div>

</body>
</html>
