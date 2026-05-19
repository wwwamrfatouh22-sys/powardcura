<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Add Department</title>
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

    .form-control {
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

    .form-control:focus {
        border-color: #c7c7c7;
        background: #fff;
        box-shadow: 0 0 0 0.12rem rgba(20, 74, 145, 0.08) !important;
    }

    .mb-custom {
        margin-bottom: 18px;
    }

    .actions {
        display: grid;
        gap: 16px;
        margin-top: 24px;
    }

    .btn-cancel,
    .btn-save {
        height: 50px;
        border-radius: 18px;
        font-size: 16px;
        font-weight: 500;
        border: none;
        transition: .2s ease;
    }

    .btn-cancel {
        background: #f8f8f8;
        border: 1px solid #d9d9d9;
        color: #2f3a45;
    }

    .btn-cancel:hover {
        background: #efefef;
    }

    .btn-save {
        background: #154a91;
        color: #fff;
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

        .actions {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>

    <div class="card-form">

        <div class="title">Add New Department</div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('admin.departments.store') }}" method="POST">
            @csrf

            <div class="mb-custom">
                <label class="form-label">Department Name</label>
                <select name="name_en" class="form-control" required>
                    <option value="">Select department</option>
                    @foreach($departmentOptions as $departmentOption)
                    <option value="{{ $departmentOption['name_en'] }}"
                        {{ old('name_en') === $departmentOption['name_en'] ? 'selected' : '' }}>
                        {{ $departmentOption['name_en'] }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-custom">
                <label class="form-label">Head of Department</label>
                <input type="text" name="head_name" class="form-control" placeholder="e.g., Dr. John Smith">
            </div>

            <div class="actions">
                <button type="submit" class="btn-save">Add Department</button>
            </div>
        </form>

    </div>

</body>

</html>
