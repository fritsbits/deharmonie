<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectPreferredLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->cookie('preferred_locale')) {
            return $next($request);
        }

        $firstTag = explode(',', $request->header('Accept-Language', 'nl'))[0];
        $lang = substr(trim($firstTag), 0, 2);

        if ($lang === 'fr') {
            $frUrl = $this->resolveEquivalentUrl($request, 'fr');

            // Skip redirect if already on the target URL (prevents loops if cookies are blocked)
            if ($request->url() === $frUrl) {
                return $next($request)->cookie('preferred_locale', 'fr', 60 * 24 * 365);
            }

            return redirect($frUrl)->cookie('preferred_locale', 'fr', 60 * 24 * 365);
        }

        return $next($request)->cookie('preferred_locale', 'nl', 60 * 24 * 365);
    }

    private function resolveEquivalentUrl(Request $request, string $targetLocale): string
    {
        try {
            $routeName = $request->route()->getName();
            $targetRoute = preg_replace('/^(nl|fr)\./', $targetLocale.'.', $routeName);
            $params = $request->route()->parameters();

            return route($targetRoute, $params);
        } catch (\Exception) {
            return route($targetLocale.'.home');
        }
    }
}
