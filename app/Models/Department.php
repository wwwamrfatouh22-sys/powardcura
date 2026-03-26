<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';
    protected $fillable = [
        'name_en',
        'name_ar',
        'image',
        'doctor_id',
        'head_name',
        'status',

    ];

    protected $guarded = ['id'];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
    public function doctor()
    {
        return $this->belongsTo(\App\Models\Doctor::class);
    }
}
