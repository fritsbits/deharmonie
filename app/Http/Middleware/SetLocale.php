<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next, ?string $locale = null): Response
    {
        $locale = $locale ?? $request->segment(1);

        if (in_array($locale, ['nl', 'fr'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
