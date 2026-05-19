<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'department_id',
        'date',
        'time',
        'status',
        'first_name',
        'last_name',
        'email',
        'phone',
        'reason',
        'payment_method',
        'payment_amount',
        'payment_status',
        'type',
        'clinic_name',
        'clinic_address',
        'clinic_phone',
        'clinic_fee',
        'clinic_notes',
        'cancellation_reason',
        'canceled_at',
    ];
    protected $guarded = ['id'];

    protected $casts = [
        'payment_amount' => 'float',
        'clinic_fee' => 'float',
        'canceled_at' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function websiteRating()
    {
        return $this->hasOne(WebsiteRating::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class);
    }

    public function radiologyRequests()
    {
        return $this->hasMany(RadiologyRequest::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function blockedTimes()
    {
        return $this->hasMany(BlockedTime::class);
    }

    public function isCompleted(): bool
    {
        return strtolower((string) $this->status) === 'completed';
    }

    public function isCanceled(): bool
    {
        return in_array((string) $this->status, ['Canceled', 'Cancelled', 'canceled', 'cancelled'], true);
    }

    public function canBeManagedByPatient(): bool
    {
        return ! $this->isCompleted() && ! $this->isCanceled();
    }

    public function canReceiveDoctorRating(): bool
    {
        return $this->isCompleted();
    }

    public function isPaymentVerified(): bool
    {
        $ps = strtolower((string) ($this->payment_status ?? ''));

        return in_array($ps, ['confirmed', 'paid'], true);
    }
}
