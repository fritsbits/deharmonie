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
        if (!empty($parsed['host']) && $parsed['host'] !== $request->getHost()) {
            $redirect = '/';
        }

        return redirect($redirect)
            ->cookie('preferred_locale', $locale, 60 * 24 * 365);
    }
}
