<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalPosition extends Model
{
    protected $fillable = [
        'name',
        'age',
        'gender',
        'phone',
        'department_id',
        'cv',
        'status'
    ];
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
