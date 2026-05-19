<?php

namespace App\Http\Controllers;

use App\Models\BlockedTime;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\DoctorSchedule;
use App\Models\DoctorTimeOff;
use App\Services\Scheduling\SlotGenerationService;
use App\Support\AuditLogger;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminScheduleController extends Controller
{
    private const SCHEDULE_TYPES = ['hospital', 'private_clinic'];

    public function edit(Doctor $doctor): View
    {
        $doctor->load('department');
        $this->syncAppointmentBlocks($doctor);

        $availabilities = DoctorAvailability::query()
            ->where('doctor_id', $doctor->id)
            ->whereIn('schedule_type', self::SCHEDULE_TYPES)
            ->latest('id')
            ->get()
            ->unique('schedule_type')
            ->keyBy('schedule_type');

        $schedules = DoctorSchedule::query()
            ->whereHas('availability', fn ($query) => $query->where('doctor_id', $doctor->id))
            ->with('availability')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $timeOff = DoctorTimeOff::query()
            ->where('doctor_id', $doctor->id)
            ->latest('starts_at')
            ->get();

        $blockedTimes = BlockedTime::query()
            ->where('doctor_id', $doctor->id)
            ->latest('starts_at')
            ->get();

        $previewType = request('preview_schedule_type', 'hospital');
        if (! in_array($previewType, self::SCHEDULE_TYPES, true)) {
            $previewType = 'hospital';
        }

        $previewDate = request('preview_date', now()->toDateString());
        $previewSlots = [];
        try {
            $previewSlots = app(SlotGenerationService::class)->generate($doctor->id, $previewType, $previewDate);
        } catch (\Throwable $e) {
            $previewSlots = [];
        }

        $weeklySchedules = [];
        foreach ($this->days() as $dayNum => $dayLabel) {
            $weeklySchedules[$dayLabel] = $schedules
                ->where('day_of_week', (int) $dayNum)
                ->sortBy('start_time')
                ->values();
        }

        return view('admin.doctor_schedule', [
            'doctor' => $doctor,
            'availabilities' => $availabilities,
            'schedules' => $schedules,
            'schedulesByDay' => $schedules->groupBy('day_of_week'),
            'weeklySchedules' => $weeklySchedules,
            'timeOff' => $timeOff,
            'blockedTimes' => $blockedTimes,
            'scheduleTypes' => self::SCHEDULE_TYPES,
            'days' => $this->days(),
            'previewType' => $previewType,
            'previewDate' => $previewDate,
            'previewSlots' => $previewSlots,
        ]);
    }

    public function saveAvailability(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_type' => ['required', Rule::in(self::SCHEDULE_TYPES)],
            'appointment_duration_minutes' => ['required', 'integer', 'min:5', 'max:240'],
            'break_between_appointments_minutes' => ['required', 'integer', 'min:0', 'max:240'],
            'booking_window_days' => ['required', 'integer', 'min:1', 'max:365'],
            'min_notice_minutes' => ['required', 'integer', 'min:0', 'max:10080'],
            'timezone' => ['nullable', 'string', 'max:80'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->stopForImpactWarning(
            $request,
            $this->countAppointmentsForScheduleType($doctor, $validated['schedule_type'])
        );

        $availability = DoctorAvailability::query()
            ->where('doctor_id', $doctor->id)
            ->where('schedule_type', $validated['schedule_type'])
            ->latest('id')
            ->first() ?? new DoctorAvailability([
                'doctor_id' => $doctor->id,
                'schedule_type' => $validated['schedule_type'],
            ]);

        $availability->fill([
            'appointment_duration_minutes' => $validated['appointment_duration_minutes'],
            'break_between_appointments_minutes' => $validated['break_between_appointments_minutes'],
            'booking_window_days' => $validated['booking_window_days'],
            'min_notice_minutes' => $validated['min_notice_minutes'],
            'timezone' => $validated['timezone'] ?: config('app.timezone', 'Africa/Cairo'),
            'is_active' => $request->boolean('is_active'),
        ])->save();

        AuditLogger::log('doctor_availability.saved', $availability, ['source' => 'admin']);
        $this->syncAppointmentBlocks($doctor);

        return back()->with('success', 'Availability settings saved.');
    }

    public function storeShift(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $this->validateShift($request);
        $availability = $this->availabilityFor($doctor, $validated['schedule_type']);
        $this->ensureShiftDoesNotOverlap($doctor, $validated);
        $this->stopForImpactWarning(
            $request,
            $this->countAppointmentsForShiftWindow($doctor, $validated)
        );

        $schedule = $availability->schedules()->create([
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location_type' => $validated['schedule_type'],
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogger::log('doctor_schedule.created', $schedule, ['source' => 'admin']);
        $this->syncAppointmentBlocks($doctor);

        return back()->with('success', 'Weekly shift added.');
    }

    public function updateShift(Request $request, Doctor $doctor, DoctorSchedule $schedule): RedirectResponse
    {
        $this->authorizeSchedule($doctor, $schedule);
        $validated = $this->validateShift($request);
        $availability = $this->availabilityFor($doctor, $validated['schedule_type']);
        $this->ensureShiftDoesNotOverlap($doctor, $validated, $schedule->id);
        $this->stopForImpactWarning(
            $request,
            $this->countAppointmentsForShiftWindow($doctor, $validated)
        );

        $schedule->update([
            'doctor_availability_id' => $availability->id,
            'day_of_week' => $validated['day_of_week'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location_type' => $validated['schedule_type'],
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLogger::log('doctor_schedule.updated', $schedule, ['source' => 'admin']);
        $this->syncAppointmentBlocks($doctor);

        return back()->with('success', 'Weekly shift updated.');
    }

    public function deleteShift(Doctor $doctor, DoctorSchedule $schedule): RedirectResponse
    {
        $this->authorizeSchedule($doctor, $schedule);
        $impactCount = $this->countAppointmentsForShiftWindow($doctor, [
            'schedule_type' => (string) $schedule->location_type,
            'day_of_week' => (int) $schedule->day_of_week,
            'start_time' => substr((string) $schedule->start_time, 0, 5),
            'end_time' => substr((string) $schedule->end_time, 0, 5),
        ]);
        $this->stopForImpactWarning(request(), $impactCount);

        $schedule->delete();

        AuditLogger::log('doctor_schedule.deleted', null, [
            'source' => 'admin',
            'doctor_id' => $doctor->id,
            'schedule_id' => $schedule->id,
        ]);

        return back()->with('success', 'Weekly shift deleted.');
    }

    public function storeTimeOff(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_type' => ['nullable', Rule::in(self::SCHEDULE_TYPES)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $this->stopForImpactWarning(
            $request,
            $this->countAppointmentsForRange($doctor, $validated['starts_at'], $validated['ends_at'], $validated['schedule_type'] ?? null)
        );

        $timeOff = DoctorTimeOff::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => $validated['schedule_type'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'reason' => $validated['reason'] ?? null,
            'created_by_admin_id' => auth('admin')->id(),
        ]);

        AuditLogger::log('doctor_time_off.created', $timeOff, ['source' => 'admin']);
        $this->syncAppointmentBlocks($doctor);

        return back()->with('success', 'Time off added.');
    }

    public function deleteTimeOff(Doctor $doctor, DoctorTimeOff $timeOff): RedirectResponse
    {
        abort_unless((int) $timeOff->doctor_id === (int) $doctor->id, 404);
        $timeOff->delete();

        AuditLogger::log('doctor_time_off.deleted', null, [
            'source' => 'admin',
            'doctor_id' => $doctor->id,
            'time_off_id' => $timeOff->id,
        ]);

        return back()->with('success', 'Time off deleted.');
    }

    public function storeBlockedTime(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_type' => ['required', Rule::in(self::SCHEDULE_TYPES)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->stopForImpactWarning(
            $request,
            $this->countAppointmentsForRange($doctor, $validated['starts_at'], $validated['ends_at'], $validated['schedule_type'])
        );

        $blockedTime = BlockedTime::query()->create([
            'doctor_id' => $doctor->id,
            'schedule_type' => $validated['schedule_type'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'reason' => $validated['reason'] ?? null,
            'source' => 'manual',
            'created_by_admin_id' => auth('admin')->id(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogger::log('blocked_time.created', $blockedTime, ['source' => 'admin']);
        $this->syncAppointmentBlocks($doctor);

        return back()->with('success', 'Manual block added.');
    }

    public function updateBlockedTime(Request $request, Doctor $doctor, BlockedTime $blockedTime): RedirectResponse
    {
        abort_unless((int) $blockedTime->doctor_id === (int) $doctor->id, 404);
        abort_unless($blockedTime->source === 'manual', 403);

        $validated = $request->validate([
            'schedule_type' => ['required', Rule::in(self::SCHEDULE_TYPES)],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'reason' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $this->stopForImpactWarning(
            $request,
            $this->countAppointmentsForRange($doctor, $validated['starts_at'], $validated['ends_at'], $validated['schedule_type'])
        );

        $blockedTime->update([
            'schedule_type' => $validated['schedule_type'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'reason' => $validated['reason'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLogger::log('blocked_time.updated', $blockedTime, ['source' => 'admin']);
        $this->syncAppointmentBlocks($doctor);

        return back()->with('success', 'Manual block updated.');
    }

    public function deleteBlockedTime(Doctor $doctor, BlockedTime $blockedTime): RedirectResponse
    {
        abort_unless((int) $blockedTime->doctor_id === (int) $doctor->id, 404);
        abort_unless($blockedTime->source === 'manual', 403);
        $blockedTime->delete();

        AuditLogger::log('blocked_time.deleted', null, [
            'source' => 'admin',
            'doctor_id' => $doctor->id,
            'blocked_time_id' => $blockedTime->id,
        ]);

        return back()->with('success', 'Manual block deleted.');
    }

    public function applyTemplate(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'schedule_type' => ['required', Rule::in(self::SCHEDULE_TYPES)],
            'template' => ['required', 'string', Rule::in(['morning', 'evening', 'full', 'auto'])],
        ]);

        $template = $validated['template'] === 'auto' ? 'full' : $validated['template'];
        $scheduleType = $validated['schedule_type'];

        $availability = $this->availabilityFor($doctor, $scheduleType);
        $timezone = (string) ($availability->timezone ?: config('app.timezone', 'Africa/Cairo'));

        $shifts = $this->templateShiftsFor($scheduleType, $template);

        for ($day = 0; $day <= 6; $day++) {
            foreach ($shifts as $shift) {
                $start = $shift['start'];
                $end = $shift['end'];

                $startSql = $this->timeForSql($start);
                $endSql = $this->timeForSql($end);

                if ($this->nextOccurrenceOfDayOverlapsDoctorTimeOff($doctor, $day, $start, $end, $scheduleType, $timezone)) {
                    continue;
                }

                $conflictingOverlap = DoctorSchedule::query()
                    ->whereHas('availability', fn ($q) => $q->where('doctor_id', $doctor->id))
                    ->where('location_type', $scheduleType)
                    ->where('day_of_week', $day)
                    ->where('is_active', true)
                    ->where('start_time', '<', $endSql)
                    ->where('end_time', '>', $startSql)
                    ->where(function ($q) use ($availability, $startSql, $endSql): void {
                        $q->where('doctor_availability_id', '!=', $availability->id)
                            ->orWhere('start_time', '!=', $startSql)
                            ->orWhere('end_time', '!=', $endSql);
                    })
                    ->exists();

                if ($conflictingOverlap) {
                    continue;
                }

                // Unique keys match DB: shifts belong to availability (no doctor_id column on doctor_schedules).
                DoctorSchedule::query()->updateOrCreate(
                    [
                        'doctor_availability_id' => $availability->id,
                        'day_of_week' => $day,
                        'start_time' => $startSql,
                        'end_time' => $endSql,
                        'location_type' => $scheduleType,
                    ],
                    [
                        'is_active' => true,
                    ]
                );
            }
        }

        AuditLogger::log('doctor_schedule.template_applied', null, [
            'doctor_id' => $doctor->id,
            'template' => $template,
            'schedule_type' => $scheduleType,
            'source' => 'admin',
        ]);
        $this->syncAppointmentBlocks($doctor);

        return redirect()->back()->with('success', 'Schedule applied successfully');
    }

    /**
     * @return array<int, array{start: string, end: string}>
     */
    private function templateShiftsFor(string $scheduleType, string $template): array
    {
        $hospital = [
            'morning' => [['start' => '09:00', 'end' => '13:00']],
            'evening' => [['start' => '17:00', 'end' => '21:00']],
            'full' => [['start' => '09:00', 'end' => '13:00'], ['start' => '17:00', 'end' => '21:00']],
        ];
        $privateClinic = [
            'morning' => [['start' => '10:00', 'end' => '14:00']],
            'evening' => [['start' => '18:00', 'end' => '22:00']],
            'full' => [['start' => '10:00', 'end' => '14:00'], ['start' => '18:00', 'end' => '22:00']],
        ];

        $map = $scheduleType === 'private_clinic' ? $privateClinic : $hospital;

        return $map[$template] ?? $map['full'];
    }

    private function nextOccurrenceOfDayOverlapsDoctorTimeOff(
        Doctor $doctor,
        int $dayOfWeek,
        string $startHm,
        string $endHm,
        string $scheduleType,
        string $timezone
    ): bool {
        $tz = $timezone !== '' ? $timezone : config('app.timezone', 'Africa/Cairo');
        $startHm = substr($this->timeForSql($startHm), 0, 5);
        $endHm = substr($this->timeForSql($endHm), 0, 5);

        for ($i = 0; $i < 21; $i++) {
            $date = CarbonImmutable::now($tz)->startOfDay()->addDays($i);
            if ((int) $date->dayOfWeek !== $dayOfWeek) {
                continue;
            }

            $slotStart = CarbonImmutable::parse($date->toDateString() . ' ' . $startHm, $tz);
            $slotEnd = CarbonImmutable::parse($date->toDateString() . ' ' . $endHm, $tz);

            return DoctorTimeOff::query()
                ->where('doctor_id', $doctor->id)
                ->where(function ($q) use ($scheduleType): void {
                    $q->whereNull('schedule_type')
                        ->orWhere('schedule_type', $scheduleType);
                })
                ->where('starts_at', '<', $slotEnd)
                ->where('ends_at', '>', $slotStart)
                ->exists();
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function validateShift(Request $request): array
    {
        return $request->validate([
            'schedule_type' => ['required', Rule::in(self::SCHEDULE_TYPES)],
            'day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /**
     * @param array<string, mixed> $validated
     */
    private function ensureShiftDoesNotOverlap(Doctor $doctor, array $validated, ?int $ignoreScheduleId = null): void
    {
        $requestIsActive = filter_var($validated['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

        if (! $requestIsActive) {
            return;
        }

        $availabilityIds = DoctorAvailability::query()
            ->where('doctor_id', $doctor->id)
            ->where('schedule_type', $validated['schedule_type'])
            ->pluck('id');

        $query = DoctorSchedule::query()
            ->whereIn('doctor_availability_id', $availabilityIds)
            ->where('location_type', $validated['schedule_type'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('is_active', true)
            ->where('start_time', '<', $this->timeForSql($validated['end_time']))
            ->where('end_time', '>', $this->timeForSql($validated['start_time']));

        if ($ignoreScheduleId !== null) {
            $query->whereKeyNot($ignoreScheduleId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'start_time' => 'This shift overlaps an existing active shift for the same day and schedule type.',
            ]);
        }
    }

    private function stopForImpactWarning(Request $request, int $appointmentCount): void
    {
        if ($appointmentCount < 1 || $request->boolean('confirm_impact')) {
            return;
        }

        throw ValidationException::withMessages([
            'schedule_impact' => "This change affects {$appointmentCount} appointments",
        ]);
    }

    private function countAppointmentsForScheduleType(Doctor $doctor, string $scheduleType): int
    {
        return $this->activeAppointments($doctor)
            ->where('type', $this->appointmentTypeForScheduleType($scheduleType))
            ->count();
    }

    /**
     * @param array<string, mixed> $shift
     */
    private function countAppointmentsForShiftWindow(Doctor $doctor, array $shift): int
    {
        return $this->activeAppointments($doctor)
            ->where('type', $this->appointmentTypeForScheduleType($shift['schedule_type']))
            ->get(['date', 'time'])
            ->filter(function (Appointment $appointment) use ($shift): bool {
                $date = CarbonImmutable::parse((string) $appointment->date);
                $time = substr((string) $appointment->time, 0, 5);

                return $date->dayOfWeek === (int) $shift['day_of_week']
                    && $time >= substr((string) $shift['start_time'], 0, 5)
                    && $time < substr((string) $shift['end_time'], 0, 5);
            })
            ->count();
    }

    private function countAppointmentsForRange(Doctor $doctor, string $startsAt, string $endsAt, ?string $scheduleType = null): int
    {
        $start = CarbonImmutable::parse($startsAt);
        $end = CarbonImmutable::parse($endsAt);

        $query = $this->activeAppointments($doctor)
            ->whereDate('date', '>=', $start->toDateString())
            ->whereDate('date', '<=', $end->toDateString());

        if ($scheduleType !== null) {
            $query->where('type', $this->appointmentTypeForScheduleType($scheduleType));
        }

        return $query
            ->get(['date', 'time', 'type'])
            ->filter(function (Appointment $appointment) use ($doctor, $start, $end): bool {
                $duration = $this->appointmentDurationMinutes(
                    $doctor->id,
                    $this->scheduleTypeForAppointmentType((string) $appointment->type)
                );
                $appointmentStart = CarbonImmutable::parse(
                    $appointment->date . ' ' . substr((string) $appointment->time, 0, 5),
                    config('app.timezone', 'Africa/Cairo')
                );
                $appointmentEnd = $appointmentStart->addMinutes($duration);

                return $appointmentStart->lt($end) && $appointmentEnd->gt($start);
            })
            ->count();
    }

    private function activeAppointments(Doctor $doctor)
    {
        return Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->where(function ($query): void {
                $query->whereNull('status')
                    ->orWhereNotIn('status', ['Canceled', 'Cancelled', 'canceled', 'cancelled']);
            });
    }

    private function syncAppointmentBlocks(Doctor $doctor): void
    {
        $activeAppointments = $this->activeAppointments($doctor)->get(['id', 'doctor_id', 'date', 'time', 'type']);
        $activeAppointmentIds = $activeAppointments->pluck('id')->all();

        BlockedTime::query()
            ->where('doctor_id', $doctor->id)
            ->where('source', 'appointment')
            ->when($activeAppointmentIds !== [], fn ($query) => $query->whereNotIn('appointment_id', $activeAppointmentIds))
            ->when($activeAppointmentIds === [], fn ($query) => $query->whereNotNull('appointment_id'))
            ->delete();

        foreach ($activeAppointments as $appointment) {
            $scheduleType = $this->scheduleTypeForAppointmentType((string) $appointment->type);
            $duration = $this->appointmentDurationMinutes($doctor->id, $scheduleType);
            $startsAt = CarbonImmutable::parse(
                $appointment->date . ' ' . substr((string) $appointment->time, 0, 5),
                config('app.timezone', 'Africa/Cairo')
            );

            BlockedTime::query()->updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'source' => 'appointment',
                    'appointment_id' => $appointment->id,
                ],
                [
                    'schedule_type' => $scheduleType,
                    'starts_at' => $startsAt,
                    'ends_at' => $startsAt->addMinutes($duration),
                    'reason' => 'Appointment #' . $appointment->id,
                    'is_active' => true,
                ]
            );
        }
    }

    private function availabilityFor(Doctor $doctor, string $scheduleType): DoctorAvailability
    {
        return DoctorAvailability::query()
            ->where('doctor_id', $doctor->id)
            ->where('schedule_type', $scheduleType)
            ->latest('id')
            ->first()
            ?? DoctorAvailability::query()->create([
                'doctor_id' => $doctor->id,
                'schedule_type' => $scheduleType,
                'appointment_duration_minutes' => 30,
                'break_between_appointments_minutes' => 0,
                'booking_window_days' => 30,
                'min_notice_minutes' => 0,
                'timezone' => config('app.timezone', 'Africa/Cairo'),
                'is_active' => true,
            ]);
    }

    private function appointmentDurationMinutes(int $doctorId, string $scheduleType): int
    {
        return (int) DoctorAvailability::query()
            ->where('doctor_id', $doctorId)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->latest('id')
            ->value('appointment_duration_minutes') ?: 15;
    }

    private function scheduleTypeForAppointmentType(string $appointmentType): string
    {
        return $appointmentType === 'private' ? 'private_clinic' : 'hospital';
    }

    private function appointmentTypeForScheduleType(string $scheduleType): string
    {
        return $scheduleType === 'private_clinic' ? 'private' : 'hospital';
    }

    private function timeForSql(string $time): string
    {
        return strlen($time) === 5 ? $time . ':00' : $time;
    }

    private function authorizeSchedule(Doctor $doctor, DoctorSchedule $schedule): void
    {
        abort_unless(
            DoctorAvailability::query()
                ->whereKey($schedule->doctor_availability_id)
                ->where('doctor_id', $doctor->id)
                ->exists(),
            404
        );
    }

    /**
     * @return array<int, string>
     */
    private function days(): array
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }
}
