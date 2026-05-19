<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Appointment Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --ink: #28323d;
            --muted: #516071;
            --line: #c8c8c8;
            --primary: #114a9e;
            --success: #16a34a;
            --star: #f5b301;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f1f5f9;
            color: var(--ink);
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            padding: 0;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 24px;
            padding: 28px;
            flex-wrap: wrap;
        }

        .invoice-card {
            width: min(448px, 100%);
            background: #fff;
            border-radius: 18px;
            padding: 48px 32px 24px;
        }

        .feedback-card {
            width: min(360px, 100%);
            background: #fff;
            border-radius: 18px;
            padding: 28px 24px;
            margin-top: 0;
            box-shadow: 0 14px 28px rgba(15, 23, 42, .08);
            text-align: center;
        }

        .doctor-rating-card {
            width: min(360px, 100%);
            background: #fff;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 14px 28px rgba(15, 23, 42, .08);
            text-align: center;
        }

        .doctor-rating-card h2 {
            margin: 0 0 10px;
            font-size: 18px;
            font-weight: 800;
        }

        .doctor-rating-card p {
            margin: 0 0 16px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .doctor-rating-link {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 46px;
            width: 100%;
            border-radius: 7px;
            background: var(--primary);
            color: #fff;
            font-weight: 800;
            text-decoration: none;
        }

        .status-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 0 auto 12px;
            background: #dcfce7;
            color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
        }

        .title {
            margin: 0;
            text-align: center;
            font-size: 25px;
            line-height: 1.2;
            font-weight: 800;
        }

        .subtitle {
            margin: 10px 0 18px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }

        .divider {
            height: 1px;
            background: var(--line);
            margin: 18px 0;
        }

        .section-title {
            margin: 0 0 18px;
            font-size: 17px;
            font-weight: 800;
            text-transform: none;
        }

        .doctor-row {
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 18px;
            align-items: center;
            margin-bottom: 18px;
        }

        .doctor-row img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin-inline-start: 10px;
            background: #eef2f7;
        }

        .doctor-name {
            margin: 0 0 8px;
            font-size: 13px;
            font-weight: 800;
        }

        .doctor-meta {
            margin: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.7;
        }

        .date-time {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 14px;
            align-items: center;
            font-size: 15px;
        }

        .inline-icon {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .patient-lines {
            display: grid;
            gap: 18px;
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .patient-lines strong {
            color: var(--ink);
            font-weight: 500;
        }

        .payment-title {
            margin: 0 0 18px;
            font-size: 17px;
            font-weight: 800;
        }

        .payment-line {
            display: flex;
            align-items: center;
            gap: 18px;
            font-size: 17px;
            margin-bottom: 16px;
        }

        .payment-line i {
            font-size: 23px;
        }

        .payment-note {
            border-radius: 6px;
            background: #fff;
            box-shadow: 0 16px 24px rgba(0, 0, 0, .18);
            padding: 22px 58px;
            color: #344050;
            font-size: 13px;
            line-height: 1.9;
            margin-bottom: 14px;
        }

        .queue {
            text-align: center;
            padding-top: 2px;
        }

        .queue h2 {
            margin: 0 0 18px;
            font-size: 16px;
            font-weight: 800;
            text-transform: lowercase;
        }

        .queue-number {
            min-width: 54px;
            height: 54px;
            padding: 0 14px;
            margin: 0 auto 18px;
            border-radius: 12px;
            box-shadow: 0 9px 14px rgba(0, 0, 0, .2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            font-weight: 800;
            background: #fff;
        }

        .queue p {
            margin: 0 0 18px;
            font-size: 17px;
        }

        .print-btn,
        .rating-submit {
            width: 100%;
            min-height: 52px;
            border: 0;
            border-radius: 7px;
            background: var(--primary);
            color: #fff;
            font-size: 17px;
            font-weight: 800;
            cursor: pointer;
        }

        .rating-card {
            margin-top: 22px;
            padding-top: 22px;
            border-top: 1px solid var(--line);
            text-align: center;
        }

        .rating-card h2 {
            margin: 0 0 14px;
            font-size: 18px;
            font-weight: 800;
        }

        .site-stars {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-bottom: 16px;
        }

        .site-star {
            border: 0;
            background: transparent;
            color: #c7ced8;
            font-size: 34px;
            line-height: 1;
            padding: 0 2px;
            cursor: pointer;
        }

        .site-star.selected {
            color: var(--star);
        }

        .feedback-help {
            margin: 0 0 16px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .feedback {
            width: 100%;
            min-height: 96px;
            border: 1px solid #d3d9e3;
            border-radius: 8px;
            padding: 12px;
            resize: vertical;
            font: inherit;
            margin-bottom: 14px;
        }

        .alert {
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .rated-box {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
        }

        .skip-link {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 44px;
            margin-top: 10px;
            color: var(--primary);
            font-weight: 800;
            text-decoration: none;
        }

        @media (max-width: 860px) {
            .page {
                padding: 0;
                gap: 0;
            }

            .invoice-card,
            .feedback-card,
            .doctor-rating-card {
                width: 100%;
                border-radius: 0;
            }
        }

        @media print {
            body { background: #fff; }
            .page { display: block; }
            .invoice-card {
                width: 100%;
                min-height: auto;
                border-radius: 0;
                box-shadow: none;
            }
            .feedback-card,
            .doctor-rating-card,
            .print-btn,
            .alert { display: none !important; }
        }
    </style>
</head>
<body>
@php
    $doctor = $appointment->doctor;
    $department = $appointment->department ?? $doctor?->department;
    $patientName = trim(implode(' ', array_filter([(string) $appointment->first_name, (string) $appointment->last_name])));
    $dateLabel = 'N/A';

    try {
        $dateLabel = \Carbon\Carbon::parse((string) $appointment->date)->format('l, F j, Y');
    } catch (\Throwable $e) {
        $dateLabel = (string) ($appointment->date ?? 'N/A');
    }

    $payment = $appointment->payment;
    $paymentLabels = [
        'fawry_card' => 'Fawry Card',
        'fawry_wallet' => 'Fawry Wallet',
        'instapay' => 'Instapay',
        'pay_at_hospital' => 'Pay at Hospital',
    ];

    $paymentLabel = $paymentLabels[$appointment->payment_method ?? ''] ?? ucfirst(str_replace('_', ' ', (string) $appointment->payment_method));
    $paymentNote = match ($appointment->payment_method) {
        'fawry_card' => 'Card payment is processed through Fawry after secure gateway verification.',
        'fawry_wallet' => 'Wallet payment is processed through Fawry after secure gateway verification.',
        'instapay' => 'Instapay payment is processed after secure gateway verification.',
        'pay_at_hospital' => 'Payment will be completed at the hospital reception before your appointment.',
        default => 'Payment method selected for this appointment.',
    };
    $paymentStatus = $payment?->status ?? $appointment->payment_status ?? 'pending';
    $transactionReference = $payment?->transaction_id ?: $payment?->reference_number;

    $expectedHours = intdiv((int) $expectedTimeMinutes, 60);
    $expectedMinutes = (int) $expectedTimeMinutes % 60;
    $expectedTimeLabel = $expectedHours > 0
        ? $expectedHours . 'h' . ($expectedMinutes > 0 ? ',' . $expectedMinutes . 'm' : '')
        : $expectedMinutes . 'm';

    $doctorImage = $doctor?->image ? asset('images/' . $doctor->image) : asset('images/logo_Image.png');
@endphp

<main class="page">
    <section class="invoice-card">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="status-icon"><i class="bi bi-check2"></i></div>
        <h1 class="title">Appointment Confirmed!</h1>
        <p class="subtitle">Your appointment has been successfully scheduled.</p>

        <div class="divider"></div>

        <h2 class="section-title">Appointment Details</h2>
        <div class="doctor-row">
            <img src="{{ $doctorImage }}" alt="{{ $doctor?->name ?? 'Doctor' }}" onerror="this.onerror=null;this.src='{{ asset('images/logo_Image.png') }}';">
            <div>
                <p class="doctor-name">{{ $doctor?->name ?? 'N/A' }}</p>
                <p class="doctor-meta">{{ $doctor?->specialization ?? 'N/A' }}</p>
                <p class="doctor-meta">{{ $department?->name_en ?? $department?->name ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="date-time">
            <span class="inline-icon"><i class="bi bi-calendar2-event"></i>{{ $dateLabel }}</span>
            <span class="inline-icon"><i class="bi bi-clock"></i>{{ $appointment->time ?? 'N/A' }}</span>
        </div>

        <div class="divider"></div>

        <h2 class="section-title">Patient Information</h2>
        <div class="patient-lines">
            <div>Name : <strong>{{ $patientName ?: 'N/A' }}</strong></div>
            <div>Email : <strong>{{ $appointment->email ?: 'N/A' }}</strong></div>
            <div>Phone : <strong>{{ $appointment->phone ?: 'N/A' }}</strong></div>
        </div>

        <div class="divider"></div>

        <h2 class="payment-title">payment method</h2>
        <div class="payment-line">
            <i class="bi bi-credit-card-fill"></i>
            <span>{{ $paymentLabel }}</span>
        </div>
        <div class="patient-lines">
            <div>Status : <strong>{{ ucfirst($paymentStatus) }}</strong></div>
            <div>Reference : <strong>{{ $transactionReference ?: 'N/A' }}</strong></div>
            <div>Amount : <strong>EGP {{ number_format((float) ($appointment->payment_amount ?? 0), 2) }}</strong></div>
        </div>
        <div class="payment-note">{{ $paymentNote }}</div>

        <div class="divider"></div>

        <div class="queue">
            <h2>patient number</h2>
            <div class="queue-number">{{ $patientNumber }}</div>
            <p>Number of patients waiting : {{ $waitingPatients }}</p>
            <p>Expected time : {{ $expectedTimeLabel }}</p>
        </div>

        <button type="button" class="print-btn" onclick="window.print()">Print</button>
    </section>

    <aside class="feedback-card" id="siteRatingSection">
        <h2>Rate Your Experience</h2>
        <p class="feedback-help">Your feedback helps us improve</p>

        @if(!$appointment->websiteRating)
            <form method="POST" action="{{ route('site.ratings.store', $appointment->id) }}">
                @csrf
                <input type="hidden" name="rating" id="site_rating_value">
                <div class="site-stars" aria-label="Rate your experience">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" class="site-star" data-rating="{{ $i }}" aria-label="{{ $i }} star">&#9733;</button>
                    @endfor
                </div>
                <textarea name="feedback" class="feedback" placeholder="Optional feedback...">{{ old('feedback') }}</textarea>
                <button type="submit" class="rating-submit">Submit Feedback</button>
                <a href="{{ route('home') }}" class="skip-link">Skip</a>
            </form>
        @else
            <div class="rated-box">
                @if($appointment->websiteRating->rating)
                    <strong>{{ $appointment->websiteRating->rating }}/5</strong>
                @endif
                @if($appointment->websiteRating->comment)
                    <div>{{ $appointment->websiteRating->comment }}</div>
                @endif
            </div>
        @endif
    </aside>

    @if($appointment->canReceiveDoctorRating())
        <aside class="doctor-rating-card">
            @if(!$appointment->rating)
                <h2>Rate Doctor</h2>
                <p>Your appointment is completed. You can now rate your doctor.</p>
                <a class="doctor-rating-link" href="{{ route('appointments.rate', $appointment) }}">Rate Doctor</a>
            @else
                <h2>Doctor Rated</h2>
                <p>Thanks for sharing your doctor feedback.</p>
            @endif
        </aside>
    @endif
</main>

<script>
    const ratingInput = document.getElementById('site_rating_value');
    const stars = Array.from(document.querySelectorAll('.site-star'));

    function paintStars(rating) {
        stars.forEach((star) => {
            const value = Number(star.dataset.rating || 0);
            const selected = value <= rating;
            star.classList.toggle('selected', selected);
            star.setAttribute('aria-pressed', selected ? 'true' : 'false');
        });
    }

    if (ratingInput && stars.length) {
        stars.forEach((star) => {
            star.addEventListener('click', () => {
                const rating = Number(star.dataset.rating || 0);
                ratingInput.value = String(rating);
                paintStars(rating);
            });
        });

        paintStars(Number(ratingInput.value || 0));
    }
</script>
</body>
</html>
