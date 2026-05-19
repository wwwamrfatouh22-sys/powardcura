<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedTime extends Model
{
    protected $fillable = [
        'doctor_id',
        'schedule_type',
        'starts_at',
        'ends_at',
        'reason',
        'source',
        'appointment_id',
        'created_by_admin_id',
        'is_active',
    ];

    protected $casts = [
        'doctor_id' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'appointment_id' => 'integer',
        'created_by_admin_id' => 'integer',
        'is_active' => 'boolean',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
