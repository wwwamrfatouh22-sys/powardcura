<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Department</title>

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
</head>

<body>

<div class="card-form">
    <h4 class="mb-4 text-center">✏️ Edit Department</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.departments.update', $department->id) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Department Name (EN)</label>
            <input type="text" name="name_en" value="{{ $department->name_en }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Department Name (AR)</label>
            <input type="text" name="name_ar" value="{{ $department->name_ar }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Head of Department</label>
            <input type="text" name="head_name" value="{{ $department->head_name }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" {{ $department->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $department->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button class="btn btn-primary w-100">
            💾 Update Department
        </button>
    </form>
</div>

</body>
</html>
