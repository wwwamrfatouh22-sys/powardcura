<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'report_number',
        'report_type',
        'patient_id',
        'doctor_id',
        'department_id',
        'generated_date',
        'priority',
        'status',
        'is_reviewed',
    ];

    protected $casts = [
        'is_reviewed' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
