<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>NUH Admin - Doctor Schedule</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Inter, Arial, sans-serif; }
    :root {
        --primary: #114a9f; --primary-dark: #0b3f8c; --text-dark: #1f2937; --text-muted: #6b7280;
        --danger: #ff3b3b; --surface: rgba(255,255,255,.96); --border: rgba(148,163,184,.24);
        --shadow: 0 16px 34px rgba(34,52,84,.15);
    }
    body {
        min-height: 100vh; color: var(--text-dark);
        background: radial-gradient(circle at top left, rgba(84,170,255,.26), transparent 32%), linear-gradient(180deg, #eef4fb 0%, #f8fafc 48%, #edf3f9 100%);
    }
    a { text-decoration: none; color: inherit; }
    .sidebar { position: fixed; top: 18px; left: 18px; width: 280px; height: calc(100vh - 36px); z-index: 1100; transform: translateX(-120%); transition: transform .35s ease; }
    .sidebar.open { transform: translateX(0); }
    .sidebar-panel { height: 100%; background: rgba(247,247,247,.97); border-radius: 34px; box-shadow: 0 18px 36px rgba(0,0,0,.14); padding: 28px 18px 18px; display: flex; flex-direction: column; }
    .sidebar-logo { display: flex; justify-content: center; align-items: center; min-height: 86px; margin-bottom: 24px; }
    .sidebar-logo img { max-width: 145px; height: auto; object-fit: contain; }
    .sidebar-nav { display: flex; flex-direction: column; gap: 8px; }
    .sidebar-nav a { display: flex; align-items: center; gap: 14px; color: #2f3947; padding: 16px 18px; border-radius: 18px; font-size: 16px; font-weight: 500; transition: .22s ease; }
    .sidebar-nav a:hover { background: #eceff3; transform: translateX(4px); }
    .sidebar-nav a.active, .sidebar-nav a.active:hover { background: var(--primary); color: #fff; box-shadow: 0 10px 22px rgba(17,74,159,.26); transform: none; }
    .sidebar-nav a i { width: 24px; text-align: center; font-size: 20px; }
    .logout-wrap { margin-top: auto; padding-top: 18px; }
    .logout-btn { display: flex; width: 100%; border: none; background: var(--danger); color: #fff; padding: 16px 18px; border-radius: 14px; font-size: 18px; font-weight: 700; align-items: center; justify-content: center; }
    .overlay { position: fixed; inset: 0; background: rgba(0,0,0,.22); opacity: 0; visibility: hidden; transition: .3s ease; z-index: 1000; }
    .overlay.show { opacity: 1; visibility: visible; }
    .main { min-height: 100vh; padding: 22px 28px 52px; }
    .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 30px; }
    .topbar-left { display: flex; align-items: flex-start; gap: 16px; }
    .brand-box img { max-width: 120px; height: auto; object-fit: contain; }
    .menu-toggle { width: 46px; height: 46px; border: none; background: transparent; font-size: 28px; color: #4b5563; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .admin-box { display: flex; align-items: center; gap: 10px; }
    .admin-info { text-align: right; line-height: 1.2; }
    .admin-info .name { font-size: 15px; font-weight: 600; }
    .admin-info .role { font-size: 14px; color: var(--text-muted); }
    .avatar { width: 46px; height: 46px; border-radius: 50%; background: #0b4aa7; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; box-shadow: 0 10px 18px rgba(17,74,159,.25); }
    .page-shell { max-width: 1220px; margin: 0 auto; }
    .page-header { display: flex; justify-content: space-between; gap: 18px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 22px; }
    .page-kicker { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 999px; background: rgba(17,74,159,.09); color: var(--primary); font-size: 13px; font-weight: 700; margin-bottom: 14px; }
    .page-header h1 { font-size: 32px; font-weight: 700; margin: 0 0 8px; }
    .page-header p { margin: 0; color: var(--text-muted); line-height: 1.6; }
    .secondary-link { display: inline-flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: 16px; background: rgba(255,255,255,.82); border: 1px solid var(--border); box-shadow: var(--shadow); font-weight: 600; }
    .panel { background: var(--surface); border: 1px solid var(--border); border-radius: 24px; box-shadow: var(--shadow); padding: 24px; margin-bottom: 22px; }
    .panel-head { display: flex; justify-content: space-between; gap: 14px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 18px; }
    .panel-head h2 { font-size: 22px; font-weight: 700; margin: 0 0 6px; }
    .panel-head p { color: var(--text-muted); margin: 0; }
    .field-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .field label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 700; color: #334155; }
    .form-control, .form-select { min-height: 46px; border-radius: 14px; border: 1px solid #d9e2ec; background: #f8fbff; padding: 10px 12px; box-shadow: none; }
    .form-control:focus, .form-select:focus { border-color: rgba(17,74,159,.45); box-shadow: 0 0 0 4px rgba(17,74,159,.10); background: #fff; }
    .btn-primary-custom, .btn-soft, .btn-danger-soft { min-height: 42px; border-radius: 13px; font-weight: 700; border: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 14px; }
    .btn-primary-custom { background: var(--primary); color: #fff; }
    .btn-primary-custom:hover { background: var(--primary-dark); color: #fff; }
    .btn-soft { background: #eef2f7; color: #334155; }
    .btn-danger-soft { background: #ffe8e8; color: #b42318; }
    .table-wrap { overflow-x: auto; border: 1px solid rgba(226,232,240,.9); border-radius: 18px; }
    .custom-table { margin: 0; min-width: 900px; }
    .custom-table th { background: var(--primary); color: #fff; font-size: 13px; padding: 13px 14px; white-space: nowrap; }
    .custom-table td { background: rgba(255,255,255,.88); padding: 12px 14px; vertical-align: middle; font-size: 14px; }
    .inline-form { display: grid; grid-template-columns: 150px 140px 130px 130px 110px 90px 46px; gap: 10px; align-items: end; min-width: 820px; }
    .inline-form.blocked { grid-template-columns: 150px 190px 190px minmax(180px, 1fr) 110px 90px 46px; }
    .inline-form.time-off { grid-template-columns: 150px 190px 190px minmax(180px, 1fr) 90px; }
    .confirm-row { display: flex; align-items: center; gap: 8px; margin-top: 10px; color: #475569; font-size: 13px; font-weight: 600; }
    .confirm-row input { width: 16px; height: 16px; }
    .preview-form { display: grid; grid-template-columns: 180px 180px 120px; gap: 12px; align-items: end; margin-bottom: 16px; }
    .slot-grid { display: flex; flex-wrap: wrap; gap: 10px; }
    .slot-chip { display: inline-flex; align-items: center; gap: 8px; padding: 9px 12px; border-radius: 999px; background: #eef6ff; color: #0b4aa7; font-weight: 700; font-size: 13px; border: 1px solid rgba(17,74,159,.12); }
    .weekly-overview { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; margin-bottom: 22px; }
    .day-card { margin-top: 0; padding: 12px 14px; background: #f9fafb; border-radius: 10px; border: 1px solid rgba(226,232,240,.95); }
    .day-card strong { display: block; font-size: 14px; font-weight: 800; color: #1e3a5f; margin-bottom: 8px; }
    .day-card .shift-chip { margin: 4px 4px 4px 0; }
    .shift-chip { display: inline-block; padding: 6px 12px; background: #eef5ff; color: #1d4ed8; border-radius: 20px; font-size: 13px; font-weight: 500; border: 1px solid rgba(29,78,216,.12); }
    .shift-chip .chip-type { font-weight: 600; font-size: 11px; color: var(--text-muted); margin-left: 8px; }
    .availability-type-block { padding: 4px 0 20px; margin-bottom: 8px; }
    .availability-type-block.is-split { border-bottom: 1px solid rgba(226,232,240,.95); margin-bottom: 22px; padding-bottom: 22px; }
    .quick-shifts { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-top: 12px; }
    .quick-shifts form { display: inline; margin: 0; }
    .quick-shifts .btn { border-radius: 10px; font-weight: 600; }
    .weekly-hint { font-size: 13px; color: var(--text-muted); margin: 0 0 14px; }
    .day-stack { display: grid; gap: 14px; }
    .day-panel { border: 1px solid rgba(226,232,240,.9); border-radius: 18px; overflow: hidden; background: rgba(255,255,255,.72); }
    .day-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 13px 16px; background: #f1f6fd; font-weight: 800; color: #1e3a5f; }
    .shift-list { display: grid; gap: 12px; padding: 14px; }
    .shift-card { border: 1px solid rgba(148,163,184,.18); border-radius: 16px; padding: 12px; background: rgba(255,255,255,.88); }
    .time-range { font-weight: 800; color: #111827; }
    .muted { color: var(--text-muted); }
    .badge-soft { display: inline-flex; align-items: center; justify-content: center; padding: 6px 10px; border-radius: 999px; background: rgba(17,74,159,.09); color: var(--primary); font-size: 12px; font-weight: 700; }
    .badge-muted { background: #eef2f7; color: #475569; }
    .badge-warning-soft { background: #fff4d6; color: #915f00; }
    .empty-state { padding: 18px; color: var(--text-muted); text-align: center; }
    .alert { border-radius: 18px; border: none; box-shadow: var(--shadow); }
    @media (max-width: 1000px) { .field-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 720px) { .main { padding: 18px 14px 34px; } .panel { padding: 18px; border-radius: 20px; } .field-grid, .preview-form { grid-template-columns: 1fr; } .page-header h1 { font-size: 26px; } }
        .settings-link { background: var(--primary); color: #fff; }
    .settings-link:hover { background: var(--primary-dark); color: #fff; }
    </style>
</head>

<body>
    @php
        $typeLabels = ['hospital' => 'Hospital', 'private_clinic' => 'Private Clinic'];
        $formatDateTime = function ($value) {
            if (! $value) {
                return '';
            }

            try {
                return \Carbon\Carbon::parse((string) $value)->format('Y-m-d\TH:i');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        };
    @endphp

    <div class="sidebar" id="sidebar">
    <div class="sidebar-panel">
        <div class="sidebar-logo"><img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo"></div>
        <div class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-table-cells-large"></i><span>Dashboard</span></a>
            <a href="{{ route('admin.appointments') }}" class="{{ request()->routeIs('admin.appointments*') ? 'active' : '' }}"><i class="fa-regular fa-calendar"></i><span>Appointments</span></a>
            <a href="{{ route('admin.doctors') }}" class="{{ request()->routeIs('admin.doctors*') ? 'active' : '' }}"><i class="fa-solid fa-stethoscope"></i><span>Doctors</span></a>
            <a href="{{ route('admin.patients') }}" class="{{ request()->routeIs('admin.patients*') ? 'active' : '' }}"><i class="fa-solid fa-user-group"></i><span>Patients</span></a>
            <a href="{{ route('admin.staff') }}" class="{{ request()->routeIs('admin.staff*') ? 'active' : '' }}"><i class="fa-solid fa-user-tie"></i><span>Staff</span></a>
            <a href="{{ route('admin.rooms') }}" class="{{ request()->routeIs('admin.rooms*') ? 'active' : '' }}"><i class="fa-regular fa-hospital"></i><span>Rooms</span></a>
            <a href="{{ route('admin.departments') }}" class="{{ request()->routeIs('admin.departments*') ? 'active' : '' }}"><i class="fa-regular fa-building"></i><span>Departments</span></a>
        </div>
        <div class="logout-wrap"><a href="{{ url('/') }}" class="logout-btn">Log out</a></div>
    </div>
</div><div class="overlay" id="overlay"></div>

    <div class="main">
        <div class="topbar">
            <div class="topbar-left"><button class="menu-toggle" id="menuToggle" type="button"><i class="fa-solid fa-bars"></i></button><div class="brand-box"><img src="{{ asset('images/nuh-logo.png') }}" alt="NUH Logo"></div></div>
            <a href="{{ route('admin.settings') }}" class="icon-btn settings-link" title="Settings" aria-label="Settings"><i class="fa-solid fa-gear"></i></a><div class="admin-box"><div class="admin-info"><div class="name">Admin Robert</div><div class="role">Administrator</div></div><div class="avatar">R</div></div>
        </div>

        <div class="page-shell">
            <div class="page-header">
                <div>
                    <div class="page-kicker"><i class="fa-regular fa-calendar-days"></i><span>Schedule Management</span></div>
                    <h1>{{ $doctor->name }}</h1>
                    <p>{{ $doctor->department?->name_en ?? 'No department' }}. Control availability, weekly shifts, time off, and slot blocks used by the scheduling engine.</p>
                </div>
                <a href="{{ route('admin.doctors') }}" class="secondary-link"><i class="fa-solid fa-arrow-left"></i><span>Back to Doctors</span></a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->has('schedule_impact'))
                <div class="alert alert-warning">{{ $errors->first('schedule_impact') }}</div>
            @endif

            @if($errors->any())
                @php
                    $visibleErrors = collect($errors->getMessages())
                        ->except('schedule_impact')
                        ->flatten();
                @endphp
                @if($visibleErrors->isNotEmpty())
                    <div class="alert alert-danger"><ul class="mb-0">@foreach($visibleErrors as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
            @endif

            <div class="panel">
                <div class="panel-head">
                    <div><h2>Doctor Availability</h2><p>These settings control generated slot length, breaks, booking window, and minimum notice.</p></div>
                </div>
                @foreach($scheduleTypes as $scheduleType)
                    @php
                        $availability = $availabilities->get($scheduleType);
                    @endphp
                    <div class="availability-type-block {{ !$loop->last ? 'is-split' : '' }}">
                    <form action="{{ route('admin.doctors.schedule.availability', $doctor->id) }}" method="POST" class="mb-0">
                        @csrf
                        <input type="hidden" name="schedule_type" value="{{ $scheduleType }}">
                        <div class="field-grid">
                            <div class="field">
                                <label>Schedule Type</label>
                                <input type="text" class="form-control" value="{{ $typeLabels[$scheduleType] }}" disabled>
                            </div>
                            <div class="field">
                                <label>Duration (minutes)</label>
                                <input type="number" min="5" max="240" name="appointment_duration_minutes" class="form-control" value="{{ old('appointment_duration_minutes', $availability->appointment_duration_minutes ?? 30) }}" required>
                            </div>
                            <div class="field">
                                <label>Break (minutes)</label>
                                <input type="number" min="0" max="240" name="break_between_appointments_minutes" class="form-control" value="{{ old('break_between_appointments_minutes', $availability->break_between_appointments_minutes ?? 0) }}" required>
                            </div>
                            <div class="field">
                                <label>Booking Window (days)</label>
                                <input type="number" min="1" max="365" name="booking_window_days" class="form-control" value="{{ old('booking_window_days', $availability->booking_window_days ?? 30) }}" required>
                            </div>
                            <div class="field">
                                <label>Min Notice (minutes)</label>
                                <input type="number" min="0" max="10080" name="min_notice_minutes" class="form-control" value="{{ old('min_notice_minutes', $availability->min_notice_minutes ?? 0) }}" required>
                            </div>
                            <div class="field">
                                <label>Timezone</label>
                                <input type="text" name="timezone" class="form-control" value="{{ old('timezone', $availability->timezone ?? config('app.timezone', 'Africa/Cairo')) }}">
                            </div>
                            <div class="field">
                                <label>Active</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', $availability->is_active ?? true) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !old('is_active', $availability->is_active ?? true) ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="field">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn-primary-custom w-100"><i class="fa-solid fa-floppy-disk"></i><span>Save</span></button>
                            </div>
                        </div>
                        <label class="confirm-row">
                            <input type="checkbox" name="confirm_impact" value="1">
                            Save anyway if this change affects existing appointments
                        </label>
                    </form>
                    @php
                        $isHospital = $scheduleType === 'hospital';
                        $qMorning = $isHospital ? '09:00 → 13:00' : '10:00 → 14:00';
                        $qEvening = $isHospital ? '17:00 → 21:00' : '18:00 → 22:00';
                        $qFull = $isHospital ? '09:00–13:00 & 17:00–21:00' : '10:00–14:00 & 18:00–22:00';
                    @endphp
                    <div class="quick-shifts">
                        <span class="muted" style="font-size: 12px; font-weight: 700; margin-right: 4px;">Quick shifts ({{ $typeLabels[$scheduleType] }})</span>
                        <form method="POST" action="{{ route('admin.doctors.schedule.apply_template', $doctor->id) }}">@csrf
                            <input type="hidden" name="schedule_type" value="{{ $scheduleType }}">
                            <input type="hidden" name="template" value="morning">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Morning ({{ $qMorning }})</button>
                        </form>
                        <form method="POST" action="{{ route('admin.doctors.schedule.apply_template', $doctor->id) }}">@csrf
                            <input type="hidden" name="schedule_type" value="{{ $scheduleType }}">
                            <input type="hidden" name="template" value="evening">
                            <button type="submit" class="btn btn-sm btn-outline-warning">Evening ({{ $qEvening }})</button>
                        </form>
                        <form method="POST" action="{{ route('admin.doctors.schedule.apply_template', $doctor->id) }}">@csrf
                            <input type="hidden" name="schedule_type" value="{{ $scheduleType }}">
                            <input type="hidden" name="template" value="full">
                            <button type="submit" class="btn btn-sm btn-outline-success">Full day ({{ $qFull }})</button>
                        </form>
                    </div>
                    </div>
                @endforeach
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div><h2>Slot preview</h2><p>Bookable slots for one date (same rules as the booking system).</p></div>
                </div>
                <form action="{{ route('admin.doctors.schedule', $doctor->id) }}" method="GET" class="preview-form">
                    <div class="field"><label>Type</label><select name="preview_schedule_type" class="form-select">@foreach($scheduleTypes as $type)<option value="{{ $type }}" {{ $previewType === $type ? 'selected' : '' }}>{{ $typeLabels[$type] }}</option>@endforeach</select></div>
                    <div class="field"><label>Date</label><input type="date" name="preview_date" class="form-control" value="{{ $previewDate }}" required></div>
                    <div class="field"><label>&nbsp;</label><button type="submit" class="btn-soft w-100">Update</button></div>
                </form>
                @php
                    try {
                        $previewDateLabel = \Carbon\Carbon::parse((string) $previewDate)->format('D, M j, Y');
                    } catch (\Throwable $e) {
                        $previewDateLabel = (string) $previewDate;
                    }
                @endphp
                <p class="muted mb-2" style="font-size: 13px; font-weight: 600;">{{ $previewDateLabel }}</p>
                @if(is_array($previewSlots) && count($previewSlots) > 0)
                    <div class="slot-grid">
                        @foreach($previewSlots as $slot)
                            @php
                                $s = is_array($slot) ? $slot : [];
                                $st = isset($s['start_time']) ? (string) $s['start_time'] : '';
                                $en = isset($s['end_time']) ? (string) $s['end_time'] : '';
                            @endphp
                            <span class="slot-chip">{{ $st }} → {{ $en }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">No bookable slots for this date.</div>
                @endif
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div><h2>Weekly schedule</h2><p>Shifts define when this doctor can be booked. Add, edit, or remove shifts below.</p></div>
                </div>
                <p class="weekly-hint">Overview</p>
                <div class="weekly-overview">
                    @foreach($weeklySchedules ?? [] as $day => $shifts)
                        <div class="day-card">
                            <strong>{{ $day }}</strong>
                            <div>
                                @forelse($shifts as $shift)
                                    @php
                                        $st = substr((string) ($shift->start_time ?? ''), 0, 5);
                                        $en = substr((string) ($shift->end_time ?? ''), 0, 5);
                                        $locLabel = $typeLabels[$shift->location_type] ?? (string) ($shift->location_type ?? '');
                                    @endphp
                                    <span class="shift-chip">{{ $st }} → {{ $en }}<span class="chip-type">{{ $locLabel }}</span></span>
                                @empty
                                    <span class="muted" style="font-size: 13px;">No shifts</span>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="weekly-hint">Add shift</p>
                <form action="{{ route('admin.doctors.schedule.shifts.store', $doctor->id) }}" method="POST" class="inline-form mb-3">
                    @csrf
                    <div class="field"><label>Type</label><select name="schedule_type" class="form-select">@foreach($scheduleTypes as $type)<option value="{{ $type }}">{{ $typeLabels[$type] }}</option>@endforeach</select></div>
                    <div class="field"><label>Day</label><select name="day_of_week" class="form-select">@foreach($days as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
                    <div class="field"><label>Start</label><input type="time" name="start_time" class="form-control" required></div>
                    <div class="field"><label>End</label><input type="time" name="end_time" class="form-control" required></div>
                    <div class="field"><label>Active</label><select name="is_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
                    <div class="field"><label>&nbsp;</label><button type="submit" class="btn-primary-custom w-100">Add</button></div>
                    <label class="confirm-row"><input type="checkbox" name="confirm_impact" value="1"> Save anyway if this shift affects existing appointments</label>
                </form>
                <p class="weekly-hint">Edit or delete shifts</p>
                <div class="day-stack">
                    @foreach($days as $dayValue => $dayLabel)
                        @php $daySchedules = $schedulesByDay->get($dayValue, collect()); @endphp
                        <div class="day-panel">
                            <div class="day-head">
                                <span>{{ $dayLabel }}</span>
                                <span class="badge-soft badge-muted">{{ $daySchedules->count() }} shifts</span>
                            </div>
                            <div class="shift-list">
                                @forelse($daySchedules as $schedule)
                                    <div class="shift-card">
                                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap mb-2">
                                            <span class="time-range">{{ substr((string) $schedule->start_time, 0, 5) }} → {{ substr((string) $schedule->end_time, 0, 5) }}</span>
                                            <span class="badge-soft">{{ $typeLabels[$schedule->location_type] ?? $schedule->location_type }}</span>
                                        </div>
                                        <form action="{{ route('admin.doctors.schedule.shifts.update', [$doctor->id, $schedule->id]) }}" method="POST" class="inline-form">
                                            @csrf @method('PUT')
                                            <div class="field"><select name="schedule_type" class="form-select">@foreach($scheduleTypes as $type)<option value="{{ $type }}" {{ $schedule->location_type === $type ? 'selected' : '' }}>{{ $typeLabels[$type] }}</option>@endforeach</select></div>
                                            <div class="field"><select name="day_of_week" class="form-select">@foreach($days as $value => $label)<option value="{{ $value }}" {{ (int) $schedule->day_of_week === (int) $value ? 'selected' : '' }}>{{ $label }}</option>@endforeach</select></div>
                                            <div class="field"><input type="time" name="start_time" class="form-control" value="{{ substr((string) $schedule->start_time, 0, 5) }}" required></div>
                                            <div class="field"><input type="time" name="end_time" class="form-control" value="{{ substr((string) $schedule->end_time, 0, 5) }}" required></div>
                                            <div class="field"><select name="is_active" class="form-select"><option value="1" {{ $schedule->is_active ? 'selected' : '' }}>Active</option><option value="0" {{ !$schedule->is_active ? 'selected' : '' }}>Inactive</option></select></div>
                                            <div class="field"><button type="submit" class="btn-soft w-100">Save</button></div>
                                            <label class="confirm-row"><input type="checkbox" name="confirm_impact" value="1"> Save anyway if affected</label>
                                        </form>
                                        <form action="{{ route('admin.doctors.schedule.shifts.delete', [$doctor->id, $schedule->id]) }}" method="POST" class="mt-2">
                                            @csrf @method('DELETE')
                                            <label class="confirm-row"><input type="checkbox" name="confirm_impact" value="1"> Delete anyway if affected</label>
                                            <button type="submit" class="btn-danger-soft mt-2" onclick="return confirm('Delete this shift?')"><i class="fa-regular fa-trash-can"></i><span>Delete shift</span></button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="empty-state">No shifts for {{ $dayLabel }}.</div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div><h2>Time Off</h2><p>Add vacation or leave ranges. Leave schedule type empty to block all schedule types.</p></div>
                </div>
                <form action="{{ route('admin.doctors.schedule.time-off.store', $doctor->id) }}" method="POST" class="inline-form time-off mb-3">
                    @csrf
                    <div class="field"><label>Type</label><select name="schedule_type" class="form-select"><option value="">All Types</option>@foreach($scheduleTypes as $type)<option value="{{ $type }}">{{ $typeLabels[$type] }}</option>@endforeach</select></div>
                    <div class="field"><label>Starts</label><input type="datetime-local" name="starts_at" class="form-control" required></div>
                    <div class="field"><label>Ends</label><input type="datetime-local" name="ends_at" class="form-control" required></div>
                    <div class="field"><label>Reason</label><input type="text" name="reason" class="form-control" placeholder="Vacation, leave, conference"></div>
                    <div class="field"><label>&nbsp;</label><button type="submit" class="btn-primary-custom w-100">Add</button></div>
                    <label class="confirm-row"><input type="checkbox" name="confirm_impact" value="1"> Save anyway if this leave affects existing appointments</label>
                </form>
                <div class="table-wrap">
                    <table class="table custom-table align-middle">
                        <thead><tr><th>Type</th><th>Starts</th><th>Ends</th><th>Reason</th><th>Action</th></tr></thead>
                        <tbody>
                            @forelse($timeOff as $item)
                                <tr>
                                    <td><span class="badge-soft">{{ $item->schedule_type ? $typeLabels[$item->schedule_type] : 'All Types' }}</span></td>
                                    <td>{{ $item->starts_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $item->ends_at?->format('Y-m-d H:i') }}</td>
                                    <td>{{ $item->reason ?: '-' }}</td>
                                    <td><form action="{{ route('admin.doctors.schedule.time-off.delete', [$doctor->id, $item->id]) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="btn-danger-soft" onclick="return confirm('Delete this time off?')"><i class="fa-regular fa-trash-can"></i></button></form></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="empty-state">No time off configured.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head">
                    <div><h2>Blocked Times</h2><p>Add manual blocks and review automatic blocks created by appointments or system processes.</p></div>
                </div>
                <form action="{{ route('admin.doctors.schedule.blocked-times.store', $doctor->id) }}" method="POST" class="inline-form blocked mb-3">
                    @csrf
                    <div class="field"><label>Type</label><select name="schedule_type" class="form-select">@foreach($scheduleTypes as $type)<option value="{{ $type }}">{{ $typeLabels[$type] }}</option>@endforeach</select></div>
                    <div class="field"><label>Starts</label><input type="datetime-local" name="starts_at" class="form-control" required></div>
                    <div class="field"><label>Ends</label><input type="datetime-local" name="ends_at" class="form-control" required></div>
                    <div class="field"><label>Reason</label><input type="text" name="reason" class="form-control" placeholder="Manual block"></div>
                    <div class="field"><label>Active</label><select name="is_active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
                    <div class="field"><label>&nbsp;</label><button type="submit" class="btn-primary-custom w-100">Add</button></div>
                    <label class="confirm-row"><input type="checkbox" name="confirm_impact" value="1"> Save anyway if this block affects existing appointments</label>
                </form>
                <div class="table-wrap">
                    <table class="table custom-table align-middle">
                        <thead><tr><th>Type</th><th>Starts</th><th>Ends</th><th>Reason</th><th>Source</th><th>Status</th><th>Actions</th></tr></thead>
                        <tbody>
                            @forelse($blockedTimes as $block)
                                <tr>
                                    @if($block->source === 'manual')
                                        <td colspan="6">
                                            <form action="{{ route('admin.doctors.schedule.blocked-times.update', [$doctor->id, $block->id]) }}" method="POST" class="inline-form blocked">
                                                @csrf @method('PUT')
                                                <div class="field"><select name="schedule_type" class="form-select">@foreach($scheduleTypes as $type)<option value="{{ $type }}" {{ $block->schedule_type === $type ? 'selected' : '' }}>{{ $typeLabels[$type] }}</option>@endforeach</select></div>
                                                <div class="field"><input type="datetime-local" name="starts_at" class="form-control" value="{{ $formatDateTime($block->starts_at) }}" required></div>
                                                <div class="field"><input type="datetime-local" name="ends_at" class="form-control" value="{{ $formatDateTime($block->ends_at) }}" required></div>
                                                <div class="field"><input type="text" name="reason" class="form-control" value="{{ $block->reason }}"></div>
                                                <div class="field"><select name="is_active" class="form-select"><option value="1" {{ $block->is_active ? 'selected' : '' }}>Active</option><option value="0" {{ !$block->is_active ? 'selected' : '' }}>Inactive</option></select></div>
                                                <div class="field"><button type="submit" class="btn-soft w-100">Save</button></div>
                                                <label class="confirm-row"><input type="checkbox" name="confirm_impact" value="1"> Save anyway if affected</label>
                                            </form>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.doctors.schedule.blocked-times.delete', [$doctor->id, $block->id]) }}" method="POST">@csrf @method('DELETE')<button type="submit" class="btn-danger-soft" onclick="return confirm('Delete this manual block?')"><i class="fa-regular fa-trash-can"></i></button></form>
                                        </td>
                                    @else
                                        <td><span class="badge-soft">{{ $typeLabels[$block->schedule_type] ?? $block->schedule_type }}</span></td>
                                        <td>{{ $block->starts_at?->format('Y-m-d H:i') }}</td>
                                        <td>{{ $block->ends_at?->format('Y-m-d H:i') }}</td>
                                        <td>{{ $block->reason ?: '-' }}</td>
                                        <td><span class="badge-soft badge-warning-soft">{{ ucfirst($block->source) }}</span></td>
                                        <td><span class="badge-soft {{ $block->is_active ? '' : 'badge-muted' }}">{{ $block->is_active ? 'Active' : 'Inactive' }}</span></td>
                                        <td class="muted">Read only</td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty-state">No blocked times configured.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    menuToggle.addEventListener('click', function() { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); });
    overlay.addEventListener('click', function() { sidebar.classList.remove('open'); overlay.classList.remove('show'); });
    </script>
</body>

</html>
