<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Doctor extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'doctors';

    protected $fillable = [
        'name',
        'email',
        'password',
        'specialization',
        'image',
        'experience',
        'rating',
        'department_id',
        'status',
        'has_private_clinic',
    ];

    protected $casts = [
        'has_private_clinic' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class);
    }

    public function radiologyRequests()
    {
        return $this->hasMany(RadiologyRequest::class);
    }

    public function privateClinic()
    {
        return $this->hasOne(PrivateClinic::class);
    }

    public function doctorAvailabilities()
    {
        return $this->hasMany(DoctorAvailability::class);
    }

    public function doctorSchedules()
    {
        return $this->hasManyThrough(
            DoctorSchedule::class,
            DoctorAvailability::class,
            'doctor_id',
            'doctor_availability_id',
            'id',
            'id'
        );
    }

    public function doctorTimeOff()
    {
        return $this->hasMany(DoctorTimeOff::class);
    }

    public function blockedTimes()
    {
        return $this->hasMany(BlockedTime::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
