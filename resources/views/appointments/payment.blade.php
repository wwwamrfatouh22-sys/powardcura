<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('ui.booking.payment_title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ app()->isLocale('ar') ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #114a9e;
            --primary-dark: #0d3f87;
            --muted: #64748b;
            --line: #d9e5f5;
            --surface: rgba(248, 250, 252, .96);
            --ink: #172033;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: {{ app()->isLocale('ar') ? "'Cairo','Segoe UI',Arial,sans-serif" : "'Inter','Segoe UI',Arial,sans-serif" }};
            background: linear-gradient(120deg, #e8f2fb 0%, #f8fbff 52%, #edf4ff 100%);
            padding: 28px 14px 38px;
            color: var(--ink);
        }

        .shell {
            max-width: 1120px;
            margin: auto;
            background: var(--surface);
            border: 1px solid rgba(148, 163, 184, .28);
            border-radius: 24px;
            box-shadow: 0 20px 44px rgba(15, 23, 42, .12);
            padding: 26px;
        }

        .heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .heading h1 {
            margin: 0;
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 800;
        }

        .heading p {
            margin: 5px 0 0;
            color: var(--muted);
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(290px, 370px) 1fr;
            gap: 18px;
            align-items: start;
        }

        .summary-card,
        .pay-card {
            background: #fff;
            border: 1px solid rgba(148, 163, 184, .24);
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .08);
            padding: 20px;
        }

        .summary-grid {
            display: grid;
            gap: 12px;
        }

        .summary-item {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 12px 14px;
        }

        .summary-item small {
            color: var(--muted);
            display: block;
            margin-bottom: 4px;
        }

        .summary-item strong {
            font-size: 14px;
        }

        .method-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .method input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .method label {
            min-height: 176px;
            border: 1.8px solid #d4e1f4;
            border-radius: 16px;
            padding: 18px;
            background: #fff;
            display: flex;
            flex-direction: column;
            gap: 12px;
            cursor: pointer;
            transition: .2s ease;
        }

        .method input:checked + label {
            border-color: var(--primary);
            background: #edf5ff;
            box-shadow: 0 12px 24px rgba(17, 74, 158, .12);
            transform: translateY(-2px);
        }

        .method-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #edf4ff;
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .method-title {
            font-weight: 800;
            font-size: 16px;
        }

        .method-copy {
            color: var(--muted);
            font-size: 13px;
            line-height: 1.55;
            margin: 0;
        }

        .method-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: auto;
        }

        .tag {
            border-radius: 999px;
            background: #eef2f7;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 9px;
        }

        .gateway-note {
            border: 1px solid #cfe0f7;
            background: #f6faff;
            border-radius: 14px;
            padding: 14px 16px;
            color: #334155;
            font-size: 14px;
            line-height: 1.7;
        }

        .confirm-btn {
            border: none;
            border-radius: 12px;
            min-height: 52px;
            padding: 0 18px;
            font-weight: 800;
            background: var(--primary);
            color: #fff;
            width: 100%;
            margin-top: 16px;
            transition: .2s ease;
        }

        .confirm-btn:hover {
            background: var(--primary-dark);
        }

        .confirm-btn:disabled {
            background: #9fb4d3;
            cursor: not-allowed;
        }

        @media (max-width: 980px) {
            .layout,
            .method-grid {
                grid-template-columns: 1fr;
            }

            .method label {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
@php($selectedMethod = old('payment_method'))
<div class="shell">
    <div class="heading">
        <div>
            <h1>{{ __('ui.booking.payment_title') }}</h1>
            <p>Choose how you want to pay for this appointment.</p>
        </div>
        <a href="{{ route('doctors.show', ['doctor' => $doctor->id, 'date' => $draft['date'], 'type' => $draft['type']]) }}" class="btn btn-outline-secondary">{{ __('ui.common.back') }}</a>
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

    <div class="layout">
        <aside class="summary-card">
            <h5 class="fw-bold mb-3">{{ __('ui.booking.summary') }}</h5>
            <div class="summary-grid">
                <div class="summary-item"><small>{{ __('ui.common.doctor') }}</small><strong>{{ $doctor->name }}</strong></div>
                <div class="summary-item"><small>{{ __('ui.booking.appointment_type') }}</small><strong>{{ \App\Support\PrivateClinicBookingSupport::typeLabel($draft['type']) }}</strong></div>
                <div class="summary-item"><small>{{ __('ui.common.date') }}</small><strong>{{ \Carbon\Carbon::parse($draft['date'])->translatedFormat('l, M j, Y') }}</strong></div>
                <div class="summary-item"><small>{{ __('ui.common.time') }}</small><strong>{{ $draft['time'] }}</strong></div>
                <div class="summary-item"><small>{{ $draft['type'] === 'private' ? __('ui.booking.clinic_name') : __('ui.common.department') }}</small><strong>{{ $draft['type'] === 'private' ? ($draft['clinic_name'] ?? __('ui.common.private_clinic')) : ($doctor->department->name_en ?? __('ui.common.n_a')) }}</strong></div>
                <div class="summary-item"><small>{{ __('ui.booking.payment_amount') }}</small><strong>EGP {{ number_format((float) $draft['payment_amount'], 2) }}</strong></div>
                <div class="summary-item"><small>{{ __('ui.booking.current_payment_status') }}</small><strong>Pending</strong></div>
                @if($draft['type'] === 'private')
                    <div class="summary-item"><small>{{ __('ui.booking.clinic_address') }}</small><strong>{{ $draft['clinic_address'] ?? __('ui.common.n_a') }}</strong></div>
                    <div class="summary-item"><small>{{ __('ui.booking.clinic_phone') }}</small><strong>{{ $draft['clinic_phone'] ?? __('ui.common.n_a') }}</strong></div>
                @endif
            </div>
        </aside>

        <form action="{{ route('appointments.confirm') }}" method="POST" class="pay-card" id="paymentForm">
            @csrf
            <input type="hidden" name="booking_token" value="{{ $draft['token'] ?? '' }}">

            <h5 class="fw-bold mb-3">{{ __('ui.booking.select_payment') }}</h5>

            <div class="method-grid">
                <div class="method">
                    <input id="pm_card" type="radio" name="payment_method" value="fawry_card" {{ $selectedMethod === 'fawry_card' ? 'checked' : '' }}>
                    <label for="pm_card">
                        <span class="method-icon"><i class="bi bi-credit-card-2-front"></i></span>
                        <span class="method-title">Pay with Card</span>
                        <p class="method-copy">Redirect securely to Fawry for Visa, MasterCard, and Meeza card payments.</p>
                        <span class="method-tags"><span class="tag">Visa</span><span class="tag">MasterCard</span><span class="tag">Meeza</span></span>
                    </label>
                </div>

                <div class="method">
                    <input id="pm_wallet" type="radio" name="payment_method" value="fawry_wallet" {{ $selectedMethod === 'fawry_wallet' ? 'checked' : '' }}>
                    <label for="pm_wallet">
                        <span class="method-icon"><i class="bi bi-wallet2"></i></span>
                        <span class="method-title">Pay with Wallet</span>
                        <p class="method-copy">Use Fawry mobile wallet payment for Vodafone Cash, Orange Cash, Etisalat Cash, and other wallets.</p>
                        <span class="method-tags"><span class="tag">Vodafone</span><span class="tag">Orange</span><span class="tag">Etisalat</span></span>
                    </label>
                </div>

                <div class="method">
                    <input id="pm_hospital" type="radio" name="payment_method" value="pay_at_hospital" {{ $selectedMethod === 'pay_at_hospital' ? 'checked' : '' }}>
                    <label for="pm_hospital">
                        <span class="method-icon"><i class="bi bi-hospital"></i></span>
                        <span class="method-title">{{ $draft['type'] === 'private' ? __('ui.booking.pay_at_clinic') : __('ui.booking.pay_at_hospital') }}</span>
                        <p class="method-copy">Skip online payment and complete payment at reception before the consultation.</p>
                        <span class="method-tags"><span class="tag">In person</span></span>
                    </label>
                </div>
            </div>

            <div class="gateway-note" id="gatewayNote">
                Online payments are confirmed only after Fawry returns a verified payment response. Apple Pay support is reserved in the gateway structure for future activation.
            </div>

            <button id="confirmButton" type="submit" class="confirm-btn" disabled>Continue</button>
        </form>
    </div>
</div>

<script>
    const inputs = Array.from(document.querySelectorAll('input[name="payment_method"]'));
    const button = document.getElementById('confirmButton');
    const note = document.getElementById('gatewayNote');

    function refreshState() {
        const selected = inputs.find((input) => input.checked);
        button.disabled = !selected;

        if (!selected) {
            button.textContent = 'Continue';
            return;
        }

        const isHospital = selected.value === 'pay_at_hospital';
        button.textContent = isHospital ? 'Confirm Appointment' : 'Continue to Fawry';
        note.textContent = isHospital
            ? 'Your appointment will be confirmed now and payment will remain pending until you pay at reception.'
            : 'You will be redirected to Fawry. The appointment is confirmed only after Fawry verifies successful payment.';
    }

    inputs.forEach((input) => input.addEventListener('change', refreshState));
    refreshState();
</script>
</body>
</html>
