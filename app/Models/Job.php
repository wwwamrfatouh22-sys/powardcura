<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs_training';
    protected $fillable = [
        'title',
        'description',
        'requirements',
        'location',
        'salary',
        'type',
    ];
    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
