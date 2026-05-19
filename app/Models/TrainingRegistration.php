<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingRegistration extends Model
{
    protected $fillable = [
        'training_id',
        'full_name',
        'email',
        'phone',
        'national_id',
        'cv_path',
        'department_id',
        'age',
        'gender',
        'university',
        'gpa',
        'status',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
