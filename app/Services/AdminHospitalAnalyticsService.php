<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminHospitalAnalyticsService
{
    private const WAITING_STATUSES = ['Pending', 'Confirmed'];
    private const COMPLETED_STATUSES = ['Completed'];
    private const CANCELED_STATUSES = ['Canceled', 'Cancelled'];

    public function build(): array
    {
        if (! Schema::hasTable('appointments')) {
            return $this->emptyAnalytics();
        }

        return [
            'daysPressure' => $this->buildDaysPressure(),
            'queuePressure' => $this->buildQueuePressure(),
            'doctorLoad' => $this->buildDoctorLoad(),
        ];
    }

    private function buildDaysPressure(): array
    {
        $dayTemplate = collect(range(0, 6))->mapWithKeys(fn (int $day) => [
            $day => [
                'day_index' => $day,
                'label' => CarbonImmutable::now()->startOfWeek()->addDays($day)->format('D'),
                'count' => 0,
            ],
        ]);

        $appointmentsByDate = Appointment::query()
            ->whereNotNull('date')
            ->select('date', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('date')
            ->get();

        $weekly = $dayTemplate->toArray();
        foreach ($appointmentsByDate as $row) {
            $date = CarbonImmutable::parse((string) $row->date);
            $dayIndex = $date->dayOfWeekIso - 1;
            $weekly[$dayIndex]['count'] += (int) $row->aggregate;
        }

        $weekly = collect($weekly)->sortBy('day_index')->values();
        $nonZeroDays = $weekly->filter(fn (array $row) => $row['count'] > 0);
        $mostBusyDay = $nonZeroDays->sortByDesc('count')->first();
        $leastBusyDay = $nonZeroDays->sortBy('count')->first();

        $hourRows = Appointment::query()
            ->whereNotNull('time')
            ->selectRaw($this->hourExpression() . ' as booking_hour, COUNT(*) as aggregate')
            ->groupBy('booking_hour')
            ->get();

        $slots = collect([
            ['key' => 'morning', 'label' => '9AM-12PM', 'start' => 9, 'end' => 12, 'count' => 0],
            ['key' => 'midday', 'label' => '12PM-3PM', 'start' => 12, 'end' => 15, 'count' => 0],
            ['key' => 'afternoon', 'label' => '3PM-6PM', 'start' => 15, 'end' => 18, 'count' => 0],
            ['key' => 'evening', 'label' => '6PM-9PM', 'start' => 18, 'end' => 21, 'count' => 0],
        ])->map(function (array $slot) use ($hourRows) {
            $slot['count'] = $hourRows
                ->filter(fn ($row) => (int) $row->booking_hour >= $slot['start'] && (int) $row->booking_hour < $slot['end'])
                ->sum(fn ($row) => (int) $row->aggregate);

            return $slot;
        });

        $maxSlotCount = max(1, (int) $slots->max('count'));
        $slots = $slots->map(function (array $slot) use ($maxSlotCount) {
            $ratio = $slot['count'] / $maxSlotCount;
            $slot['pressure'] = $ratio >= 0.67 ? 'High' : ($ratio >= 0.34 ? 'Medium' : 'Low');
            $slot['tone'] = strtolower($slot['pressure']);
            $slot['percent'] = (int) round($ratio * 100);

            return $slot;
        })->values();

        return [
            'weekly' => $weekly,
            'mostBusyDay' => $mostBusyDay,
            'leastBusyDay' => $leastBusyDay,
            'hourlySlots' => $slots,
            'peakBookingHours' => $slots->sortByDesc('count')->take(2)->values(),
        ];
    }

    private function buildQueuePressure(): array
    {
        $now = CarbonImmutable::now();
        $today = $now->toDateString();

        $waitingAppointments = Appointment::query()
            ->with(['doctor.department', 'department'])
            ->whereDate('date', $today)
            ->whereIn('status', self::WAITING_STATUSES)
            ->orderBy('time')
            ->orderBy('id')
            ->get();

        $waitingRows = $waitingAppointments->values()->map(function (Appointment $appointment, int $index) use ($now) {
            $scheduledAt = $this->appointmentDateTime($appointment);
            $waitMinutes = $scheduledAt && $scheduledAt->lessThan($now)
                ? $scheduledAt->diffInMinutes($now)
                : 0;
            $bookingLeadMinutes = $appointment->created_at && $scheduledAt
                ? CarbonImmutable::parse($appointment->created_at)->diffInMinutes($scheduledAt, false)
                : null;
            $department = $appointment->department?->name_en
                ?: $appointment->doctor?->department?->name_en
                ?: 'Unassigned';

            return [
                'id' => $appointment->id,
                'queue_number' => $index + 1,
                'doctor_id' => $appointment->doctor_id,
                'doctor' => $appointment->doctor?->name ?: 'Unassigned',
                'department' => $department,
                'time' => (string) $appointment->time,
                'status' => (string) $appointment->status,
                'wait_minutes' => (int) $waitMinutes,
                'booking_lead_minutes' => $bookingLeadMinutes,
            ];
        });

        $currentWaiting = $waitingRows->count();
        $averageWait = $currentWaiting > 0 ? (int) round($waitingRows->avg('wait_minutes')) : 0;
        $departmentQueues = $waitingRows
            ->groupBy('department')
            ->map(fn (Collection $rows, string $department) => [
                'department' => $department,
                'count' => $rows->count(),
                'average_wait' => (int) round($rows->avg('wait_minutes')),
                'longest_wait' => (int) $rows->max('wait_minutes'),
            ])
            ->sortByDesc(fn (array $row) => [$row['count'], $row['longest_wait']])
            ->values();

        $doctorQueues = $waitingRows
            ->groupBy('doctor')
            ->map(fn (Collection $rows, string $doctor) => [
                'doctor' => $doctor,
                'count' => $rows->count(),
                'average_wait' => (int) round($rows->avg('wait_minutes')),
            ])
            ->sortByDesc(fn (array $row) => [$row['average_wait'], $row['count']])
            ->values();

        $pressureScore = min(100, ($currentWaiting * 10) + min(40, $averageWait));
        $pressure = $pressureScore >= 70 ? 'High' : ($pressureScore >= 35 ? 'Medium' : 'Low');

        return [
            'currentWaiting' => $currentWaiting,
            'averageWaitMinutes' => $averageWait,
            'longestQueue' => $departmentQueues->first(),
            'estimatedPressure' => $pressure,
            'pressureScore' => $pressureScore,
            'busyNow' => $currentWaiting,
            'queueOverload' => $departmentQueues->firstWhere('count', '>=', 5) ?: $departmentQueues->first(),
            'fastDepartments' => $departmentQueues->filter(fn (array $row) => $row['average_wait'] <= 15)->take(3)->values(),
            'delayedDoctors' => $doctorQueues->filter(fn (array $row) => $row['average_wait'] >= 20)->take(5)->values(),
        ];
    }

    private function buildDoctorLoad(): array
    {
        if (! Schema::hasTable('doctors')) {
            return [
                'rows' => collect(),
                'topBusyDoctors' => collect(),
                'busiestDoctor' => null,
                'leastBusyDoctor' => null,
            ];
        }

        $appointmentStats = Appointment::query()
            ->select('doctor_id')
            ->selectRaw('COUNT(*) as total_appointments')
            ->selectRaw($this->statusCountExpression(self::COMPLETED_STATUSES) . ' as completed_appointments')
            ->selectRaw($this->statusCountExpression(self::CANCELED_STATUSES) . ' as canceled_appointments')
            ->selectRaw('COUNT(DISTINCT date) as active_days')
            ->groupBy('doctor_id')
            ->get()
            ->keyBy('doctor_id');

        $scheduleCapacity = $this->doctorScheduleCapacity();

        $doctors = Doctor::query()
            ->withoutTrashed()
            ->with('department:id,name_en')
            ->select('id', 'name', 'department_id', 'specialization', 'status')
            ->get();

        $rows = $doctors->map(function (Doctor $doctor) use ($appointmentStats, $scheduleCapacity) {
            $stats = $appointmentStats->get($doctor->id);
            $total = (int) ($stats->total_appointments ?? 0);
            $completed = (int) ($stats->completed_appointments ?? 0);
            $canceled = (int) ($stats->canceled_appointments ?? 0);
            $activeDays = max(1, (int) ($stats->active_days ?? 0));
            $weeklyCapacity = (int) ($scheduleCapacity[$doctor->id]['weekly_slots'] ?? 0);
            $estimatedMonthlyCapacity = $weeklyCapacity > 0 ? (int) round($weeklyCapacity * 4.285) : 0;
            $utilization = $estimatedMonthlyCapacity > 0
                ? min(100, (int) round(($total / $estimatedMonthlyCapacity) * 100))
                : 0;

            return [
                'doctor_id' => $doctor->id,
                'name' => $doctor->name,
                'department' => $doctor->department?->name_en ?? 'Unassigned',
                'total_appointments' => $total,
                'completed_appointments' => $completed,
                'canceled_appointments' => $canceled,
                'average_daily_load' => round($total / $activeDays, 1),
                'schedule_utilization' => $utilization,
                'weekly_capacity' => $weeklyCapacity,
            ];
        })->sortByDesc('total_appointments')->values();

        $withAppointments = $rows->filter(fn (array $row) => $row['total_appointments'] > 0);

        return [
            'rows' => $rows,
            'topBusyDoctors' => $rows->take(6)->values(),
            'busiestDoctor' => $withAppointments->first(),
            'leastBusyDoctor' => $withAppointments->sortBy('total_appointments')->first(),
        ];
    }

    private function doctorScheduleCapacity(): array
    {
        if (! Schema::hasTable('doctor_availabilities') || ! Schema::hasTable('doctor_schedules')) {
            return [];
        }

        $rows = DB::table('doctor_availabilities')
            ->join('doctor_schedules', 'doctor_schedules.doctor_availability_id', '=', 'doctor_availabilities.id')
            ->where('doctor_availabilities.is_active', true)
            ->where('doctor_schedules.is_active', true)
            ->select(
                'doctor_availabilities.doctor_id',
                'doctor_availabilities.appointment_duration_minutes',
                'doctor_schedules.start_time',
                'doctor_schedules.end_time'
            )
            ->get();

        return $rows->groupBy('doctor_id')->map(function (Collection $doctorRows) {
            $weeklySlots = $doctorRows->sum(function ($row) {
                $duration = max(1, (int) $row->appointment_duration_minutes);
                $start = CarbonImmutable::parse((string) $row->start_time);
                $end = CarbonImmutable::parse((string) $row->end_time);
                $minutes = max(0, $start->diffInMinutes($end, false));

                return (int) floor($minutes / $duration);
            });

            return ['weekly_slots' => $weeklySlots];
        })->toArray();
    }

    private function appointmentDateTime(Appointment $appointment): ?CarbonImmutable
    {
        if (! $appointment->date || ! $appointment->time) {
            return null;
        }

        try {
            return CarbonImmutable::parse($appointment->date . ' ' . substr((string) $appointment->time, 0, 5));
        } catch (\Throwable) {
            return null;
        }
    }

    private function hourExpression(): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'sqlite' => "CAST(SUBSTR(time, 1, 2) AS INTEGER)",
            'pgsql' => "CAST(SUBSTRING(time FROM 1 FOR 2) AS INTEGER)",
            default => "CAST(SUBSTRING(time, 1, 2) AS UNSIGNED)",
        };
    }

    private function statusCountExpression(array $statuses): string
    {
        $quoted = collect($statuses)
            ->map(fn (string $status) => DB::connection()->getPdo()->quote($status))
            ->implode(',');

        return "SUM(CASE WHEN status IN ({$quoted}) THEN 1 ELSE 0 END)";
    }

    private function emptyAnalytics(): array
    {
        return [
            'daysPressure' => [
                'weekly' => collect(),
                'mostBusyDay' => null,
                'leastBusyDay' => null,
                'hourlySlots' => collect(),
                'peakBookingHours' => collect(),
            ],
            'queuePressure' => [
                'currentWaiting' => 0,
                'averageWaitMinutes' => 0,
                'longestQueue' => null,
                'estimatedPressure' => 'Low',
                'pressureScore' => 0,
                'busyNow' => 0,
                'queueOverload' => null,
                'fastDepartments' => collect(),
                'delayedDoctors' => collect(),
            ],
            'doctorLoad' => [
                'rows' => collect(),
                'topBusyDoctors' => collect(),
                'busiestDoctor' => null,
                'leastBusyDoctor' => null,
            ],
        ];
    }
}
