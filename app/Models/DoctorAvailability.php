<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorAvailability extends Model
{
    protected $fillable = [
        'doctor_id',
        'schedule_type',
        'appointment_duration_minutes',
        'break_between_appointments_minutes',
        'booking_window_days',
        'min_notice_minutes',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'doctor_id' => 'integer',
        'appointment_duration_minutes' => 'integer',
        'break_between_appointments_minutes' => 'integer',
        'booking_window_days' => 'integer',
        'min_notice_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function schedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function timeOff()
    {
        return $this->hasMany(DoctorTimeOff::class, 'doctor_id', 'doctor_id');
    }

    public function blockedTimes()
    {
        return $this->hasMany(BlockedTime::class, 'doctor_id', 'doctor_id');
    }
}
