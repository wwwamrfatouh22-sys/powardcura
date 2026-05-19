<?php

namespace App\Models;

use App\Models\Concerns\DiagnosticRequest;
use Illuminate\Database\Eloquent\Model;

class RadiologyRequest extends Model
{
    use DiagnosticRequest;

    protected $table = 'radiology_requests';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_id',
        'phone',
        'request_type',
        'notes',
        'priority',
        'status',
        'uploaded_result',
        'completed_by_staff_id',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];
}
