<?php

namespace App\Support;

use App\Models\Doctor;
use Carbon\Carbon;

class PrivateClinicBookingSupport
{
    public const HOSPITAL_SLOTS = [
        '09:00',
        '10:00',
        '11:00',
        '12:00',
        '14:00',
        '15:00',
        '16:00',
        '17:00',
    ];

    public const WEEK_DAYS = [
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];

    public static function normalizeType(?string $type): string
    {
        return $type === 'private' ? 'private' : 'hospital';
    }

    public static function typeLabel(?string $type): string
    {
        return self::normalizeType($type) === 'private' ? 'Private Clinic' : 'Hospital Visit';
    }

    public static function hasPrivateClinic(Doctor $doctor): bool
    {
        return (bool) ($doctor->has_private_clinic ?? true);
    }

    public static function calculateAmount(Doctor $doctor, ?string $type): float
    {
        $type = self::normalizeType($type);
        $doctor->loadMissing('privateClinic');

        if (
            $type === 'private' &&
            self::hasPrivateClinic($doctor) &&
            $doctor->privateClinic &&
            $doctor->privateClinic->clinic_fee !== null
        ) {
            return (float) $doctor->privateClinic->clinic_fee;
        }

        return $type === 'private' ? 450.00 : 250.00;
    }

    public static function slotsForDoctor(Doctor $doctor, ?string $type): array
    {
        $type = self::normalizeType($type);
        $doctor->loadMissing('privateClinic');

        if (
            $type === 'private' &&
            self::hasPrivateClinic($doctor) &&
            $doctor->privateClinic &&
            is_array($doctor->privateClinic->available_times) &&
            count($doctor->privateClinic->available_times) > 0
        ) {
            return self::normalizeTimes($doctor->privateClinic->available_times);
        }

        return self::HOSPITAL_SLOTS;
    }

    public static function isDateAvailable(Doctor $doctor, ?string $type, string $date): bool
    {
        $type = self::normalizeType($type);

        if ($type !== 'private') {
            return true;
        }

        $doctor->loadMissing('privateClinic');

        if (!self::hasPrivateClinic($doctor)) {
            return false;
        }

        if (!$doctor->privateClinic) {
            return true;
        }

        $days = self::normalizeDays($doctor->privateClinic->available_days ?? []);
        if ($days === []) {
            return true;
        }

        $weekday = strtolower(Carbon::parse($date)->englishDayOfWeek);

        return in_array($weekday, $days, true);
    }

    public static function normalizeDays(?array $days): array
    {
        return collect($days ?? [])
            ->map(fn ($day) => strtolower(trim((string) $day)))
            ->filter(fn ($day) => in_array($day, self::WEEK_DAYS, true))
            ->unique()
            ->values()
            ->all();
    }

    public static function parseTimesString(?string $value): array
    {
        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $parts = preg_split('/[\s,]+/', trim($value)) ?: [];

        return self::normalizeTimes($parts);
    }

    public static function normalizeTimes(?array $times): array
    {
        return collect($times ?? [])
            ->map(function ($time) {
                $normalized = substr(trim((string) $time), 0, 5);

                return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $normalized) ? $normalized : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    public static function clinicSnapshot(Doctor $doctor, ?string $type): array
    {
        $type = self::normalizeType($type);
        $doctor->loadMissing('privateClinic');

        if ($type !== 'private' || !self::hasPrivateClinic($doctor)) {
            return [
                'clinic_name' => null,
                'clinic_address' => null,
                'clinic_phone' => null,
                'clinic_fee' => null,
                'clinic_notes' => null,
            ];
        }

        if (!$doctor->privateClinic) {
            return [
                'clinic_name' => null,
                'clinic_address' => null,
                'clinic_phone' => null,
                'clinic_fee' => null,
                'clinic_notes' => null,
            ];
        }

        return [
            'clinic_name' => $doctor->privateClinic->clinic_name,
            'clinic_address' => $doctor->privateClinic->clinic_address,
            'clinic_phone' => $doctor->privateClinic->clinic_phone,
            'clinic_fee' => $doctor->privateClinic->clinic_fee,
            'clinic_notes' => $doctor->privateClinic->notes,
        ];
    }
}
