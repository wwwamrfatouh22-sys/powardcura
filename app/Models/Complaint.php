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
        'type',
        'department',
        'details',
    ];
    protected $guarded = ['id'];

}
