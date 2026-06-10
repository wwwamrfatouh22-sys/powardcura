<?php

namespace App\Services\Scheduling;

use App\Models\Appointment;
use App\Models\BlockedTime;
use App\Models\DoctorAvailability;
use App\Models\DoctorTimeOff;
use App\Support\AppointmentSecurity;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class SlotGenerationService
{
    /**
     * @return array<int, array{date:string,start_time:string,end_time:string,schedule_type:string}>
     */
    public function generate(
        int $doctorId,
        string $scheduleType,
        CarbonInterface|string $dateFrom,
        CarbonInterface|string|null $dateTo = null,
        ?int $ignoreAppointmentId = null
    ): array {
        if (! in_array($scheduleType, ['hospital', 'private_clinic'], true)) {
            return [];
        }

        $availability = DoctorAvailability::query()
            ->where('doctor_id', $doctorId)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        if (! $availability) {
            return [];
        }

        $timezone = $availability->timezone ?: config('app.timezone', 'Africa/Cairo');
        $startDate = $this->dateOnly($dateFrom, $timezone);
        $endDate = $this->dateOnly($dateTo ?? $dateFrom, $timezone);

        if ($endDate->lt($startDate)) {
            return [];
        }

        $now = CarbonImmutable::now($timezone);
        $latestBookableDate = $now->startOfDay()->addDays((int) $availability->booking_window_days);
        $minimumStart = $now->addMinutes((int) $availability->min_notice_minutes);

        $rangeStart = $startDate->startOfDay();
        $rangeEnd = $endDate->endOfDay();
        $unavailableRanges = $this->unavailableRanges(
            $doctorId,
            $scheduleType,
            $rangeStart,
            $rangeEnd,
            $timezone,
            (int) $availability->appointment_duration_minutes,
            $ignoreAppointmentId
        );

        $slots = [];
        $cursor = $startDate;

        while ($cursor->lte($endDate)) {
            if ($cursor->lt($now->startOfDay()) || $cursor->gt($latestBookableDate)) {
                $cursor = $cursor->addDay();
                continue;
            }

            $daySlots = $this->slotsForDate($availability, $scheduleType, $cursor, $minimumStart, $unavailableRanges);
            array_push($slots, ...$daySlots);
            $cursor = $cursor->addDay();
        }

        return $slots;
    }

    /**
     * @return array<int, array{date:string,start_time:string,end_time:string,schedule_type:string,available:bool,status:string,label:string}>
     */
    public function availability(
        int $doctorId,
        string $scheduleType,
        CarbonInterface|string $dateFrom,
        CarbonInterface|string|null $dateTo = null,
        ?int $ignoreAppointmentId = null,
        ?Collection $blockingAppointments = null
    ): array {
        if (! in_array($scheduleType, ['hospital', 'private_clinic'], true)) {
            return [];
        }

        $availability = DoctorAvailability::query()
            ->where('doctor_id', $doctorId)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        if (! $availability) {
            return [];
        }

        $timezone = $availability->timezone ?: config('app.timezone', 'Africa/Cairo');
        $startDate = $this->dateOnly($dateFrom, $timezone);
        $endDate = $this->dateOnly($dateTo ?? $dateFrom, $timezone);

        if ($endDate->lt($startDate)) {
            return [];
        }

        $now = CarbonImmutable::now($timezone);
        $latestBookableDate = $now->startOfDay()->addDays((int) $availability->booking_window_days);
        $minimumStart = $now->addMinutes((int) $availability->min_notice_minutes);
        $unavailableRanges = $this->unavailableRanges(
            $doctorId,
            $scheduleType,
            $startDate->startOfDay(),
            $endDate->endOfDay(),
            $timezone,
            (int) $availability->appointment_duration_minutes,
            $ignoreAppointmentId,
            $blockingAppointments
        );

        $slots = [];
        $cursor = $startDate;

        while ($cursor->lte($endDate)) {
            if ($cursor->lt($now->startOfDay()) || $cursor->gt($latestBookableDate)) {
                $cursor = $cursor->addDay();
                continue;
            }

            array_push(
                $slots,
                ...$this->slotsForDate($availability, $scheduleType, $cursor, $minimumStart, $unavailableRanges, true)
            );
            $cursor = $cursor->addDay();
        }

        return $slots;
    }

    /**
     * @param array<int, array{0:CarbonImmutable,1:CarbonImmutable,2:string}> $unavailableRanges
     * @return array<int, array<string, mixed>>
     */
    private function slotsForDate(
        DoctorAvailability $availability,
        string $scheduleType,
        CarbonImmutable $date,
        CarbonImmutable $minimumStart,
        array $unavailableRanges,
        bool $includeUnavailable = false
    ): array {
        $duration = (int) $availability->appointment_duration_minutes;
        $break = (int) $availability->break_between_appointments_minutes;
        $step = $duration + $break;

        $schedules = $availability->schedules()
            ->where('day_of_week', $date->dayOfWeek)
            ->where('location_type', $scheduleType)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        $slots = [];

        foreach ($schedules as $schedule) {
            $cursor = $this->combineDateAndTime($date, (string) $schedule->start_time);
            $shiftEnd = $this->combineDateAndTime($date, (string) $schedule->end_time);

            while ($cursor->copy()->addMinutes($duration)->lte($shiftEnd)) {
                $slotEnd = $cursor->addMinutes($duration);
                $unavailableReason = $this->overlapReason($cursor, $slotEnd, $unavailableRanges);

                if ($cursor->gte($minimumStart) && ($includeUnavailable || $unavailableReason === null)) {
                    $slot = [
                        'date' => $date->toDateString(),
                        'start_time' => $cursor->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'schedule_type' => $scheduleType,
                    ];

                    if ($includeUnavailable) {
                        $slot += [
                            'available' => $unavailableReason === null,
                            'status' => $unavailableReason ?? 'available',
                            'label' => $unavailableReason === 'booked' ? 'Slot is already booked' : ($unavailableReason === null ? 'Available' : 'Unavailable'),
                        ];
                    }

                    $slots[] = $slot;
                }

                $cursor = $cursor->addMinutes($step);
            }
        }

        return $slots;
    }

    /**
     * @return array<int, array{0:CarbonImmutable,1:CarbonImmutable,2:string}>
     */
    private function unavailableRanges(
        int $doctorId,
        string $scheduleType,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd,
        string $timezone,
        int $durationMinutes,
        ?int $ignoreAppointmentId,
        ?Collection $blockingAppointments = null
    ): array {
        return array_merge(
            $this->blockedRanges($doctorId, $scheduleType, $rangeStart, $rangeEnd, $timezone),
            $this->appointmentRanges(
                $doctorId,
                $rangeStart,
                $rangeEnd,
                $timezone,
                $durationMinutes,
                $ignoreAppointmentId,
                $blockingAppointments
            )
        );
    }

    /**
     * @return array<int, array{0:CarbonImmutable,1:CarbonImmutable,2:string}>
     */
    private function blockedRanges(
        int $doctorId,
        string $scheduleType,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd,
        string $timezone
    ): array {
        $timeOff = DoctorTimeOff::query()
            ->where('doctor_id', $doctorId)
            ->where(function ($query) use ($scheduleType): void {
                $query->whereNull('schedule_type')
                    ->orWhere('schedule_type', $scheduleType);
            })
            ->where('starts_at', '<', $rangeEnd)
            ->where('ends_at', '>', $rangeStart)
            ->get(['starts_at', 'ends_at']);

        $blocked = BlockedTime::query()
            ->where('doctor_id', $doctorId)
            ->where('schedule_type', $scheduleType)
            ->where('is_active', true)
            ->where('starts_at', '<', $rangeEnd)
            ->where('ends_at', '>', $rangeStart)
            ->get(['starts_at', 'ends_at']);

        return $timeOff
            ->concat($blocked)
            ->map(fn ($range) => [
                CarbonImmutable::parse($range->starts_at, $timezone),
                CarbonImmutable::parse($range->ends_at, $timezone),
                'blocked',
            ])
            ->all();
    }

    /**
     * @return array<int, array{0:CarbonImmutable,1:CarbonImmutable,2:string}>
     */
    private function appointmentRanges(
        int $doctorId,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd,
        string $timezone,
        int $durationMinutes,
        ?int $ignoreAppointmentId = null,
        ?Collection $blockingAppointments = null
    ): array {
        $appointments = $blockingAppointments ?? AppointmentSecurity::blockingAppointments(
            $doctorId,
            $rangeStart->toDateString(),
            $rangeEnd->toDateString(),
            $ignoreAppointmentId
        )->get(['id', 'date', 'time']);

        return $appointments
            ->when(
                $ignoreAppointmentId !== null,
                fn (Collection $items) => $items->where('id', '!=', $ignoreAppointmentId)
            )
            ->map(function (Appointment $appointment) use ($timezone, $durationMinutes) {
                $time = AppointmentSecurity::normalizeTime((string) $appointment->time);
                $start = CarbonImmutable::parse($appointment->date . ' ' . $time, $timezone);

                return [$start, $start->addMinutes($durationMinutes), 'booked'];
            })
            ->all();
    }

    /**
     * @param array<int, array{0:CarbonImmutable,1:CarbonImmutable,2:string}> $ranges
     */
    private function overlapReason(CarbonImmutable $start, CarbonImmutable $end, array $ranges): ?string
    {
        foreach ($ranges as [$blockedStart, $blockedEnd, $reason]) {
            if ($start->lt($blockedEnd) && $end->gt($blockedStart)) {
                return $reason;
            }
        }

        return null;
    }

    private function dateOnly(CarbonInterface|string $date, string $timezone): CarbonImmutable
    {
        $dateString = $date instanceof CarbonInterface ? $date->format('Y-m-d') : $date;

        return CarbonImmutable::parse($dateString, $timezone)->startOfDay();
    }

    private function combineDateAndTime(CarbonImmutable $date, string $time): CarbonImmutable
    {
        return CarbonImmutable::parse($date->toDateString() . ' ' . substr($time, 0, 5), $date->timezone);
    }
}
