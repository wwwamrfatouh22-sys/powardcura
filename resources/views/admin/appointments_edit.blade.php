<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Edit Appointment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e88e5, #90caf9);
            font-family: Arial, sans-serif;
            min-height: 100vh;
            padding: 24px 12px;
        }

        .card-form {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            max-width: 760px;
            margin: 16px auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .form-control,
        .form-select,
        textarea.form-control {
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="card-form">
        <h4 class="mb-4 text-center">Edit Appointment</h4>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.appointments.update', $appointment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Patient (Optional)</label>
                    <select name="patient_id" class="form-select">
                        <option value="">No linked patient</option>
                        @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ (string) old('patient_id', $appointment->patient_id) === (string) $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }} ({{ $patient->national_id }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Doctor</label>
                    <select name="doctor_id" class="form-select" required>
                        @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ (string) old('doctor_id', $appointment->doctor_id) === (string) $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $appointment->first_name) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $appointment->last_name) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $appointment->email) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $appointment->phone) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="{{ old('date', $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('Y-m-d') : '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Time</label>
                    <input type="time" name="time" class="form-control" value="{{ old('time', $appointment->time) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @php $status = old('status', $appointment->status); @endphp
                        <option value="Pending" {{ $status === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Confirmed" {{ $status === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="Completed" {{ $status === 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Type</label>
                    @php $type = old('type', $appointment->type ?: 'hospital'); @endphp
                    <select name="type" class="form-select">
                        <option value="hospital" {{ $type === 'hospital' ? 'selected' : '' }}>Hospital Visit</option>
                        <option value="private" {{ $type === 'private' ? 'selected' : '' }}>Private Clinic</option>
                    </select>
                </div>

                @if(($appointment->type ?: 'hospital') === 'private')
                    <div class="col-md-6">
                        <label class="form-label">Clinic Name</label>
                        <input type="text" class="form-control" value="{{ $appointment->clinic_name ?: 'Private Clinic' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Clinic Phone</label>
                        <input type="text" class="form-control" value="{{ $appointment->clinic_phone ?: 'N/A' }}" readonly>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Clinic Address</label>
                        <input type="text" class="form-control" value="{{ $appointment->clinic_address ?: 'N/A' }}" readonly>
                    </div>
                @endif

                <div class="col-12">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-control" rows="3">{{ old('reason', $appointment->reason) }}</textarea>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <a href="{{ route('admin.appointments') }}" class="btn btn-outline-secondary">Cancel</a>
                <button class="btn btn-primary w-100">Update Appointment</button>
            </div>
        </form>
    </div>
</body>

</html>
