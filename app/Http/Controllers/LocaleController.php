<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $redirect = $request->query('redirect', '/');

        $parsed = parse_url($redirect);
        if (! empty($parsed['host']) && $parsed['host'] !== $request->getHost()) {
            $redirect = '/';
        }

        return redirect($redirect)
            ->cookie('preferred_locale', $locale, 60 * 24 * 365);
    }

    public function detect(Request $request): RedirectResponse
    {
        $cookie = $request->cookie('preferred_locale');
        if (in_array($cookie, ['nl', 'fr'], true)) {
            return redirect()->route("$cookie.home");
        }

        $firstTag = explode(',', $request->header('Accept-Language', 'nl'))[0];
        $lang = substr(trim($firstTag), 0, 2);
        $locale = $lang === 'fr' ? 'fr' : 'nl';

        return redirect()->route("$locale.home")
            ->cookie('preferred_locale', $locale, 60 * 24 * 365);
    }
}
