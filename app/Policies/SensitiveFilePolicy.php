<?php

namespace App\Policies;

use App\Models\LabTest;
use App\Models\RadiologyResult;
use App\Support\AuthContext;
use Illuminate\Support\Facades\Auth;

class SensitiveFilePolicy
{
    public function viewMedicalResult(LabTest|RadiologyResult $record): bool
    {
        return match (AuthContext::role()) {
            'admin' => true,
            'staff' => Auth::guard('staff')->user()?->role === 'radiology_lab',
            'patient' => (int) $record->patient_id === (int) AuthContext::id(),
            default => false,
        };
    }
}
