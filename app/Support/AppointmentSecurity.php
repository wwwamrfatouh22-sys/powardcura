<?php

namespace App\Support;

use App\Models\Appointment;
use Illuminate\Validation\ValidationException;

class AppointmentSecurity
{
    public static function normalizeTime(string $time): string
    {
        $normalized = trim($time);

        if (preg_match('/^\d{2}:\d{2}(?::\d{2})?$/', $normalized) !== 1) {
            return substr($normalized, 0, 5);
        }

        return substr($normalized, 0, 5);
    }

    public static function ensureSlotAvailable(int $doctorId, string $date, string $time, ?int $ignoreAppointmentId = null): void
    {
        $normalizedTime = self::normalizeTime($time);

        $query = Appointment::query()
            ->where('doctor_id', $doctorId)
            ->where(function ($builder) {
                $builder->whereNull('status')
                    ->orWhereNotIn('status', ['Canceled', 'Cancelled', 'canceled', 'cancelled']);
            })
            ->whereDate('date', $date)
            ->where(function ($builder) use ($normalizedTime) {
                $builder
                    ->where('time', $normalizedTime)
                    ->orWhere('time', $normalizedTime . ':00');
            });

        if ($ignoreAppointmentId !== null) {
            $query->whereKeyNot($ignoreAppointmentId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'time' => 'This time slot has already been booked. Please select another slot.',
            ]);
        }
    }
}
