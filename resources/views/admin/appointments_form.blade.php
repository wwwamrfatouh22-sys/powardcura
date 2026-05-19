<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>NUH Admin - {{ $pageTitle }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: Inter, Arial, sans-serif; }
    :root {
        --primary: #114a9f; --primary-dark: #0b3f8c; --text-dark: #1f2937; --text-muted: #6b7280;
        --danger: #ff3b3b; --surface: rgba(255,255,255,.96); --surface-soft: rgba(241,246,253,.9);
        --border-soft: rgba(148,163,184,.2); --shadow-soft: 0 12px 28px rgba(34,52,84,.16); --shadow-card: 0 24px 50px rgba(34,52,84,.16);
    }
    body {
        min-height: 100vh; color: var(--text-dark); overflow-x: hidden;
        background: radial-gradient(circle at top left, rgba(84,170,255,.28), transparent 32%), radial-gradient(circle at 85% 20%, rgba(17,74,159,.18), transparent 25%), linear-gradient(180deg, #eef4fb 0%, #f8fafc 42%, #edf3f9 100%);
    }
    a { text-decoration: none; color: inherit; }
    .sidebar { position: fixed; top: 18px; left: 18px; width: 280px; height: calc(100vh - 36px); z-index: 1100; transform: translateX(-120%); transition: transform .35s cubic-bezier(.4, 0, .2, 1); }
    .sidebar.open { transform: translateX(0); }
    .sidebar-panel { height: 100%; background: rgba(247,247,247,.97); border-radius: 34px; box-shadow: 0 18px 36px rgba(0,0,0,.14); padding: 28px 18px 18px; display: flex; flex-direction: column; backdrop-filter: blur(2px); }
    .sidebar-logo { display: flex; justify-content: center; align-items: center; min-height: 86px; margin-bottom: 24px; }
    .sidebar-logo img { max-width: 145px; height: auto; object-fit: contain; }
    .sidebar-nav { margin-top: 8px; display: flex; flex-direction: column; gap: 8px; }
    .sidebar-nav a { display: flex; align-items: center; gap: 14px; color: #2f3947; padding: 16px 18px; border-radius: 18px; font-size: 16px; font-weight: 500; transition: .22s ease; }
    .sidebar-nav a:hover { background: #eceff3; color: #2e3844; transform: translateX(4px); }
    .sidebar-nav a.active, .sidebar-nav a.active:hover { background: var(--primary); color: #fff; box-shadow: 0 10px 22px rgba(17,74,159,.26); transform: none; }
    .sidebar-nav a i { width: 24px; text-align: center; font-size: 20px; }
    .logout-wrap { margin-top: auto; padding-top: 18px; }
    .logout-btn { display: flex; width: 100%; border: none; background: var(--danger); color: #fff; padding: 16px 18px; border-radius: 14px; font-size: 18px; font-weight: 700; align-items: center; justify-content: center; transition: .25s ease; }
    .logout-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255,59,63,.3); }
    .overlay { position: fixed; inset: 0; background: rgba(0,0,0,.22); opacity: 0; visibility: hidden; transition: .3s ease; z-index: 1000; }
    .overlay.show { opacity: 1; visibility: visible; }
    .main { min-height: 100vh; padding: 22px 28px 52px; }
    .topbar { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 30px; }
    .topbar-left { display: flex; align-items: flex-start; gap: 16px; }
    .brand-box img { max-width: 120px; height: auto; object-fit: contain; }
    .menu-toggle { width: 46px; height: 46px; border: none; background: transparent; font-size: 28px; color: #4b5563; cursor: pointer; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
    .menu-toggle:hover { background: rgba(255,255,255,.45); }
    .admin-box { display: flex; align-items: center; gap: 10px; }
    .admin-info { text-align: right; line-height: 1.2; }
    .admin-info .name { font-size: 15px; color: #1f2937; font-weight: 600; }
    .admin-info .role { font-size: 14px; color: #6b7280; }
    .avatar { width: 46px; height: 46px; border-radius: 50%; background: #0b4aa7; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 18px; box-shadow: 0 10px 18px rgba(17,74,159,.25); }
    .page-shell { max-width: 1200px; margin: 0 auto; }
    .page-header { display: flex; justify-content: space-between; gap: 18px; align-items: flex-start; flex-wrap: wrap; margin-bottom: 22px; }
    .page-kicker { display: inline-flex; align-items: center; gap: 8px; padding: 8px 14px; border-radius: 999px; background: rgba(17,74,159,.09); color: var(--primary); font-size: 13px; font-weight: 700; margin-bottom: 14px; }
    .page-header h1 { font-size: 32px; font-weight: 700; margin: 0 0 10px; }
    .page-header p { max-width: 720px; margin: 0; color: var(--text-muted); font-size: 15px; line-height: 1.6; }
    .secondary-link { display: inline-flex; align-items: center; gap: 10px; padding: 12px 18px; border-radius: 16px; background: rgba(255,255,255,.82); border: 1px solid var(--border-soft); box-shadow: var(--shadow-soft); color: var(--text-dark); font-weight: 600; }
    .content-grid { display: grid; grid-template-columns: minmax(0, 1.9fr) minmax(300px, .95fr); gap: 22px; align-items: start; }
    .panel, .side-panel { background: var(--surface); border: 1px solid var(--border-soft); border-radius: 28px; box-shadow: var(--shadow-card); }
    .panel { padding: 28px; }
    .panel-head { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; align-items: flex-start; margin-bottom: 24px; }
    .panel-head h2 { margin: 0 0 8px; font-size: 24px; font-weight: 700; }
    .panel-head p { margin: 0; color: var(--text-muted); }
    .status-pill { display: inline-flex; align-items: center; justify-content: center; min-width: 110px; padding: 10px 14px; border-radius: 999px; background: rgba(17,74,159,.09); color: var(--primary); font-weight: 700; font-size: 13px; }
    .form-section + .form-section { margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(226,232,240,.9); }
    .section-title { margin-bottom: 16px; }
    .section-title h3 { margin: 0 0 6px; font-size: 17px; font-weight: 700; }
    .section-title p { margin: 0; color: var(--text-muted); font-size: 14px; }
    .field-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
    .field-span-2 { grid-column: span 2; }
    .field label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #334155; }
    .form-control, .form-select { min-height: 50px; border-radius: 16px; border: 1px solid #d9e2ec; background: #f8fbff; padding: 12px 14px; color: var(--text-dark); box-shadow: none; }
    textarea.form-control { min-height: 120px; resize: vertical; }
    .form-control:focus, .form-select:focus { border-color: rgba(17,74,159,.45); box-shadow: 0 0 0 4px rgba(17,74,159,.10); background: #fff; }
    .invalid-feedback { display: block; }
    .form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; flex-wrap: wrap; }
    .btn-soft, .btn-primary-custom { min-width: 170px; min-height: 50px; border-radius: 16px; font-weight: 700; border: none; display: inline-flex; align-items: center; justify-content: center; gap: 10px; }
    .btn-soft { background: #eef2f7; color: #334155; }
    .btn-primary-custom { background: var(--primary); color: #fff; box-shadow: 0 14px 28px rgba(17,74,159,.18); }
    .side-panel { padding: 22px; background: linear-gradient(180deg, rgba(255,255,255,.98) 0%, rgba(242,247,255,.96) 100%); }
    .info-stack { display: grid; gap: 14px; }
    .info-card { padding: 16px 18px; border-radius: 20px; background: var(--surface-soft); border: 1px solid rgba(148,163,184,.18); }
    .info-card h3 { margin: 0 0 6px; font-size: 15px; font-weight: 700; }
    .info-card p, .info-card ul { margin: 0; color: var(--text-muted); font-size: 14px; line-height: 1.6; }
    .info-card ul { padding-left: 18px; }
    .clinic-grid { display: grid; gap: 12px; margin-top: 14px; }
    .clinic-item { padding: 14px 16px; background: rgba(255,255,255,.74); border-radius: 16px; border: 1px solid rgba(148,163,184,.16); }
    .clinic-item strong { display: block; font-size: 12px; text-transform: uppercase; color: #64748b; margin-bottom: 4px; }
    .clinic-item span { color: var(--text-dark); font-size: 14px; }
    .alert { border-radius: 18px; border: none; box-shadow: var(--shadow-soft); }
    @media (max-width: 992px) { .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 768px) { .main { padding: 18px 14px 34px; } .panel, .side-panel { border-radius: 22px; } .panel { padding: 22px 18px; } .page-header h1 { font-size: 26px; } .field-grid { grid-template-columns: 1fr; } .field-span-2 { grid-column: auto; } .form-actions > * { width: 100%; } }
        .settings-link { background: var(--primary); color: #fff; }
    .settings-link:hover { background: var(--primary-dark); color: #fff; }
    </style>
</head>

<body>
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
                <div><div class="page-kicker"><i class="fa-regular fa-calendar-check"></i><span>Appointments</span></div><h1>{{ $pageTitle }}</h1><p>{{ $pageDescription }}</p></div>
                <a href="{{ route('admin.appointments') }}" class="secondary-link"><i class="fa-solid fa-arrow-left"></i><span>Back to Appointments</span></a>
            </div>
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @php
                $selectedType = old('type', $appointment->type ?: 'hospital');
                $selectedStatus = old('status', $appointment->status ?: 'Pending');
            @endphp
            <div class="content-grid">
                <div class="panel">
                    <div class="panel-head"><div><h2>{{ $isEdit ? 'Appointment Details' : 'New Appointment Details' }}</h2><p>Use the same structured admin workflow for creating and updating appointments.</p></div><div class="status-pill">{{ $isEdit ? 'Edit Mode' : 'Create Mode' }}</div></div>
                    <form action="{{ $formAction }}" method="POST">
                        @csrf
                        @if($formMethod !== 'POST') @method($formMethod) @endif
                        <div class="form-section">
                            <div class="section-title"><h3>Patient Information</h3><p>Link an existing patient or enter contact details manually.</p></div>
                            <div class="field-grid">
                                <div class="field field-span-2">
                                    <label for="patient_id">Patient (Optional)</label>
                                    <select id="patient_id" name="patient_id" class="form-select @error('patient_id') is-invalid @enderror">
                                        <option value="">No linked patient</option>
                                        @foreach($patients as $patient)
                                            <option value="{{ $patient->id }}" {{ (string) old('patient_id', $appointment->patient_id) === (string) $patient->id ? 'selected' : '' }}>{{ $patient->full_name }} ({{ $patient->national_id }})</option>
                                        @endforeach
                                    </select>
                                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="field"><label for="first_name">First Name</label><input id="first_name" type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $appointment->first_name) }}">@error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field"><label for="last_name">Last Name</label><input id="last_name" type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $appointment->last_name) }}">@error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $appointment->email) }}">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field"><label for="phone">Phone</label><input id="phone" type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $appointment->phone) }}" required>@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="section-title"><h3>Appointment Scheduling</h3><p>Select the doctor, booking type, and timing details.</p></div>
                            <div class="field-grid">
                                <div class="field">
                                    <label for="doctor_id">Doctor</label>
                                    <select id="doctor_id" name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}" {{ (string) old('doctor_id', $appointment->doctor_id) === (string) $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="field"><label for="type">Booking Type</label><select id="type" name="type" class="form-select @error('type') is-invalid @enderror"><option value="hospital" {{ $selectedType === 'hospital' ? 'selected' : '' }}>Hospital Visit</option><option value="private" {{ $selectedType === 'private' ? 'selected' : '' }}>Private Clinic</option></select>@error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field"><label for="date">Date</label><input id="date" type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('Y-m-d') : '') }}" required>@error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field"><label for="time">Time</label><input id="time" type="time" name="time" class="form-control @error('time') is-invalid @enderror" value="{{ old('time', $appointment->time) }}" required>@error('time')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field"><label for="status">Status</label><select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required><option value="Pending" {{ $selectedStatus === 'Pending' ? 'selected' : '' }}>Pending</option><option value="Confirmed" {{ $selectedStatus === 'Confirmed' ? 'selected' : '' }}>Confirmed</option><option value="Completed" {{ $selectedStatus === 'Completed' ? 'selected' : '' }}>Completed</option><option value="Canceled" {{ $selectedStatus === 'Canceled' ? 'selected' : '' }}>Canceled</option></select>@error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field"><label for="payment_method">Payment Method</label><select id="payment_method" name="payment_method" class="form-select @error('payment_method') is-invalid @enderror"><option value="">Optional</option>@foreach(['fawry_card' => 'Fawry Card', 'fawry_wallet' => 'Fawry Wallet', 'instapay' => 'Instapay', 'pay_at_hospital' => 'Pay at Hospital'] as $val => $lab)<option value="{{ $val }}" @selected(old('payment_method', $appointment->payment_method) === $val)>{{ $lab }}</option>@endforeach</select>@error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field"><label for="payment_status">Payment Status</label><select id="payment_status" name="payment_status" class="form-select @error('payment_status') is-invalid @enderror">
                                    @php $ps = old('payment_status', $appointment->payment_status ?? 'pending'); @endphp
                                    @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'paid' => 'Paid', 'failed' => 'Failed', 'canceled' => 'Canceled'] as $val => $lab)
                                        <option value="{{ $val }}" {{ (string) $ps === (string) $val ? 'selected' : '' }}>{{ $lab }}</option>
                                    @endforeach
                                </select>@error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                                <div class="field field-span-2"><label for="reason">Reason</label><textarea id="reason" name="reason" class="form-control @error('reason') is-invalid @enderror">{{ old('reason', $appointment->reason) }}</textarea>@error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                            </div>
                        </div>
                        <div class="form-actions"><a href="{{ route('admin.appointments') }}" class="btn-soft">Cancel</a><button type="submit" class="btn-primary-custom"><i class="fa-solid fa-floppy-disk"></i><span>{{ $submitLabel }}</span></button></div>
                    </form>
                </div>
                <div class="side-panel"><div class="info-stack">
                    <div class="info-card"><h3>Workflow Notes</h3><ul><li>Existing create and edit validation rules are unchanged.</li><li>Department and clinic snapshot values are still filled automatically on submit.</li><li>This dedicated page replaces the old modal flow for cleaner administration.</li></ul></div>
                    <div class="info-card"><h3>Current Booking Type</h3><p>{{ $selectedType === 'private' ? 'Private clinic details are captured automatically from the selected doctor configuration.' : 'Hospital visit details will follow the selected doctor and department.' }}</p>
                        @if($selectedType === 'private' && $isEdit)
                            <div class="clinic-grid">
                                <div class="clinic-item"><strong>Clinic Name</strong><span>{{ $appointment->clinic_name ?: 'Private Clinic' }}</span></div>
                                <div class="clinic-item"><strong>Clinic Phone</strong><span>{{ $appointment->clinic_phone ?: 'N/A' }}</span></div>
                                <div class="clinic-item"><strong>Clinic Address</strong><span>{{ $appointment->clinic_address ?: 'N/A' }}</span></div>
                            </div>
                        @endif
                    </div>
                    <div class="info-card"><h3>Design Goal</h3><p>This screen matches the admin dashboard tone with a cleaner background, better spacing, and a readable form surface shared by both create and edit.</p></div>
                </div></div>
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
