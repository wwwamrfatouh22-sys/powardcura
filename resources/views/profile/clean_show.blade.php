<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Patient Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --page-bg: linear-gradient(135deg, #e6f1ff 0%, #f7fbff 48%, #eef4f8 100%);
            --card-bg: rgba(255, 255, 255, 0.94);
            --ink: #223047;
            --muted: #6b7a90;
            --line: #d9e4f1;
            --primary: #1554c7;
            --primary-dark: #113d8d;
            --success-bg: #eaf7ee;
            --success-text: #2b8a57;
            --pending-bg: #fff3d8;
            --pending-text: #8b6500;
            --danger-bg: #fde8e8;
            --danger-text: #b64242;
            --shadow: 0 20px 45px rgba(22, 58, 117, 0.12);
            --radius: 24px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--ink);
            background: var(--page-bg);
        }

        .profile-shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px 18px 48px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .brand-block h1 {
            margin: 0;
            font-size: clamp(1.8rem, 2vw, 2.3rem);
            font-weight: 700;
        }

        .brand-block p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.98rem;
        }

        .topbar-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .action-btn {
            border: 0;
            border-radius: 14px;
            padding: 12px 18px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .action-outline {
            background: rgba(255,255,255,.72);
            color: var(--primary);
            border: 1px solid #c7d7eb;
        }

        .action-primary {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 12px 22px rgba(21, 84, 199, 0.18);
        }

        .grid-layout {
            display: grid;
            grid-template-columns: minmax(320px, 380px) minmax(0, 1fr);
            gap: 24px;
        }

        .stack {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .panel {
            background: var(--card-bg);
            border: 1px solid rgba(255,255,255,.7);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px;
            backdrop-filter: blur(8px);
        }

        .patient-hero {
            padding: 28px;
        }

        .patient-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eaf2ff;
            color: var(--primary);
            font-size: .85rem;
            font-weight: 700;
            margin-bottom: 18px;
        }

        .patient-name {
            margin: 0;
            font-size: clamp(1.7rem, 3vw, 2.3rem);
            font-weight: 700;
        }

        .patient-subtitle {
            margin: 10px 0 22px;
            color: var(--muted);
            font-size: 1rem;
            line-height: 1.7;
        }

        .summary-list,
        .info-list {
            display: grid;
            gap: 14px;
        }

        .edit-form {
            display: grid;
            gap: 14px;
        }

        .edit-form[hidden] {
            display: none;
        }

        .form-label {
            font-size: .9rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #31435d;
        }

        .form-control {
            border-radius: 14px;
            border: 1px solid #cbd9eb;
            padding: 12px 14px;
        }

        .form-control:focus {
            border-color: #2f80ed;
            box-shadow: 0 0 0 3px rgba(47, 128, 237, 0.12);
        }

        .feedback-box {
            display: none;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: .92rem;
        }

        .feedback-box.show {
            display: block;
        }

        .feedback-success {
            background: #eaf7ee;
            color: #246b45;
        }

        .feedback-error {
            background: #fdeaea;
            color: #9c3737;
        }

        .summary-list {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 18px;
        }

        .summary-card,
        .info-row {
            border-radius: 18px;
            background: #f8fbff;
            border: 1px solid var(--line);
            padding: 16px;
        }

        .summary-card .label,
        .info-row .label {
            display: block;
            color: var(--muted);
            font-size: .86rem;
            margin-bottom: 8px;
        }

        .summary-card .value,
        .info-row .value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
            word-break: break-word;
        }

        .section-title {
            margin: 0 0 18px;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .section-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .edit-trigger {
            border: 1px solid #c7d7eb;
            background: #fff;
            color: var(--primary);
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 700;
            font-size: .9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .edit-trigger:hover {
            background: #f1f7ff;
        }

        .appointments-head {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .appointments-meta {
            color: var(--muted);
            font-size: .95rem;
        }

        .appointment-list {
            display: grid;
            gap: 16px;
        }

        .prescription-list {
            display: grid;
            gap: 14px;
        }

        .prescription-card {
            border: 1px solid var(--line);
            border-radius: 18px;
            background: #fbfdff;
            padding: 18px;
        }

        .prescription-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .prescription-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
        }

        .prescription-meta {
            color: var(--muted);
            font-size: .9rem;
            line-height: 1.6;
        }

        .prescription-notes {
            margin: 0;
            color: #4c5b70;
            font-size: .94rem;
            line-height: 1.7;
        }

        .appointment-card {
            border: 1px solid var(--line);
            border-radius: 20px;
            background: #fbfdff;
            padding: 18px;
        }

        .next-date-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            row-gap: 12px;
            column-gap: 16px;
            align-items: center;
            font-size: 16px;
        }

        .next-date-grid .label {
            color: #767d8d;
        }

        .next-date-grid .value {
            color: #2f3747;
            font-weight: 600;
            text-align: right;
        }

        .date-badge {
            background: #2f6fff;
            color: #fff;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 14px;
            display: inline-block;
            box-shadow: 0 8px 16px rgba(47, 111, 255, .18);
        }

        .appointment-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .appointment-title {
            margin: 0 0 6px;
            font-size: 1.02rem;
            font-weight: 700;
        }

        .appointment-sub {
            color: var(--muted);
            font-size: .94rem;
            line-height: 1.6;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 112px;
            border-radius: 999px;
            padding: 8px 12px;
            font-size: .82rem;
            font-weight: 700;
        }

        .status-pending {
            background: var(--pending-bg);
            color: var(--pending-text);
        }

        .status-confirmed {
            background: #e8f1ff;
            color: var(--primary-dark);
        }

        .status-completed {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .status-canceled,
        .status-cancelled,
        .status-rejected {
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .appointment-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .appointment-info {
            border-radius: 14px;
            background: #fff;
            border: 1px solid #e4edf7;
            padding: 12px 14px;
        }

        .appointment-info .label {
            display: block;
            color: var(--muted);
            font-size: .8rem;
            margin-bottom: 5px;
        }

        .appointment-info .value {
            font-size: .95rem;
            font-weight: 600;
        }

        .empty-state {
            border: 1px dashed #c8d6e8;
            border-radius: 20px;
            padding: 28px 20px;
            background: rgba(255,255,255,.55);
            text-align: center;
            color: var(--muted);
        }

        .rating-box {
            margin-top: 14px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff;
            border: 1px solid #e4edf7;
        }

        .stars-readonly {
            color: #f2b01e;
            font-size: 1rem;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .rating-comment {
            margin: 0;
            color: #536174;
            font-size: .92rem;
            line-height: 1.65;
        }

        .rate-toggle {
            margin-top: 14px;
            border-top: 1px dashed #d7e4f4;
            padding-top: 14px;
        }

        .rate-toggle summary {
            cursor: pointer;
            list-style: none;
            color: var(--primary);
            font-weight: 700;
        }

        .rate-toggle summary::-webkit-details-marker {
            display: none;
        }

        .rating-form {
            margin-top: 14px;
        }

        .stars-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 4px;
            margin-bottom: 14px;
        }

        .stars-input input {
            display: none;
        }

        .stars-input label {
            font-size: 28px;
            color: #c8d2df;
            cursor: pointer;
            transition: color .2s ease;
        }

        .stars-input label:hover,
        .stars-input label:hover ~ label,
        .stars-input input:checked ~ label {
            color: #f2b01e;
        }

        .rating-form textarea {
            width: 100%;
            min-height: 92px;
            border-radius: 14px;
            border: 1px solid #c9d8ec;
            padding: 12px 14px;
            resize: vertical;
            font: inherit;
        }

        .rating-form textarea:focus {
            outline: none;
            border-color: #2f80ed;
            box-shadow: 0 0 0 3px rgba(47, 128, 237, 0.12);
        }

        .submit-rating-btn {
            margin-top: 14px;
            background: var(--primary-dark);
            border: none;
            border-radius: 12px;
            color: #fff;
            padding: 12px 18px;
            font-size: .95rem;
            font-weight: 700;
        }

        .rating-lock {
            margin-top: 14px;
            color: var(--muted);
            font-size: .92rem;
        }

        .appointment-actions {
            margin-top: 14px;
            display: grid;
            gap: 10px;
            border-top: 1px dashed #d7e4f4;
            padding-top: 14px;
        }

        .manage-toggle {
            border-radius: 16px;
            background: #fff;
            border: 1px solid #e4edf7;
            padding: 12px 14px;
        }

        .manage-toggle summary {
            cursor: pointer;
            list-style: none;
            color: var(--primary);
            font-weight: 800;
        }

        .manage-toggle summary::-webkit-details-marker {
            display: none;
        }

        .appointment-manage-form {
            display: grid;
            gap: 10px;
            margin-top: 12px;
        }

        .appointment-manage-form label {
            color: #31435d;
            font-size: .82rem;
            font-weight: 800;
        }

        .appointment-manage-form input,
        .appointment-manage-form select,
        .appointment-manage-form textarea {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #c9d8ec;
            padding: 11px 12px;
            font: inherit;
            background: #fff;
        }

        .appointment-manage-form textarea {
            min-height: 82px;
            resize: vertical;
        }

        .manage-status {
            min-height: 18px;
            color: var(--muted);
            font-size: .82rem;
            line-height: 1.5;
        }

        .manage-submit {
            border: 0;
            border-radius: 12px;
            color: #fff;
            padding: 12px 16px;
            font-weight: 800;
            background: var(--primary);
        }

        .manage-submit.danger {
            background: #b91c1c;
        }

        .flash-alert {
            border-radius: 16px;
            margin-bottom: 20px;
            border: none;
            box-shadow: 0 12px 26px rgba(22, 58, 117, 0.08);
        }

        .info-static {
            display: grid;
            gap: 14px;
        }

        @media (max-width: 991.98px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .profile-shell {
                padding: 20px 14px 36px;
            }

            .summary-list,
            .appointment-grid {
                grid-template-columns: 1fr;
            }

            .next-date-grid {
                grid-template-columns: 1fr;
            }

            .next-date-grid .value {
                text-align: left;
            }

            .panel,
            .patient-hero {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="profile-shell">
        @php
            $genderValue = strtolower((string) $patient->gender);
            $genderLabel = in_array($genderValue, ['male', 'female'], true) ? ucfirst($genderValue) : 'Not available';
        @endphp

        @if(session('success'))
            <div class="alert alert-success flash-alert">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger flash-alert">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger flash-alert">{{ $errors->first() }}</div>
        @endif

        <div class="topbar">
            <div class="brand-block">
                <h1>Patient Profile</h1>
                <p>Personal details and appointment history in one clean view.</p>
            </div>

            <div class="topbar-actions">
                <button type="button" class="action-btn action-outline" onclick="window.print()">Print Profile</button>
                <a href="{{ route('home') }}" class="action-btn action-primary">Back To Home</a>
            </div>
        </div>

        <div class="grid-layout">
            <div class="stack">
                <section class="panel patient-hero">
                    <div class="patient-badge">Patient Record</div>
                    <h2 class="patient-name">{{ $patient->full_name }}</h2>
                    <p class="patient-subtitle">
                        File number:
                        {{ $patient->file_number ? '123' . $patient->file_number : 'Not assigned yet' }}
                    </p>

                    <div class="summary-list">
                        <div class="summary-card">
                            <span class="label">Gender</span>
                            <span class="value" id="profile-gender-summary">{{ $genderLabel }}</span>
                        </div>
                        <div class="summary-card">
                            <span class="label">Age</span>
                            <span class="value">{{ $patient->age ? $patient->age . ' years' : 'Not available' }}</span>
                        </div>
                        <div class="summary-card">
                            <span class="label">Blood Type</span>
                            <span class="value">{{ $patient->blood_type ?: 'Not available' }}</span>
                        </div>
                        <div class="summary-card">
                            <span class="label">Last Visit</span>
                            <span class="value">{{ $patient->last_visit ?: 'Not available' }}</span>
                        </div>
                    </div>
                </section>

                <section class="panel">
                    <div class="section-header-row">
                        <h3 class="section-title mb-0">Personal Information</h3>
                        <button type="button" id="edit-profile-button" class="edit-trigger">
                            <span aria-hidden="true">✎</span>
                            <span>Edit</span>
                        </button>
                    </div>
                    <div class="info-static" id="profile-info-display">
                        <div class="info-row">
                            <span class="label">Email</span>
                            <span class="value" id="profile-email">{{ $patient->user?->email ?: 'Not available' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Name</span>
                            <span class="value" id="profile-name">{{ $patient->full_name ?: 'Not available' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Gender</span>
                            <span class="value" id="profile-gender">{{ $genderLabel }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Phone</span>
                            <span class="value" id="profile-phone">{{ $patient->phone ?: 'Not available' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Address</span>
                            <span class="value" id="profile-address">{{ $patient->address ?: 'Not available' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">National ID</span>
                            <span class="value">{{ $patient->national_id ?: 'Not available' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="label">Date of Birth</span>
                            <span class="value">{{ $patient->dob ?: 'Not available' }}</span>
                        </div>
                    </div>
                    <div id="profile-feedback" class="feedback-box"></div>
                    <form id="profile-edit-form" class="edit-form mt-3" hidden>
                        <div>
                            <label for="full_name" class="form-label">Name</label>
                            <input id="full_name" name="full_name" type="text" class="form-control" value="{{ $patient->full_name }}" required>
                        </div>
                        <div>
                            <label for="gender" class="form-label">Gender</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="male" {{ $genderValue === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $genderValue === 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div>
                            <label for="phone" class="form-label">Phone</label>
                            <input id="phone" name="phone" type="text" class="form-control" value="{{ $patient->phone }}">
                        </div>
                        <div>
                            <label for="address" class="form-label">Address</label>
                            <input id="address" name="address" type="text" class="form-control" value="{{ $patient->address }}">
                        </div>
                        <div class="topbar-actions">
                            <button type="submit" class="action-btn action-primary">Save Changes</button>
                            <button type="button" id="cancel-edit-button" class="action-btn action-outline">Cancel</button>
                        </div>
                    </form>
                </section>

                <section class="panel">
                    <h3 class="section-title">Upcoming Appointment</h3>
                    <div id="next-appointment-container">
                        @if($nextAppointment)
                            <div class="next-date-grid">
                                <div class="label">date</div>
                                <div class="value">
                                    <span class="date-badge">{{ $nextAppointment->date ? \Carbon\Carbon::parse($nextAppointment->date)->format('F j, Y') : 'No upcoming appointment' }}</span>
                                </div>

                                <div class="label">time</div>
                                <div class="value">{{ $nextAppointment->time ?? '-' }}</div>

                                <div class="label">doctor</div>
                                <div class="value">{{ $nextAppointment->doctor?->name ?? '-' }}</div>

                                <div class="label">Specialization</div>
                                <div class="value">{{ $nextAppointment->doctor?->specialization ?? '-' }}</div>
                            </div>
                        @else
                            <div class="empty-state">No upcoming appointment</div>
                        @endif
                    </div>
                </section>

                <section class="panel">
                    <h3 class="section-title">Doctor Prescription</h3>
                    <div id="prescriptions-container" class="prescription-list">
                        <div class="empty-state">Loading prescriptions...</div>
                    </div>
                </section>

                <section class="panel">
                    <h3 class="section-title">Radiology & Laboratory Results</h3>
                    <div class="empty-state" style="text-align:left;">
                        View and download protected radiology images, scan files, PDFs, and laboratory reports linked to your account.
                    </div>
                    <a href="{{ route('results.index') }}" class="action-btn action-primary" style="margin-top:14px;">Open Results</a>
                </section>
            </div>

            <div class="stack">
                <section class="panel">
                    <div class="appointments-head">
                        <div>
                            <h3 class="section-title mb-1">Appointments</h3>
                            <div class="appointments-meta">All available appointments linked to this patient profile.</div>
                        </div>
                        <div class="appointments-meta">{{ $patient->appointments->count() }} total</div>
                    </div>

                    @if($patient->appointments->isEmpty())
                        <div class="empty-state">No appointments found yet.</div>
                    @else
                        <div class="appointment-list">
                            @foreach($patient->appointments as $appointment)
                                <article class="appointment-card">
                                    <div class="appointment-top">
                                        <div>
                                            <h4 class="appointment-title">{{ $appointment->doctor?->name ?? 'Doctor not assigned' }}</h4>
                                            <div class="appointment-sub">
                                                {{ $appointment->department?->name_en ?? 'Department not assigned' }}<br>
                                                {{ $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('F j, Y') : 'No date' }}
                                                at {{ $appointment->time ?? 'No time' }}
                                            </div>
                                        </div>

                                        <span class="status-badge status-{{ strtolower($appointment->status ?? 'pending') }}">
                                            {{ $appointment->status ?? 'Pending' }}
                                        </span>
                                    </div>

                                    <div class="appointment-grid">
                                        <div class="appointment-info">
                                            <span class="label">Doctor</span>
                                            <span class="value">{{ $appointment->doctor?->name ?? 'Not assigned' }}</span>
                                        </div>
                                        <div class="appointment-info">
                                            <span class="label">Department</span>
                                            <span class="value">{{ $appointment->department?->name_en ?? 'Not assigned' }}</span>
                                        </div>
                                        <div class="appointment-info">
                                            <span class="label">Reason</span>
                                            <span class="value">{{ $appointment->reason ?: 'Not available' }}</span>
                                        </div>
                                        <div class="appointment-info">
                                            <span class="label">Contact Used</span>
                                            <span class="value">{{ $appointment->email ?: ($patient->user?->email ?: 'Not available') }}</span>
                                        </div>
                                    </div>

                                    @if($appointment->rating)
                                        <div class="rating-box">
                                            <div class="stars-readonly">
                                                {{ str_repeat('★', (int) $appointment->rating->rating) }}{{ str_repeat('☆', 5 - (int) $appointment->rating->rating) }}
                                            </div>
                                            <p class="rating-comment">
                                                {{ $appointment->rating->comment ?: 'You rated this appointment without an additional comment.' }}
                                            </p>
                                        </div>
                                    @elseif($appointment->canReceiveDoctorRating())
                                        <details class="rate-toggle">
                                            <summary>Rate Doctor</summary>
                                            <form class="rating-form" action="{{ route('doctor.ratings.store', $appointment) }}" method="POST">
                                                @csrf
                                                <div class="stars-input">
                                                    @for($star = 5; $star >= 1; $star--)
                                                        <input type="radio" id="rating-{{ $appointment->id }}-{{ $star }}" name="rating" value="{{ $star }}">
                                                        <label for="rating-{{ $appointment->id }}-{{ $star }}">★</label>
                                                    @endfor
                                                </div>

                                                <textarea name="comment" placeholder="Share optional feedback about the doctor..."></textarea>

                                                <button type="submit" class="submit-rating-btn">Submit Rating</button>
                                            </form>
                                        </details>
                                    @else
                                        <div class="rating-lock">Rating becomes available after this appointment is confirmed.</div>
                                    @endif

                                    @if($appointment->canBeManagedByPatient())
                                        <div class="appointment-actions">
                                            <details class="manage-toggle">
                                                <summary>Reschedule Appointment</summary>
                                                <form
                                                    class="appointment-manage-form reschedule-form"
                                                    action="{{ route('appointments.reschedule', $appointment) }}"
                                                    method="POST"
                                                    data-slots-url="{{ route('appointments.reschedule-slots', $appointment) }}"
                                                    data-current-time="{{ substr((string) $appointment->time, 0, 5) }}"
                                                >
                                                    @csrf
                                                    <label for="reschedule-date-{{ $appointment->id }}">New date</label>
                                                    <input
                                                        id="reschedule-date-{{ $appointment->id }}"
                                                        type="date"
                                                        name="date"
                                                        value="{{ \Carbon\Carbon::parse((string) $appointment->date)->toDateString() }}"
                                                        min="{{ now()->toDateString() }}"
                                                        required
                                                    >

                                                    <label for="reschedule-time-{{ $appointment->id }}">New time</label>
                                                    <select id="reschedule-time-{{ $appointment->id }}" name="time" required>
                                                        <option value="">Loading available slots...</option>
                                                    </select>
                                                    <div class="manage-status">Choose a date to refresh available slots.</div>

                                                    <button type="submit" class="manage-submit">Reschedule Appointment</button>
                                                </form>
                                            </details>

                                            <details class="manage-toggle">
                                                <summary>Cancel Appointment</summary>
                                                <form
                                                    class="appointment-manage-form"
                                                    action="{{ route('appointments.cancel', $appointment) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Cancel this appointment?');"
                                                >
                                                    @csrf
                                                    <label for="cancel-reason-{{ $appointment->id }}">Cancellation reason optional</label>
                                                    <textarea id="cancel-reason-{{ $appointment->id }}" name="cancellation_reason" placeholder="Optional reason..."></textarea>
                                                    <button type="submit" class="manage-submit danger">Cancel Appointment</button>
                                                </form>
                                            </details>
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const profileDataUrl = @json(route('profile.data'));
        const prescriptionsUrl = @json(route('profile.prescriptions'));
        const profileUpdateUrl = @json(route('profile.update'));

        async function fetchJson(url, options = {}) {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    ...options.headers,
                },
                ...options,
            });

            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            return data;
        }

        function renderNextAppointment(appointment) {
            const container = document.getElementById('next-appointment-container');

            if (!appointment) {
                container.innerHTML = '<div class="empty-state">No upcoming appointment</div>';
                return;
            }

            container.innerHTML = `
                <div class="next-date-grid">
                    <div class="label">date</div>
                    <div class="value">
                        <span class="date-badge">${appointment.date_label ?? 'No upcoming appointment'}</span>
                    </div>

                    <div class="label">time</div>
                    <div class="value">${appointment.time ?? '-'}</div>

                    <div class="label">doctor</div>
                    <div class="value">${appointment.doctor_name ?? '-'}</div>

                    <div class="label">Specialization</div>
                    <div class="value">${appointment.specialization ?? '-'}</div>
                </div>
            `;
        }

        function renderPrescriptions(prescriptions) {
            const container = document.getElementById('prescriptions-container');

            if (!prescriptions.length) {
                container.innerHTML = '<div class="empty-state">No prescriptions available.</div>';
                return;
            }

            container.innerHTML = prescriptions.map((prescription) => `
                <article class="prescription-card">
                    <div class="prescription-top">
                        <div>
                            <h4 class="prescription-title">${prescription.medication ?? 'Prescription'}</h4>
                            <div class="prescription-meta">Doctor: ${prescription.doctor_name ?? 'Doctor not assigned'}</div>
                        </div>
                        <div class="prescription-meta">${prescription.date ?? 'No date'}</div>
                    </div>
                    <p class="prescription-notes">${prescription.notes ?? 'No notes available.'}</p>
                </article>
            `).join('');
        }

        function setFeedback(message, type) {
            const feedback = document.getElementById('profile-feedback');
            feedback.className = `feedback-box show ${type === 'success' ? 'feedback-success' : 'feedback-error'}`;
            feedback.textContent = message;
        }

        function formatGender(value) {
            if (value === 'male') {
                return 'Male';
            }

            if (value === 'female') {
                return 'Female';
            }

            return 'Not available';
        }

        function toggleEditForm(showForm) {
            const form = document.getElementById('profile-edit-form');
            const display = document.getElementById('profile-info-display');
            const feedback = document.getElementById('profile-feedback');

            form.hidden = !showForm;
            display.hidden = showForm;

            if (!showForm) {
                feedback.className = 'feedback-box';
                feedback.textContent = '';
            }
        }

        async function loadProfileData() {
            try {
                const result = await fetchJson(profileDataUrl);
                const payload = result.data;
                renderNextAppointment(payload.next_appointment);

                if (payload.patient) {
                    document.querySelector('.patient-name').textContent = payload.patient.full_name ?? 'Patient';
                    document.getElementById('profile-name').textContent = payload.patient.full_name ?? 'Not available';
                    document.getElementById('profile-email').textContent = payload.patient.email ?? 'Not available';
                    document.getElementById('profile-gender').textContent = formatGender(payload.patient.gender);
                    document.getElementById('profile-gender-summary').textContent = formatGender(payload.patient.gender);
                    document.getElementById('profile-phone').textContent = payload.patient.phone ?? 'Not available';
                    document.getElementById('profile-address').textContent = payload.patient.address ?? 'Not available';
                    document.getElementById('full_name').value = payload.patient.full_name ?? '';
                    document.getElementById('gender').value = payload.patient.gender ?? '';
                    document.getElementById('phone').value = payload.patient.phone ?? '';
                    document.getElementById('address').value = payload.patient.address ?? '';
                }
            } catch (error) {
                renderNextAppointment(null);
            }
        }

        async function loadPrescriptions() {
            try {
                const result = await fetchJson(prescriptionsUrl);
                renderPrescriptions(result.data ?? []);
            } catch (error) {
                renderPrescriptions([]);
            }
        }

        async function loadRescheduleSlots(form) {
            const dateInput = form.querySelector('input[name="date"]');
            const timeSelect = form.querySelector('select[name="time"]');
            const status = form.querySelector('.manage-status');
            const endpoint = form.dataset.slotsUrl;

            if (!dateInput || !timeSelect || !status || !endpoint) {
                return;
            }

            timeSelect.innerHTML = '<option value="">Loading available slots...</option>';
            status.textContent = 'Refreshing available slots...';

            try {
                const response = await fetch(`${endpoint}?date=${encodeURIComponent(dateInput.value)}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                const slots = Array.isArray(data.slots) ? data.slots : [];

                if (!slots.length) {
                    timeSelect.innerHTML = '<option value="">No available slots</option>';
                    status.textContent = 'No available slots for this date.';
                    return;
                }

                timeSelect.innerHTML = '<option value="">Select a time</option>';

                slots.forEach((slot) => {
                    const time = String(slot.time || '').slice(0, 5);
                    if (!time) return;

                    const option = document.createElement('option');
                    option.value = time;
                    option.textContent = slot.end_time ? `${time} - ${slot.end_time}` : time;

                    if (time === form.dataset.currentTime) {
                        option.selected = true;
                    }

                    timeSelect.appendChild(option);
                });

                status.textContent = `${slots.length} available slot${slots.length === 1 ? '' : 's'} found.`;
            } catch (error) {
                timeSelect.innerHTML = '<option value="">Unable to load slots</option>';
                status.textContent = 'We could not refresh slots. Please try again.';
            }
        }

        document.querySelectorAll('.reschedule-form').forEach((form) => {
            const dateInput = form.querySelector('input[name="date"]');
            if (dateInput) {
                dateInput.addEventListener('change', () => loadRescheduleSlots(form));
            }
            loadRescheduleSlots(form);
        });

        document.getElementById('profile-edit-form').addEventListener('submit', async (event) => {
            event.preventDefault();

            const form = event.currentTarget;
            const payload = {
                full_name: form.full_name.value.trim(),
                gender: form.gender.value,
                phone: form.phone.value.trim(),
                address: form.address.value.trim(),
            };

            try {
                const result = await fetchJson(profileUpdateUrl, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                setFeedback(result.message ?? 'Profile updated successfully.', 'success');
                await loadProfileData();
                toggleEditForm(false);
            } catch (error) {
                const message = error?.message
                    ?? Object.values(error?.errors ?? {}).flat().join(' ')
                    ?? 'Unable to update profile right now.';
                setFeedback(message, 'error');
            }
        });

        document.getElementById('edit-profile-button').addEventListener('click', () => {
            toggleEditForm(true);
        });

        document.getElementById('cancel-edit-button').addEventListener('click', () => {
            toggleEditForm(false);
            loadProfileData();
        });

        loadProfileData();
        loadPrescriptions();
    </script>
</body>
</html>
