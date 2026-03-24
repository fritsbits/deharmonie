<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next, string $locale = 'nl')
    {
        app()->setLocale($locale);
        return $next($request);
    }
}
