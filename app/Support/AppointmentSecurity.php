<?php

namespace App\Support;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class AppointmentSecurity
{
    private const CANCELED_STATUSES = ['canceled', 'cancelled'];

    public static function normalizeTime(string $time): string
    {
        $normalized = trim($time);

        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $normalized, $matches) !== 1) {
            return '';
        }

        $hour = (int) $matches[1];
        $minute = (int) $matches[2];

        if ($hour > 23 || $minute > 59) {
            return '';
        }

        return sprintf('%02d:%02d', $hour, $minute);
    }

    public static function normalizeStatus(?string $status): string
    {
        return strtolower(trim((string) $status));
    }

    public static function isCanceledStatus(?string $status): bool
    {
        return in_array(self::normalizeStatus($status), self::CANCELED_STATUSES, true);
    }

    public static function blockingAppointments(
        int $doctorId,
        string $dateFrom,
        ?string $dateTo = null,
        ?int $ignoreAppointmentId = null
    ): Builder {
        $query = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereRaw("COALESCE(LOWER(TRIM(status)), '') NOT IN (?, ?)", self::CANCELED_STATUSES)
            ->whereDate('date', '>=', $dateFrom)
            ->whereDate('date', '<=', $dateTo ?? $dateFrom);

        if ($ignoreAppointmentId !== null) {
            $query->whereKeyNot($ignoreAppointmentId);
        }

        return $query;
    }

    public static function ensureSlotAvailable(int $doctorId, string $date, string $time, ?int $ignoreAppointmentId = null): void
    {
        $normalizedTime = self::normalizeTime($time);

        $query = self::blockingAppointments($doctorId, $date, $date, $ignoreAppointmentId)
            ->where(function ($builder) use ($normalizedTime) {
                $builder
                    ->where('time', $normalizedTime)
                    ->orWhere('time', $normalizedTime . ':00');
            });

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'time' => 'This time slot has already been booked. Please select another slot.',
            ]);
        }
    }
}
