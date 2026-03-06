<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiologyResult extends Model
{
    protected $table = 'lab_tests';
    protected $fillable = ['patient_id', 'title', 'description','scan_date'];
    protected $guarded = ['id'];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
