<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('ui.booking.details_title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ app()->isLocale('ar') ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#114a9e; --primary-dark:#0d3f87; --muted:#6f7785; }
        body {
            margin:0; min-height:100vh; font-family:{{ app()->isLocale('ar') ? "'Cairo','Segoe UI',Arial,sans-serif" : "'Inter','Segoe UI',Arial,sans-serif" }};
            background:radial-gradient(circle at 14% 88%, rgba(24,137,255,.85) 0%, rgba(24,137,255,0) 44%),
                       linear-gradient(90deg,#9fcaf0 0%,#eef5fb 42%,#f3f3f3 100%);
            padding:26px 14px 36px; color:#2f3742; text-align:{{ app()->isLocale('ar') ? 'right' : 'left' }};
        }
        .shell { max-width:980px; margin:auto; background:rgba(248,248,248,.96); border-radius:28px; box-shadow:0 20px 40px rgba(0,0,0,.14); padding:26px; }
        .topline { display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .topline h1 { margin:0; font-size:clamp(24px,3vw,30px); font-weight:800; }
        .topline p { margin:4px 0 0; color:var(--muted); }
        .doctor-summary { display:flex; justify-content:space-between; align-items:center; gap:16px; padding:14px; border-radius:18px; background:#eef4ff; margin-bottom:24px; }
        .doctor-left { display:flex; align-items:center; gap:12px; }
        .doctor-avatar { width:62px; height:62px; border-radius:50%; object-fit:cover; background:#d8e7fb; box-shadow:0 8px 16px rgba(17,74,158,.2); }
        .muted { color:var(--muted); }
        .card-soft { background:#fff; border-radius:18px; padding:16px; box-shadow:0 12px 26px rgba(17,74,158,.08); }
        .form-label { font-weight:700; color:#384658; }
        .form-control, .form-select { border-radius:12px; min-height:50px; border:1.5px solid #c6d7ef; }
        textarea.form-control { min-height:110px; }
        .continue-btn { border:none; border-radius:12px; min-height:52px; background:var(--primary); color:#fff; font-weight:700; width:100%; }
        .continue-btn:hover { background:var(--primary-dark); }
        .continue-btn:disabled { background:#cfd6e2; color:#6f7f96; cursor:not-allowed; }
        .slot-alert { border-radius:16px; border:none; background:#ffecec; color:#9f1f1f; font-weight:700; padding:12px 14px; margin-bottom:16px; }
        .slot-ok { border-radius:16px; border:none; background:#eef6ff; color:#23476e; font-weight:700; padding:12px 14px; margin-bottom:16px; }
        @media (max-width:768px) { .doctor-summary { flex-direction:column; align-items:flex-start; } }
    </style>
</head>
<body>
<div class="shell">
    <div class="topline">
        <div>
            <h1>{{ __('ui.booking.details_title') }}</h1>
            <p>{{ __('ui.booking.details_step') }}</p>
        </div>
        <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'date' => $selectedDate, 'type' => $selectedType]) }}" class="btn btn-outline-secondary">{{ __('ui.common.back') }}</a>
    </div>

    <div class="doctor-summary">
        <div class="doctor-left">
            <img src="{{ $doctor->image ? asset('images/' . $doctor->image) : asset('images/logo_Image.png') }}" class="doctor-avatar" alt="{{ $doctor->name }}" onerror="this.onerror=null;this.src='{{ asset('images/logo_Image.png') }}';">
            <div>
                <strong>{{ $doctor->name }}</strong>
                <div class="muted">{{ $doctor->specialization }}</div>
                <div class="muted">{{ optional($doctor->department)->name_en ?? 'Department' }}</div>
            </div>
        </div>
        <div class="text-end">
            <div><i class="bi bi-calendar-event"></i> {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, M j, Y') }}</div>
            <div><i class="bi bi-clock"></i> {{ $time }}</div>
            <div><i class="bi bi-bookmark-check"></i> {{ __('ui.booking.choose_location') }}</div>
            <div><i class="bi bi-cash-stack"></i> {{ __('ui.booking.hospital_selected', ['amount' => number_format(\App\Support\PrivateClinicBookingSupport::calculateAmount($doctor, 'hospital'), 2)]) }}</div>
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

    @if(!$slotStillAvailable)
        <div class="slot-alert"><i class="bi bi-exclamation-circle"></i> Slot no longer available</div>
    @else
        <div class="slot-ok"><i class="bi bi-check-circle"></i> Selected slot is available. Confirm your details to continue.</div>
    @endif

    <form action="{{ route('appointments.review') }}" method="POST">
        @csrf
        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
        <input type="hidden" name="time" value="{{ $time }}">
        <input type="hidden" name="date" value="{{ $selectedDate }}">

        <div class="card-soft mb-3">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="form-label" for="type">{{ __('ui.booking.booking_location') }} *</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="hospital" {{ old('type', $selectedType) === 'hospital' ? 'selected' : '' }}>{{ __('ui.common.hospital') }}</option>
                        <option value="private" {{ old('type', $selectedType) === 'private' ? 'selected' : '' }} {{ $hasPrivateClinic ? '' : 'disabled' }}>{{ __('ui.common.private_clinic') }}{{ $hasPrivateClinic ? '' : ' (' . __('ui.common.not_available') . ')' }}</option>
                    </select>
                    <div class="muted mt-2" id="bookingTypeNote">
                        {{ $hasPrivateClinic ? __('ui.booking.choose_location_note') : __('ui.booking.hospital_only_note') }}
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('ui.booking.first_name') }} *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('ui.booking.last_name') }} *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('ui.common.email') }}</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('ui.booking.phone') }} *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">{{ __('ui.booking.reason') }}</label>
                    <textarea name="reason" class="form-control" placeholder="{{ app()->isLocale('ar') ? 'اكتب الأعراض أو سبب الزيارة' : 'Describe symptoms or reason' }}">{{ old('reason') }}</textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="continue-btn" {{ $slotStillAvailable ? '' : 'disabled' }}>{{ __('ui.booking.continue_payment') }}</button>
    </form>
</div>
<script>
    const bookingTypeSelect = document.getElementById('type');
    const bookingTypeNote = document.getElementById('bookingTypeNote');
    const hospitalFee = @json(number_format(\App\Support\PrivateClinicBookingSupport::calculateAmount($doctor, 'hospital'), 2));
    const privateFee = @json(number_format(\App\Support\PrivateClinicBookingSupport::calculateAmount($doctor, 'private'), 2));
    const hasPrivateClinic = @json($hasPrivateClinic);

    function renderBookingTypeNote() {
        if (bookingTypeSelect.value === 'private' && hasPrivateClinic) {
            bookingTypeNote.textContent = @json(__('ui.booking.private_selected', ['amount' => '__AMOUNT__'])).replace('__AMOUNT__', privateFee);
            return;
        }

        bookingTypeNote.textContent = hasPrivateClinic
            ? @json(__('ui.booking.hospital_selected', ['amount' => '__AMOUNT__'])).replace('__AMOUNT__', hospitalFee)
            : @json(__('ui.booking.hospital_only_note'));
    }

    bookingTypeSelect.addEventListener('change', renderBookingTypeNote);
    renderBookingTypeNote();
</script>
</body>
</html>
