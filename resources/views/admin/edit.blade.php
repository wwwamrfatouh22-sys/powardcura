<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Edit Patient</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
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
        margin-bottom: 6px;
    }

    .subtitle {
        font-size: 15px;
        color: #6b7280;
        margin-bottom: 28px;
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
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #1e5fcf;
        box-shadow: 0 0 0 0.1rem rgba(30, 95, 207, 0.15);
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

    @media (max-width: 576px) {
        .two-cols {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>

<body>

    <div class="card-form">

        <div class="title">Edit Patient</div>
        <div class="subtitle">Update patient details</div>

        <form action="{{ route('admin.patients.update', $patient->id) }}" method="POST">
            @csrf

            <!-- Name -->
            <div class="mb-custom">
                <label class="form-label">Patient Name</label>
                <input type="text" name="name" value="{{ $patient->full_name }}" class="form-control"
                    placeholder="Enter patient name">
            </div>

            <!-- Age + Gender -->
            <div class="two-cols">
                <div class="mb-custom">
                    <label class="form-label">Age</label>
                    <input type="date" name="dob" value="{{ $patient->dob }}" class="form-control">
                </div>

                <div class="mb-custom">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-select">
                        @php($patientGender = strtolower((string) $patient->gender))
                        <option value="male" {{ $patientGender === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ $patientGender === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <!-- Phone -->
            <div class="mb-custom">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" value="{{ $patient->phone }}" class="form-control"
                    placeholder="Enter phone number">
            </div>

            <!-- Medical Condition -->
            <div class="mb-custom">
                <label class="form-label">Medical Condition</label>
                <input type="text" name="medical_condition" value="{{ $patient->medical_condition }}"
                    class="form-control" placeholder="Enter medical condition">
            </div>

            <!-- Last Visit -->
            <div class="mb-custom">
                <label class="form-label">Last Visit Date</label>
                <input type="datetime-local" name="last_visit"
                    value="{{ \Carbon\Carbon::parse($patient->last_visit)->format('Y-m-d\TH:i') }}"
                    class="form-control">
            </div>

            <button class="btn-save">Save Changes</button>
        </form>

    </div>

</body>

</html>
