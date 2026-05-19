<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StaffNotRadiologyLabMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(Auth::guard('staff')->check(), 403);
        abort_if(in_array(Auth::guard('staff')->user()?->role, ['radiology_lab', 'radiology', 'laboratory', 'lab'], true), 403);

        return $next($request);
    }
}
