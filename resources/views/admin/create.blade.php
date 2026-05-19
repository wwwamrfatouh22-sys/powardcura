<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Add Patient</title>
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
        margin-bottom: 22px;
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

    .error-text {
        font-size: 13px;
        color: #dc3545;
        margin-top: 6px;
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
        <div class="title">Add Patient</div>

        <form action="{{ route('admin.patients.store') }}" method="POST">
            @csrf

            <div class="mb-custom">
                <label class="form-label">Patient Name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                    placeholder="Enter patient name">
                @error('name')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="two-cols">
                <div class="mb-custom">
                    <label class="form-label">Age</label>
                    <input type="date" name="dob" value="{{ old('dob') }}" class="form-control">
                    @error('dob')
                    <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-custom">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        <option value=""></option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                    <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-custom">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control"
                    placeholder="Enter phone number">
                @error('phone')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-custom">
                <label class="form-label">Medical Condition</label>
                <input type="text" name="medical_condition" value="{{ old('medical_condition') }}" class="form-control"
                    placeholder="Enter medical condition">
                @error('medical_condition')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-custom">
                <label class="form-label">Last Visit Date</label>
                <input type="datetime-local" name="last_visit" value="{{ old('last_visit') }}" class="form-control">
                @error('last_visit')
                <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>
    </div>

</body>

</html>
