<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    protected $fillable = [
        'doctor_availability_id',
        'day_of_week',
        'start_time',
        'end_time',
        'location_type',
        'room_id',
        'is_active',
    ];

    protected $casts = [
        'doctor_availability_id' => 'integer',
        'day_of_week' => 'integer',
        'room_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function availability()
    {
        return $this->belongsTo(DoctorAvailability::class, 'doctor_availability_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
