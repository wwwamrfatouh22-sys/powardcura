<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Support\AuthContext;

class AppointmentPolicy
{
    public function view(mixed $user, Appointment $appointment): bool
    {
        return match (AuthContext::role()) {
            'admin', 'staff' => true,
            'doctor' => (int) $appointment->doctor_id === (int) AuthContext::id(),
            'patient' => (int) $appointment->patient_id === (int) AuthContext::id(),
            default => false,
        };
    }

    public function manage(mixed $user, Appointment $appointment): bool
    {
        return in_array(AuthContext::role(), ['admin', 'staff'], true);
    }
}
