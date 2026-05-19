<?php

namespace App\Models\Concerns;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait DiagnosticRequest
{
    public const PRIORITIES = ['normal', 'urgent'];
    public const STATUSES = ['pending', 'processing', 'completed'];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'completed_by_staff_id');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
