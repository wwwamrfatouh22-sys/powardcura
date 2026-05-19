<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Edit Staff</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        min-height: 100vh; font-family: Arial, Helvetica, sans-serif;
        background: linear-gradient(90deg, #8ecbff 0%, #cfeaff 18%, #edf6ff 38%, #f8f9fb 62%, #f4f4f5 100%);
        display: flex; align-items: center; justify-content: center; padding: 30px 16px;
    }
    .card-form {
        width: 100%; max-width: 580px; background: #f3f3f3; border-radius: 34px; padding: 32px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
    }
    .title { font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 6px; }
    .subtitle { font-size: 15px; color: #6b7280; margin-bottom: 24px; }
    .form-label { font-size: 15px; font-weight: 500; margin-bottom: 8px; color: #1f2937; }
    .form-control, .form-select {
        min-height: 58px; border-radius: 12px; border: 1.8px solid #2f80ed; background: #fff;
        font-size: 15px; padding: 14px; box-shadow: none !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1e5fcf; box-shadow: 0 0 0 0.1rem rgba(30, 95, 207, 0.15) !important;
    }
    .two-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .mb-custom { margin-bottom: 18px; }
    .btn-save {
        width: 100%; height: 52px; border: none; border-radius: 10px; background: #154a91; color: #fff;
        font-size: 16px; font-weight: 500; margin-top: 10px; transition: .2s;
    }
    .btn-save:hover { background: #123e78; }
    .alert { border-radius: 14px; margin-bottom: 18px; }
    .invalid-feedback { display: block; }
    @media (max-width:576px) {
        .card-form { padding: 24px 20px; border-radius: 26px; }
        .two-cols { grid-template-columns: 1fr; }
    }
    </style>
</head>

<body>
    <div class="card-form">
        <div class="title">Edit Staff</div>
        <div class="subtitle">Update staff details. Leave password blank to keep the current password.</div>

        @if ($errors->any())
        <div class="alert alert-danger">Please review the highlighted fields and try again.</div>
        @endif

        <form action="{{ route('admin.staff.update', $staffMember->id) }}" method="POST">
            @csrf

            <div class="mb-custom">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name', $staffMember->displayName()) }}" class="form-control @error('full_name') is-invalid @enderror" placeholder="Enter full name" required>
                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="two-cols">
                <div class="mb-custom">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $staffMember->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-custom">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $staffMember->phone) }}" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter phone">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="two-cols">
                <div class="mb-custom">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach($roles as $value => $label)
                        <option value="{{ $value }}" @selected(old('role', $staffMember->role) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-custom">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $staffMember->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="two-cols">
                <div class="mb-custom">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Optional new password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-custom">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
                </div>
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>
    </div>
</body>

</html>
