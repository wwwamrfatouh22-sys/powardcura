<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        abort_unless(Auth::guard('staff')->check(), 403);
        abort_unless(in_array(Auth::guard('staff')->user()?->role, $roles, true), 403);

        return $next($request);
    }
}
