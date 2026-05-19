<?php

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use Illuminate\Support\Facades\Route;

test('primary web guards are isolated by provider and model', function () {
    $expected = [
        'admin' => ['provider' => 'admins', 'model' => Admin::class],
        'doctor' => ['provider' => 'doctors', 'model' => Doctor::class],
        'patient' => ['provider' => 'patients', 'model' => Patient::class],
        'staff' => ['provider' => 'staff', 'model' => Staff::class],
    ];

    foreach ($expected as $guard => $definition) {
        expect(config("auth.guards.$guard.provider"))->toBe($definition['provider'])
            ->and(config("auth.providers.{$definition['provider']}.model"))->toBe($definition['model']);
    }
});

test('routes using role middleware also declare matching explicit auth guards', function () {
    $guardRoles = [
        'admin' => 'admin',
        'admin-api' => 'admin',
        'doctor' => 'doctor',
        'doctor-api' => 'doctor',
        'patient' => 'patient',
        'staff' => 'staff',
        'staff-api' => 'staff',
    ];

    foreach (Route::getRoutes() as $route) {
        $middleware = $route->gatherMiddleware();
        $roleMiddleware = collect($middleware)->first(
            fn ($entry) => is_string($entry) && str_starts_with($entry, 'role:')
        );

        if ($roleMiddleware === null) {
            continue;
        }

        $roles = explode(',', substr($roleMiddleware, 5));
        $authGuards = collect($middleware)
            ->filter(fn ($entry) => is_string($entry) && str_starts_with($entry, 'auth:'))
            ->flatMap(fn ($entry) => explode(',', substr($entry, 5)))
            ->map(fn ($guard) => trim($guard))
            ->filter()
            ->values();

        expect($authGuards->all())->not->toBeEmpty("{$route->uri()} uses role middleware without an explicit auth guard.");

        foreach ($roles as $role) {
            $role = trim($role);
            $hasMatchingGuard = $authGuards->contains(
                fn ($guard) => ($guardRoles[$guard] ?? null) === $role
            );

            expect($hasMatchingGuard)->toBeTrue("{$route->uri()} role:$role has no matching explicit auth guard.");
        }
    }
});

test('api routes do not use web session authentication', function () {
    foreach (Route::getRoutes() as $route) {
        if (!str_starts_with($route->uri(), 'api/')) {
            continue;
        }

        expect($route->gatherMiddleware())->not->toContain('web');
    }
});
