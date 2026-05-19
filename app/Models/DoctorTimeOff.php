<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorTimeOff extends Model
{
    protected $table = 'doctor_time_off';

    protected $fillable = [
        'doctor_id',
        'schedule_type',
        'starts_at',
        'ends_at',
        'reason',
        'created_by_admin_id',
    ];

    protected $casts = [
        'doctor_id' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'created_by_admin_id' => 'integer',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function createdByAdmin()
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
