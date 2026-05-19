<?php

namespace App\Policies;

use App\Models\Patient;
use App\Support\AuthContext;

class PatientPolicy
{
    public function view(mixed $user, Patient $patient): bool
    {
        return match (AuthContext::role()) {
            'admin', 'staff' => true,
            'patient' => (int) $patient->id === (int) AuthContext::id(),
            default => false,
        };
    }

    public function manage(mixed $user, Patient $patient): bool
    {
        return in_array(AuthContext::role(), ['admin', 'staff'], true);
    }
}
