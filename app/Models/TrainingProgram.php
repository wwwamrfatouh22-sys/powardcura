<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
     protected $fillable = [
    'name',
    'age',
    'gender',
    'phone',
    'university',
    'department_id',
    'cv',
    'gpa',
    'status'
];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
