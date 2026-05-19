<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Edit Department</title>
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
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px 16px;
    }

    .card-form {
        width: 100%;
        max-width: 560px;
        background: #f4f4f4;
        border-radius: 34px;
        padding: 36px 32px 30px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
    }

    .title {
        font-size: 24px;
        font-weight: 500;
        color: #2f3a45;
        margin-bottom: 26px;
    }

    .form-label {
        display: block;
        font-size: 15px;
        font-weight: 500;
        color: #2f3a45;
        margin-bottom: 10px;
    }

    .form-control,
    .form-select {
        height: 52px;
        border-radius: 18px;
        border: 1px solid #dddddd;
        background: #f8f8f8;
        font-size: 15px;
        color: #2f3a45;
        padding: 0 16px;
        box-shadow: none !important;
    }

    .form-control::placeholder {
        color: #969696;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #c7c7c7;
        background: #fff;
        box-shadow: 0 0 0 0.12rem rgba(20, 74, 145, 0.08) !important;
    }

    .mb-custom {
        margin-bottom: 18px;
    }

    .btn-save {
        width: 100%;
        height: 52px;
        border: none;
        border-radius: 18px;
        background: #154a91;
        color: #fff;
        font-size: 16px;
        font-weight: 500;
        margin-top: 20px;
        transition: .2s ease;
    }

    .btn-save:hover {
        background: #123e78;
    }

    .alert {
        border-radius: 16px;
        margin-bottom: 18px;
    }

    @media (max-width:576px) {
        .card-form {
            padding: 26px 20px 24px;
            border-radius: 26px;
        }
    }
    </style>
</head>

<body>

    <div class="card-form">

        <div class="title">Edit Department</div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('admin.departments.update', $department->id) }}" method="POST">
            @csrf

            <div class="mb-custom">
                <label class="form-label">Department Name</label>
                <select name="name_en" class="form-select" required>
                    @foreach($departmentOptions as $departmentOption)
                    <option value="{{ $departmentOption['name_en'] }}"
                        {{ old('name_en', $department->name_en) === $departmentOption['name_en'] ? 'selected' : '' }}>
                        {{ $departmentOption['name_en'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-custom">
                <label class="form-label">Head of Department</label>
                <input type="text" name="head_name" value="{{ $department->head_name }}" class="form-control"
                    placeholder="e.g., Dr. Sarah Chen">
            </div>

            <div class="mb-custom">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ $department->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $department->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button type="submit" class="btn-save">
                Update Department
            </button>

        </form>

    </div>

</body>

</html>
