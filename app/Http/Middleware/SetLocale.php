<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale', config('app.locale', 'en'));

        if (!in_array($locale, ['en', 'ar'], true)) {
            $locale = 'en';
        }

        App::setLocale($locale);
        Carbon::setLocale($locale);
        View::share('currentLocale', $locale);
        View::share('isRtl', $locale === 'ar');

        return $next($request);
    }
}
