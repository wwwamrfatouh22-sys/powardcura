<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    private const GUARD_ROLES = [
        'admin' => 'admin',
        'admin-api' => 'admin',
        'doctor' => 'doctor',
        'doctor-api' => 'doctor',
        'patient' => 'patient',
        'web' => 'patient',
        'staff' => 'staff',
        'staff-api' => 'staff',
    ];

    private const ROLE_MODELS = [
        'admin' => Admin::class,
        'doctor' => Doctor::class,
        'patient' => Patient::class,
        'staff' => Staff::class,
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $guards = $this->routeGuards($request);

        abort_unless($guards !== [], 403);

        $activeGuards = [];

        foreach ($guards as $guard) {
            $role = self::GUARD_ROLES[$guard] ?? null;

            if ($role === null || !in_array($role, $roles, true)) {
                continue;
            }

            if (!Auth::guard($guard)->check()) {
                continue;
            }

            $user = Auth::guard($guard)->user();
            $expectedModel = self::ROLE_MODELS[$role] ?? null;

            abort_unless($expectedModel !== null && $user instanceof $expectedModel, 403);

            $activeGuards[] = $guard;
        }

        abort_unless(count($activeGuards) === 1, 403);

        return $next($request);
    }

    /**
     * Role checks are valid only when a route declares explicit auth guards.
     *
     * @return array<int, string>
     */
    private function routeGuards(Request $request): array
    {
        $guards = [];

        foreach ($request->route()?->gatherMiddleware() ?? [] as $middleware) {
            if (!is_string($middleware) || !str_starts_with($middleware, 'auth:')) {
                continue;
            }

            foreach (explode(',', substr($middleware, 5)) as $guard) {
                $guard = trim($guard);

                if ($guard !== '') {
                    $guards[] = $guard;
                }
            }
        }

        return array_values(array_unique($guards));
    }
}
