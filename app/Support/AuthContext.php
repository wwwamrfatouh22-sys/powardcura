<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class AuthContext
{
    public const GUARDS = ['admin', 'admin-api', 'doctor', 'doctor-api', 'staff', 'staff-api', 'patient'];

    private const GUARD_ROLES = [
        'admin' => 'admin',
        'admin-api' => 'admin',
        'doctor' => 'doctor',
        'doctor-api' => 'doctor',
        'staff' => 'staff',
        'staff-api' => 'staff',
        'patient' => 'patient',
    ];

    public static function user(): ?Authenticatable
    {
        foreach (self::GUARDS as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }

        return null;
    }

    public static function role(): ?string
    {
        foreach (self::GUARDS as $guard) {
            if (Auth::guard($guard)->check()) {
                return self::GUARD_ROLES[$guard];
            }
        }

        return null;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user ? (int) $user->getAuthIdentifier() : null;
    }
}
