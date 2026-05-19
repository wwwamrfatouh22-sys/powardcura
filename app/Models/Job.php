<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    protected $table = 'jobs_training';

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'department',
        'location',
        'salary',
        'type',
        'status',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
