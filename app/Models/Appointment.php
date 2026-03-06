<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';
    protected $fillable = [
        'doctor_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'reason',
        'time',
        'date',
        'payment_method',
        'type'
    ];

    protected $guarded = ['id'];

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
}
