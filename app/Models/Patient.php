<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
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
        'weight'
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

}
