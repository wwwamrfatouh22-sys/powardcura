<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = [
        'doctor_id',
        'start_date',
        'end_date',
        'reason',
        'status'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
