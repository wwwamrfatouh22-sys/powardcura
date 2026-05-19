<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('ui.booking.rate_doctor_title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ app()->isLocale('ar') ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary:#114a9e; --primary-dark:#0d3f87; --muted:#6f7785; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: {{ app()->isLocale('ar') ? "'Cairo','Segoe UI',Arial,sans-serif" : "'Inter','Segoe UI',Arial,sans-serif" }};
            background: radial-gradient(circle at 14% 88%, rgba(24,137,255,.85) 0%, rgba(24,137,255,0) 44%),
                        linear-gradient(90deg,#9fcaf0 0%,#eef5fb 42%,#f3f3f3 100%);
            padding: 26px 14px 36px;
            color: #2f3742;
            text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }};
        }
        .shell {
            max-width: 820px;
            margin: auto;
            background: rgba(248,248,248,.96);
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0,0,0,.14);
            padding: 26px;
        }
        .heading h1 { margin: 0; font-size: clamp(24px,3vw,30px); font-weight: 800; }
        .heading p { margin: 6px 0 0; color: var(--muted); }
        .summary { margin-top: 16px; background: #eef4ff; border-radius: 16px; padding: 14px; }
        .summary strong { display: block; }
        .star-row { display: flex; gap: 10px; justify-content: center; margin: 16px 0 10px; }
        .star-btn {
            width: 52px;
            height: 52px;
            border: none;
            border-radius: 50%;
            background: #f3f6fb;
            color: #c0ccde;
            font-size: 28px;
            cursor: pointer;
            transition: transform .15s ease, color .15s ease, background .15s ease;
        }
        .star-btn:hover { transform: translateY(-2px) scale(1.04); }
        .star-btn.active { color: #ffb300; background: #fff3cf; box-shadow: 0 8px 18px rgba(255,179,0,.25); }
        .rating-label { text-align: center; color: #4d5e74; font-weight: 600; min-height: 24px; }
        .form-control { border-radius: 12px; border: 1.5px solid #c6d7ef; }
        textarea.form-control { min-height: 120px; }
        .submit-btn { border: none; border-radius: 12px; min-height: 52px; background: var(--primary); color: #fff; font-weight: 700; width: 100%; }
        .submit-btn:hover { background: var(--primary-dark); }
        @media (max-width: 576px) {
            .star-row { gap: 8px; }
            .star-btn { width: 46px; height: 46px; font-size: 24px; }
        }
    </style>
</head>
<body>
@php
    $ratingLabels = app()->isLocale('ar')
        ? [1 => 'سيئ جدًا', 2 => 'سيئ', 3 => 'متوسط', 4 => 'جيد', 5 => 'ممتاز']
        : [1 => 'Very Poor', 2 => 'Poor', 3 => 'Average', 4 => 'Good', 5 => 'Excellent'];
    $selectionTemplate = app()->isLocale('ar')
        ? 'تم الاختيار: __COUNT__ نجمة (__LABEL__)'
        : 'Selected: __COUNT__ star__PLURAL__ (__LABEL__)';
    $timeConnector = app()->isLocale('ar') ? 'الساعة' : 'at';
@endphp
<div class="shell">
    <div class="heading">
        <h1>{{ __('ui.booking.rate_doctor_title') }}</h1>
        <p>{{ __('ui.booking.rate_doctor_step') }}</p>
    </div>

    <div class="summary">
        <strong>{{ $appointment->doctor->name }}</strong>
        <span class="text-muted">{{ $appointment->doctor->specialization }} | {{ optional($appointment->doctor->department)->name_en ?? 'Department' }}</span><br>
        <span class="text-muted">{{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('l, M j, Y') }} {{ $timeConnector }} {{ $appointment->time }}</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('doctor.ratings.store', $appointment) }}" method="POST" class="mt-3">
        @csrf
        <input type="hidden" name="rating" id="rating_value" value="{{ old('rating', '') }}">

        <div class="star-row" id="doctorStarRow">
            @for($i = 1; $i <= 5; $i++)
                <button type="button" class="star-btn star" aria-label="{{ app()->isLocale('ar') ? "قيّم بـ {$i} نجوم" : "Rate {$i} stars" }}">
                    <i class="bi bi-star-fill"></i>
                </button>
            @endfor
        </div>
        <div class="rating-label" id="ratingLabel">{{ __('ui.booking.select_rating') }}</div>

        <div class="mt-3">
            <label class="form-label fw-bold">{{ __('ui.booking.doctor_comment') }}</label>
            <textarea name="comment" class="form-control" placeholder="{{ __('ui.booking.feedback_placeholder') }}">{{ old('comment') }}</textarea>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="submit" class="submit-btn">{{ __('ui.booking.submit_doctor_rating') }}</button>
            <a href="{{ route('appointments.invoice', $appointment) }}" class="btn btn-outline-secondary w-100">{{ __('ui.booking.skip_now') }}</a>
        </div>
    </form>
</div>

<script>
    const labels = @json($ratingLabels);
    const selectionTemplate = @json($selectionTemplate);
    const input = document.getElementById('rating_value');
    const label = document.getElementById('ratingLabel');

    function updateLabel(value) {
        const n = Math.max(1, Math.min(5, Number(value)));
        label.textContent = selectionTemplate
            .replace('__COUNT__', n)
            .replace('__PLURAL__', n > 1 ? 's' : '')
            .replace('__LABEL__', labels[n]);
    }

    document.querySelectorAll('#doctorStarRow .star').forEach((star, index) => {
        star.addEventListener('click', () => {
            document.getElementById('rating_value').value = String(index + 1);
            document.querySelectorAll('#doctorStarRow .star').forEach((s, i) => {
                s.classList.toggle('active', i <= index);
            });
            updateLabel(index + 1);
        });
    });

    if (input.value) {
        const v = Math.max(1, Math.min(5, Number(input.value)));
        document.querySelectorAll('#doctorStarRow .star').forEach((s, i) => s.classList.toggle('active', i < v));
        updateLabel(v);
    }
</script>
</body>
</html>
