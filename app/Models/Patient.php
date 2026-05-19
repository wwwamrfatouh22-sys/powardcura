<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    use SoftDeletes;

    protected $table = 'patients';
    protected $fillable = [
        'full_name',
        'national_id',
        'file_number',
        'blood_type',
        'gender',
        'age',
        'dob',
        'phone',
        'address',
        'password',
        'user_id',
        'blood_pressure',
        'pulse_rate',
        'temperature',
        'weight',
        'medical_condition',
        'last_visit'
    ];
    protected $guarded = ['id'];


    public function labTests()
    {
        return $this->hasMany(LabTest::class);
    }

    public function radiologyResults()
    {
        return $this->hasMany(RadiologyResult::class);
    }

    public function labRequests()
    {
        return $this->hasMany(LabRequest::class);
    }

    public function radiologyRequests()
    {
        return $this->hasMany(RadiologyRequest::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function medications()
    {
        return $this->hasMany(Medication::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
