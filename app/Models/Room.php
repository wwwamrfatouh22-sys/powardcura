<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_number',
        'type',
        'floor',
        'capacity',
        'status',
        'patient_id',
        'current_patient'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctorSchedules()
    {
        return $this->hasMany(DoctorSchedule::class);
    }
}
