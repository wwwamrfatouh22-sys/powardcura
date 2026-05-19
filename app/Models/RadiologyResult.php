<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiologyResult extends Model
{
    protected $table = 'radiology_results';
    protected $fillable = ['patient_id', 'appointment_id', 'patient_phone', 'uploaded_by_staff_id', 'result_type', 'title', 'description', 'notes', 'scan_date', 'file_name'];
    protected $guarded = ['id'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(Staff::class, 'uploaded_by_staff_id');
    }
}
