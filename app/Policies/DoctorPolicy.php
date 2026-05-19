<?php

namespace App\Policies;

use App\Models\Doctor;
use App\Support\AuthContext;

class DoctorPolicy
{
    public function view(mixed $user, Doctor $doctor): bool
    {
        return in_array(AuthContext::role(), ['admin', 'staff'], true)
            || (AuthContext::role() === 'doctor' && (int) $doctor->id === (int) AuthContext::id());
    }

    public function manage(mixed $user, Doctor $doctor): bool
    {
        return in_array(AuthContext::role(), ['admin', 'staff'], true);
    }
}
