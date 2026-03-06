<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $table = 'job_applications';
    protected $fillable = [
        'job_id',
        'name',
        'email',
        'phone',
        'national_id',
        'cv',
    ];
}
