<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Doctor extends Authenticatable
{

    protected $table = 'doctors';

    protected $fillable = [
        'name',
        'email',
        'password',
        'specialization',
        'image',
        'experience',
        'rating',
        'department_id',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
