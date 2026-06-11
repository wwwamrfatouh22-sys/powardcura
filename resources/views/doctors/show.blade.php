<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $doctor->name }} | {{ __('ui.common.doctor') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="{{ app()->isLocale('ar') ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0f4ba5;
            --primary-dark: #0b3f8b;
            --surface: rgba(248, 248, 248, 0.96);
            --text: #243247;
            --muted: #5f6f84;
            --shadow: 0 16px 34px rgba(33, 61, 102, 0.16);
            --line: #d4e1f4;
        }

        body {
            margin: 0;
            font-family: {{ app()->isLocale('ar') ? "'Cairo','Segoe UI',Arial,sans-serif" : "'Inter','Segoe UI',Arial,sans-serif" }};
            color: var(--text);
            background: radial-gradient(circle at 15% 90%, rgba(20, 133, 255, .82) 0%, rgba(20, 133, 255, 0) 42%),
                        linear-gradient(90deg, #9ecdf3 0%, #edf6ff 40%, #f4f4f6 100%);
            min-height: 100vh;
        }

        .page-shell { max-width: 1200px; margin: 0 auto; padding: 28px 16px 46px; }

        .profile-card {
            background: var(--surface);
            border-radius: 30px;
            box-shadow: var(--shadow);
            padding: 24px;
            margin-bottom: 24px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: minmax(260px, 320px) 1fr;
            gap: 24px;
            align-items: stretch;
        }

        .doctor-image {
            width: 100%;
            height: 320px;
            object-fit: cover;
            border-radius: 24px;
            box-shadow: 0 12px 24px rgba(11, 63, 139, 0.2);
            background: #d8e7fb;
        }

        .doctor-name { margin: 0; font-size: clamp(24px, 3vw, 34px); font-weight: 800; }
        .doctor-spec { margin: 8px 0 14px; color: var(--muted); font-size: 16px; }

        .doctor-tags { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }

        .tag {
            border-radius: 999px;
            background: #eaf2ff;
            color: #29456b;
            font-size: 13px;
            font-weight: 700;
            padding: 7px 12px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .doctor-bio {
            margin: 0;
            color: #4f6076;
            line-height: 1.8;
            font-size: 15px;
            max-width: 700px;
        }

        .booking-card {
            background: var(--surface);
            border-radius: 30px;
            box-shadow: var(--shadow);
            padding: 24px;
        }

        .section-title { margin: 0 0 14px; font-size: 22px; font-weight: 800; }
        .section-sub { color: var(--muted); margin-bottom: 18px; font-size: 14px; }

        .booking-grid {
            display: grid;
            grid-template-columns: minmax(280px, 360px) 1fr;
            gap: 18px;
            align-items: start;
            margin-bottom: 22px;
        }

        .booking-panel, .location-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 10px 22px rgba(17, 74, 158, .06);
        }

        .control-label {
            display: block;
            font-size: 14px;
            font-weight: 800;
            color: #32445c;
            margin-bottom: 8px;
        }

        .type-select {
            border-radius: 14px;
            border: 1.5px solid #c8d8ef;
            min-height: 52px;
            width: 100%;
        }

        .type-note {
            margin-top: 12px;
            font-size: 13px;
            color: var(--muted);
        }

        .location-card {
            background: linear-gradient(180deg, #ffffff 0%, #f5f9ff 100%);
        }

        .location-badge {
            display: inline-flex;
            border-radius: 999px;
            background: #eaf2ff;
            color: #204573;
            font-size: 12px;
            font-weight: 800;
            padding: 6px 10px;
            margin-bottom: 10px;
        }

        .location-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
        }

        .location-sub {
            color: var(--muted);
            margin: 4px 0 14px;
            font-size: 13px;
        }

        .detail-list {
            display: grid;
            gap: 10px;
        }

        .detail-item {
            border: 1px solid #e3ebf7;
            border-radius: 14px;
            padding: 12px;
            background: rgba(255, 255, 255, .9);
        }

        .detail-item small {
            display: block;
            color: var(--muted);
            margin-bottom: 3px;
        }

        .detail-item span {
            color: #2b3f59;
        }

        .date-scroll {
            display: grid;
            grid-template-columns: repeat(5, minmax(130px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .date-pill {
            border: 1.5px solid #d4e1f4;
            border-radius: 14px;
            background: #fff;
            padding: 10px 12px;
            text-align: left;
            transition: .22s ease;
            cursor: pointer;
        }

        .date-pill strong { display: block; font-size: 15px; }
        .date-pill span { font-size: 12px; color: var(--muted); }
        .date-pill small { display: block; margin-top: 5px; font-size: 12px; font-weight: 800; color: var(--primary); }
        .date-pill.active,
        .date-pill:hover { border-color: var(--primary); background: #eaf2ff; }

        .date-pill.disabled,
        .date-pill.disabled:hover {
            background: #f3f5f8;
            border-color: #dbe2ec;
            color: #8a97a8;
            cursor: not-allowed;
            transform: none;
        }

        .time-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(120px, 1fr));
            gap: 10px;
        }

        .slot-btn {
            border: 1.5px solid transparent;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            padding: 11px 12px;
            text-align: center;
            font-weight: 700;
            text-decoration: none;
            transition: .22s ease;
            display: inline-flex;
            flex-direction: column;
            gap: 3px;
            justify-content: center;
            align-items: center;
            min-height: 62px;
        }

        .slot-btn:hover { background: var(--primary-dark); color: #fff; transform: translateY(-2px); }
        .slot-btn.selected-slot {
            background: #082f6c;
            border-color: #b9d7ff;
            box-shadow: 0 12px 24px rgba(8, 47, 108, .24);
            transform: translateY(-2px);
        }
        .slot-btn small { font-size: 11px; font-weight: 800; opacity: .9; }

        .slot-btn.disabled-slot,
        .slot-btn.disabled-slot:hover {
            background: #cfd6e2;
            color: #6f7f96;
            cursor: not-allowed;
            pointer-events: none;
            transform: none;
            box-shadow: none;
            text-decoration: none;
        }

        .booking-toolbar {
            display: grid;
            grid-template-columns: minmax(220px, 320px) 1fr;
            gap: 14px;
            margin-bottom: 18px;
            align-items: end;
        }

        .status-strip {
            border-radius: 14px;
            padding: 12px 14px;
            background: #eef6ff;
            color: #23476e;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            min-height: 52px;
        }

        .status-strip.error { background: #ffecec; color: #9f1f1f; }
        .status-strip.loading { background: #f5f8fc; color: #5d7088; }

        .continue-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .selected-summary {
            color: var(--muted);
            font-weight: 700;
        }

        .continue-booking {
            border: none;
            border-radius: 14px;
            min-height: 50px;
            padding: 0 22px;
            background: var(--primary);
            color: #fff;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .continue-booking.disabled {
            pointer-events: none;
            background: #cfd6e2;
            color: #6f7f96;
        }

        .empty-state {
            border: 1.5px dashed #c8d8ef;
            border-radius: 18px;
            padding: 18px;
            text-align: center;
            background: #f9fbff;
            color: #5d7088;
            font-weight: 600;
            grid-column: 1 / -1;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: var(--primary-dark);
            font-weight: 700;
            margin-bottom: 12px;
        }

        @media (max-width: 992px) {
            .profile-grid,
            .booking-grid { grid-template-columns: 1fr; }
            .doctor-image { height: 280px; }
            .booking-toolbar { grid-template-columns: 1fr; }
            .date-scroll { grid-template-columns: repeat(3, minmax(120px, 1fr)); }
            .time-grid { grid-template-columns: repeat(3, minmax(120px, 1fr)); }
        }

        @media (max-width: 640px) {
            .date-scroll { grid-template-columns: repeat(2, minmax(120px, 1fr)); }
            .time-grid { grid-template-columns: repeat(2, minmax(120px, 1fr)); }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <a href="{{ route('specialties.doctors', $doctor->department_id, false) }}" class="back-link"><i class="bi bi-arrow-left"></i> Back To Specialist List</a>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <section class="profile-card">
        <div class="profile-grid">
            <div>
                <img
                    src="{{ $doctor->image ? '/images/' . ltrim($doctor->image, '/') : '/images/logo_Image.png' }}"
                    alt="{{ $doctor->name }}"
                    class="doctor-image"
                    onerror="this.onerror=null;this.src='/images/logo_Image.png';"
                >
            </div>
            <div>
                <h1 class="doctor-name">{{ $doctor->name }}</h1>
                <p class="doctor-spec">{{ $doctor->specialization ?: 'General Specialist' }}</p>

                <div class="doctor-tags">
                    <span class="tag"><i class="bi bi-star-fill"></i> {{ number_format((float) ($doctor->rating ?? 0), 1) }}/5 Rating</span>
                    <span class="tag"><i class="bi bi-briefcase-fill"></i> {{ $doctor->experience ?? 0 }} Years Experience</span>
                    <span class="tag"><i class="bi bi-hospital"></i> {{ optional($doctor->department)->name_en ?? 'Hospital Department' }}</span>
                </div>

                <p class="doctor-bio">
                    Book your consultation with a qualified specialist. Select a preferred date and available time slot below to continue secure appointment booking.
                </p>
            </div>
        </div>
    </section>

    <section class="booking-card">
        <h2 class="section-title">{{ __('ui.doctor.choose_date_time') }}</h2>
        <p class="section-sub">{{ __('ui.doctor.choose_date_time_sub') }}</p>

        <div class="booking-toolbar">
            <div>
                <label class="control-label" for="bookingType">Booking location</label>
                <select id="bookingType" class="form-select type-select">
                    <option value="hospital" {{ $selectedType === 'hospital' ? 'selected' : '' }}>{{ __('ui.common.hospital') }}</option>
                    <option value="private" {{ $selectedType === 'private' ? 'selected' : '' }} {{ \App\Support\PrivateClinicBookingSupport::hasPrivateClinic($doctor) ? '' : 'disabled' }}>{{ __('ui.common.private_clinic') }}</option>
                </select>
            </div>
            <div class="status-strip" id="slotStatus"><i class="bi bi-calendar-check"></i><span>Select a date and available slot.</span></div>
        </div>

        <div class="date-scroll" id="dateList"></div>
        <div class="time-grid" id="timeGrid"></div>

        <div class="continue-row">
            <div class="selected-summary" id="selectedSummary">No slot selected yet.</div>
            <a href="#" class="continue-booking disabled" id="continueBooking"><span>Continue</span><i class="bi bi-arrow-right"></i></a>
        </div>
    </section>
</div>

<script>
    const dateList = document.getElementById('dateList');
    const timeGrid = document.getElementById('timeGrid');
    const bookingTypeSelect = document.getElementById('bookingType');
    const slotStatus = document.getElementById('slotStatus');
    const selectedSummary = document.getElementById('selectedSummary');
    const continueBooking = document.getElementById('continueBooking');

    let slotDays = @json($slotDays);
    const currentLocale = @json(app()->isLocale('ar') ? 'ar-EG' : 'en-US');
    const doctorId = @json((int) $doctor->id);
    let selectedDate = @json($selectedDate ?? now()->toDateString());
    let selectedType = @json($selectedType ?? 'hospital');
    let selectedSlot = null;

    function setStatus(message, mode = 'info', icon = 'bi-calendar-check') {
        slotStatus.className = `status-strip ${mode === 'info' ? '' : mode}`.trim();
        slotStatus.innerHTML = `<i class="bi ${icon}"></i><span>${message}</span>`;
    }

    function renderDates() {
        dateList.innerHTML = '';
        slotDays.forEach(day => {
            const btn = document.createElement('button');
            btn.type = 'button';
            const disabled = !day.doctor_available;
            btn.className = `date-pill ${day.date === selectedDate ? 'active' : ''} ${disabled ? 'disabled' : ''}`.trim();
            btn.disabled = false;
            btn.innerHTML = `<strong>${day.label}</strong><span>${day.date}</span><small>${day.available_count} available</small>`;
            btn.addEventListener('click', () => {
                selectedDate = day.date;
                selectedSlot = null;
                updateContinue();
                renderDates();
                renderSlots();
            });

            dateList.appendChild(btn);
        });
    }

    function renderSlots() {
        timeGrid.innerHTML = '';
        const day = slotDays.find(item => item.date === selectedDate);
        const slots = day ? day.slots : [];

        if (!slots.length) {
            timeGrid.innerHTML = '<div class="empty-state">Doctor is not available for this date.</div>';
            setStatus('Doctor is not available', 'error', 'bi-exclamation-circle');
            return;
        }

        slots.forEach(slot => {
            if (!slot.available) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'slot-btn disabled-slot';
                button.disabled = true;
                button.innerHTML = `<span>${slot.time}</span><small>${slot.label || 'Unavailable'}</small>`;
                timeGrid.appendChild(button);
            } else {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `slot-btn ${selectedSlot === slot.time ? 'selected-slot' : ''}`.trim();
                button.innerHTML = `<span>${slot.time}</span><small>Available</small>`;
                button.addEventListener('click', () => verifyAndSelectSlot(slot.time));
                timeGrid.appendChild(button);
            }
        });

        if (day && day.doctor_available) {
            setStatus(`${day.available_count} slots available on ${day.date}`, 'info', 'bi-calendar-check');
        }
    }

    async function loadSlotsForDate(date = selectedDate) {
        setStatus('Loading available slots...', 'loading', 'bi-arrow-repeat');
        try {
            const fetchUrl = `/doctors/${doctorId}/booked-slots?date=${encodeURIComponent(date)}&type=${encodeURIComponent(selectedType)}`;
            console.debug('Booking slots fetch URL:', fetchUrl);

            const response = await fetch(fetchUrl, {
                headers: { 'Accept': 'application/json' },
            });
            if (!response.ok) throw new Error('Failed to load slots.');

            const data = await response.json();
            const updatedDay = {
                date: data.date,
                label: new Date(`${data.date}T00:00:00`).toLocaleDateString(currentLocale, { weekday: 'short', day: 'numeric', month: 'short' }),
                available_count: data.available_count || 0,
                doctor_available: Boolean(data.doctor_available),
                slots: Array.isArray(data.slots) ? data.slots : [],
            };

            const index = slotDays.findIndex(item => item.date === data.date);
            if (index >= 0) {
                slotDays[index] = updatedDay;
            } else {
                slotDays = [updatedDay];
            }

            return updatedDay;
        } catch (e) {
            setStatus('We could not refresh slots. Please try again.', 'error', 'bi-exclamation-circle');
            console.error(e);
            return null;
        }
    }

    async function verifyAndSelectSlot(time) {
        console.debug('Verifying selected booking slot:', {
            doctorId,
            type: selectedType,
            date: selectedDate,
            time,
        });

        const day = await loadSlotsForDate(selectedDate);
        const freshSlot = day ? day.slots.find(slot => slot.time === time) : null;

        if (!freshSlot || !freshSlot.available) {
            selectedSlot = null;
            updateContinue();
            renderDates();
            renderSlots();
            setStatus('Slot no longer available', 'error', 'bi-exclamation-circle');
            return;
        }

        selectedSlot = time;
        renderDates();
        renderSlots();
        updateContinue();
        setStatus('Slot selected. Continue to confirm your details.', 'info', 'bi-check-circle');
    }

    function updateContinue() {
        if (!selectedSlot) {
            selectedSummary.textContent = 'No slot selected yet.';
            continueBooking.classList.add('disabled');
            continueBooking.href = '#';
            return;
        }

        continueBooking.href = `/appointment/${doctorId}/${encodeURIComponent(selectedSlot)}?date=${encodeURIComponent(selectedDate)}&type=${encodeURIComponent(selectedType)}`;
        continueBooking.classList.remove('disabled');
        selectedSummary.textContent = `${selectedDate} at ${selectedSlot}`;
    }

    bookingTypeSelect.addEventListener('change', async () => {
        selectedType = bookingTypeSelect.value;
        selectedSlot = null;
        updateContinue();
        await loadSlotsForDate(selectedDate);
        renderDates();
        renderSlots();
    });

    renderDates();
    renderSlots();
    updateContinue();
</script>
</body>
</html>
