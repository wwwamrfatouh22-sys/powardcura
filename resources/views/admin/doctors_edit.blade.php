<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Edit Doctor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        min-height: 100vh; font-family: Arial, Helvetica, sans-serif;
        background: linear-gradient(90deg, #8ecbff 0%, #cfeaff 18%, #edf6ff 38%, #f8f9fb 62%, #f4f4f5 100%);
        background-attachment: fixed; display: flex; align-items: center; justify-content: center; padding: 30px 16px;
    }
    .card-form {
        width: 100%; max-width: 580px; background: #f3f3f3; border-radius: 34px; padding: 32px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.12);
    }
    .title { font-size: 22px; font-weight: 600; color: #1f2937; margin-bottom: 6px; }
    .subtitle { font-size: 15px; color: #6b7280; margin-bottom: 26px; }
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
        <div class="title">Edit Doctor</div>
        <div class="subtitle">Update doctor details and keep the admin list in sync.</div>

        @if ($errors->any())
        <div class="alert alert-danger">
            Please review the highlighted fields and try again.
        </div>
        @endif

        <form action="{{ route('admin.doctors.update', $doctor->id) }}" method="POST">
            @csrf
            @php($clinicDays = old('clinic_available_days', $doctor->privateClinic?->available_days ?? []))

            <div class="two-cols">
                <div class="mb-custom">
                    <label class="form-label">Doctor Name</label>
                    <input type="text" name="name" value="{{ old('name', $doctor->name) }}"
                        class="form-control @error('name') is-invalid @enderror" placeholder="Enter doctor name" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-custom">
                    <label class="form-label">Experience</label>
                    <input type="number" name="experience" value="{{ old('experience', $doctor->experience) }}"
                        class="form-control @error('experience') is-invalid @enderror" placeholder="0" min="0" required>
                    @error('experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-custom">
                <label class="form-label">Specialization</label>
                <input type="text" name="specialization" value="{{ old('specialization', $doctor->specialization) }}"
                    class="form-control @error('specialization') is-invalid @enderror"
                    placeholder="e.g., Cardiology Consultant" required>
                @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-custom">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $doctor->email) }}"
                    class="form-control @error('email') is-invalid @enderror" placeholder="Enter email address">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-custom">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}"
                        {{ (string) old('department_id', $doctor->department_id) === (string) $department->id ? 'selected' : '' }}>
                        {{ $department->name_en }}
                    </option>
                    @endforeach
                </select>
                @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-custom">
                <label class="form-label">Status</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="Available" {{ old('status', $doctor->status) === 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Busy" {{ old('status', $doctor->status) === 'Busy' ? 'selected' : '' }}>Busy</option>
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <hr class="my-4">
            <div class="title" style="font-size:18px;">Private Clinic Details</div>
            <div class="subtitle" style="margin-bottom:18px;">Private Clinic is enabled by default. Turn it off only for exception doctors.</div>

            <div class="mb-custom">
                <label class="d-inline-flex align-items-center gap-2">
                    <input type="hidden" name="has_private_clinic" value="0">
                    <input type="checkbox" name="has_private_clinic" value="1" {{ old('has_private_clinic', $doctor->has_private_clinic ? '1' : '0') === '1' ? 'checked' : '' }}>
                    <span>Doctor offers Private Clinic booking</span>
                </label>
            </div>

            <div class="mb-custom">
                <label class="form-label">Clinic Name</label>
                <input type="text" name="clinic_name" value="{{ old('clinic_name', $doctor->privateClinic?->clinic_name) }}"
                    class="form-control @error('clinic_name') is-invalid @enderror" placeholder="Enter clinic name">
                @error('clinic_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-custom">
                <label class="form-label">Clinic Address</label>
                <input type="text" name="clinic_address" value="{{ old('clinic_address', $doctor->privateClinic?->clinic_address) }}"
                    class="form-control @error('clinic_address') is-invalid @enderror" placeholder="Enter clinic address">
                @error('clinic_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="two-cols">
                <div class="mb-custom">
                    <label class="form-label">Clinic Phone</label>
                    <input type="text" name="clinic_phone" value="{{ old('clinic_phone', $doctor->privateClinic?->clinic_phone) }}"
                        class="form-control @error('clinic_phone') is-invalid @enderror" placeholder="Enter clinic phone">
                    @error('clinic_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-custom">
                    <label class="form-label">Clinic Fee</label>
                    <input type="number" step="0.01" min="0" name="clinic_fee" value="{{ old('clinic_fee', $doctor->privateClinic?->clinic_fee) }}"
                        class="form-control @error('clinic_fee') is-invalid @enderror" placeholder="450.00">
                    @error('clinic_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-custom">
                <label class="form-label">Available Days</label>
                <div class="d-flex flex-wrap gap-3">
                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                        <label class="d-inline-flex align-items-center gap-2">
                            <input type="checkbox" name="clinic_available_days[]" value="{{ $day }}" {{ in_array($day, $clinicDays, true) ? 'checked' : '' }}>
                            <span>{{ ucfirst($day) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('clinic_available_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                @error('clinic_available_days.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-custom">
                <label class="form-label">Available Times</label>
                <input type="text" name="clinic_available_times" value="{{ old('clinic_available_times', is_array($doctor->privateClinic?->available_times) ? implode(', ', $doctor->privateClinic->available_times) : '') }}"
                    class="form-control @error('clinic_available_times') is-invalid @enderror" placeholder="17:00, 18:00, 19:00">
                <small class="text-muted">Use comma-separated times in 24-hour format.</small>
                @error('clinic_available_times')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-custom">
                <label class="form-label">Notes</label>
                <textarea name="clinic_notes" class="form-control @error('clinic_notes') is-invalid @enderror" placeholder="Optional notes for patients">{{ old('clinic_notes', $doctor->privateClinic?->notes) }}</textarea>
                @error('clinic_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
        </form>
    </div>
</body>

</html>
