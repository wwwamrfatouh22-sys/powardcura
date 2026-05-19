<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $table = 'complaints';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'type',
        'department',
        'details',
        'status',
        'priority',
    ];

    protected $guarded = ['id'];
}
