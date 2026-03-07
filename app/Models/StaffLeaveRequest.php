<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLeaveRequest extends Model
{
    protected $table = 'staff_leave_requests';
    protected $fillable = [
        'doctor_id',
        'nurse_id',
        'start_date',
        'end_date',
        'reason',
        'status'
    ];
}
