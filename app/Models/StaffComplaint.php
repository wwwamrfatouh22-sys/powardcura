<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffComplaint extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'date',
        'subject',
        'priority',
        'status'
    ];
}
