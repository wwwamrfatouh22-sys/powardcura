<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(90deg, #94c1e9 0%, #ffffff 50%, #94c1e9 100%);
            font-family: sans-serif;
        }

        .card-container {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-top: 50px;
        }

        .doctor-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }

        .form-control {
            border-radius: 10px;
            height: 50px;
        }

        textarea.form-control {
            height: 120px;
        }

        .btn-confirm {
            background: #0d4c92;
            color: #fff;
            border-radius: 10px;
            height: 50px;
            font-size: 18px;
        }

        .btn-confirm:hover {
            background: #083a70;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card-container">

        <h2 class="text-center mb-4">Book Appointment</h2>

        {{-- Appointment Details --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/' . $doctor->image) }}" class="doctor-img">
                <div>
                    <h5 class="mb-0">{{ $doctor->name }}</h5>
                    <small>{{ $doctor->specialization }}</small>
                </div>
            </div>

            <div class="text-end">
                <div>📅 {{ now()->format('F j, Y') }}</div>
                <div>⏰ {{ $time }}</div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Form --}}
        <form action="{{ route('appointments.store') }}" method="POST">
            @csrf

            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
            <input type="hidden" name="time" value="{{ $time }}">

            <h4 class="text-center mb-4">Patient Information</h4>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>First Name </label>
                    <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}">
                </div>

                <div class="col-md-6">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>

                <div class="col-md-6">
                    <label>Phone Number </label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" >
                </div>
            </div>

            <div class="mb-4">
                <label>Reason for Visit</label>
                <textarea name="reason" class="form-control" >{{ old('reason') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Appointment Type *</label>
                <select name="type" class="form-control" required>
                    <option value="hospital" {{ old('type')=='hospital'?'selected':'' }}>Hospital</option>
                    <option value="private" {{ old('type')=='private'?'selected':'' }}>Private Clinic</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="form-label fw-bold">Payment Method *</label>

                <div class="d-flex gap-4 mt-2">

                    <div>
                        <input type="radio" name="payment_method" value="instapay"
                            {{ old('payment_method') == 'instapay' ? 'checked' : '' }}>
{{--                        <label>Instapay</label>--}}
{{--                        <p>01023171831</p>--}}
                        <a href="https://ipn.eg/S/ahmedmahfouz255/instapay/9gIpzQ" target="_blank">Instapay</a>
                    </div>

                    <div>
                        <input type="radio" name="payment_method" value="bank_account"
                            {{ old('payment_method') == 'bank_account' ? 'checked' : '' }}>
                        <label>Fawry</label>
                    </div>

                    <div>
                        <input type="radio" name="payment_method" value="vodafone_cash"
                            {{ old('payment_method') == 'vodafone_cash' ? 'checked' : '' }}>
                        <label>Vodafone Cash</label>
                        <p>01023171831</p>
                    </div>

                    <div>
                        <input type="radio" name="payment_method" value="hospital"
                            {{ old('payment_method') == 'hospital' ? 'checked' : '' }}>
                        <label>Payment at Hospital</label>
                    </div>

                </div>

                @error('payment_method')
                <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-confirm w-100">
                Confirm
            </button>

        </form>

    </div>
</div>

</body>
</html>
