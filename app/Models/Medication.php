<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $fillable = [
        'patient_id',
        'name',
        'dose',
        'instructions'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

}
