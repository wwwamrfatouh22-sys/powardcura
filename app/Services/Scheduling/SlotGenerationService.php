<?php

namespace App\Services\Scheduling;

use App\Models\Appointment;
use App\Models\BlockedTime;
use App\Models\DoctorAvailability;
use App\Models\DoctorTimeOff;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class SlotGenerationService
{
    private const CANCELED_STATUSES = ['Canceled', 'Cancelled', 'canceled', 'cancelled'];

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
        $blockedRanges = $this->blockedRanges($doctorId, $scheduleType, $rangeStart, $rangeEnd, $timezone);
        $appointmentRanges = $this->appointmentRanges(
            $doctorId,
            $rangeStart,
            $rangeEnd,
            $timezone,
            (int) $availability->appointment_duration_minutes,
            $ignoreAppointmentId
        );
        $unavailableRanges = array_merge($blockedRanges, $appointmentRanges);

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
     * @param array<int, array{0:CarbonImmutable,1:CarbonImmutable}> $unavailableRanges
     * @return array<int, array{date:string,start_time:string,end_time:string,schedule_type:string}>
     */
    private function slotsForDate(
        DoctorAvailability $availability,
        string $scheduleType,
        CarbonImmutable $date,
        CarbonImmutable $minimumStart,
        array $unavailableRanges
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

                if ($cursor->gte($minimumStart) && ! $this->overlapsAny($cursor, $slotEnd, $unavailableRanges)) {
                    $slots[] = [
                        'date' => $date->toDateString(),
                        'start_time' => $cursor->format('H:i'),
                        'end_time' => $slotEnd->format('H:i'),
                        'schedule_type' => $scheduleType,
                    ];
                }

                $cursor = $cursor->addMinutes($step);
            }
        }

        return $slots;
    }

    /**
     * @return array<int, array{0:CarbonImmutable,1:CarbonImmutable}>
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
            ])
            ->all();
    }

    /**
     * @return array<int, array{0:CarbonImmutable,1:CarbonImmutable}>
     */
    private function appointmentRanges(
        int $doctorId,
        CarbonImmutable $rangeStart,
        CarbonImmutable $rangeEnd,
        string $timezone,
        int $durationMinutes,
        ?int $ignoreAppointmentId = null
    ): array {
        $query = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where(function ($query): void {
                $query->whereNull('status')
                    ->orWhereNotIn('status', self::CANCELED_STATUSES);
            })
            ->whereDate('date', '>=', $rangeStart->toDateString())
            ->whereDate('date', '<=', $rangeEnd->toDateString());

        if ($ignoreAppointmentId !== null) {
            $query->whereKeyNot($ignoreAppointmentId);
        }

        return $query
            ->get(['date', 'time'])
            ->map(function (Appointment $appointment) use ($timezone, $durationMinutes) {
                $start = CarbonImmutable::parse($appointment->date . ' ' . substr((string) $appointment->time, 0, 5), $timezone);

                return [$start, $start->addMinutes($durationMinutes)];
            })
            ->all();
    }

    /**
     * @param array<int, array{0:CarbonImmutable,1:CarbonImmutable}> $ranges
     */
    private function overlapsAny(CarbonImmutable $start, CarbonImmutable $end, array $ranges): bool
    {
        foreach ($ranges as [$blockedStart, $blockedEnd]) {
            if ($start->lt($blockedEnd) && $end->gt($blockedStart)) {
                return true;
            }
        }

        return false;
    }

    private function dateOnly(CarbonInterface|string $date, string $timezone): CarbonImmutable
    {
        return CarbonImmutable::parse($date, $timezone)->startOfDay();
    }

    private function combineDateAndTime(CarbonImmutable $date, string $time): CarbonImmutable
    {
        return CarbonImmutable::parse($date->toDateString() . ' ' . substr($time, 0, 5), $date->timezone);
    }
}
