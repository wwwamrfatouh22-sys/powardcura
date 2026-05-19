<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateClinic extends Model
{
    protected $fillable = [
        'doctor_id',
        'clinic_name',
        'clinic_address',
        'clinic_phone',
        'clinic_fee',
        'available_days',
        'available_times',
        'notes',
    ];

    protected $casts = [
        'clinic_fee' => 'float',
        'available_days' => 'array',
        'available_times' => 'array',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
