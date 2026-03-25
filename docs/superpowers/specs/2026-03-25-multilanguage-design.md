# Multi-Language Design Spec
Date: 2026-03-25

## Problem

The site is bilingual (NL/FR) and the route/middleware infrastructure is already correct, but:

1. Views use inline ternaries (`app()->getLocale() === 'fr' ? ... : ...`) instead of the `__()` translation helper and existing lang files
2. New visitors always land on the NL version — there is no browser locale detection
3. The language switcher in the nav is not wired up to remember the user's preference

## Scope

This spec covers three changes:

1. Browser locale auto-detection with cookie memory
2. Migration of all inline ternaries to `__()` / `trans()` translation strings
3. A `/set-locale/{locale}` route that powers the language switcher and persists preference

---

## 1. Browser Locale Detection & Redirect

### Behaviour

- First-time visitors to any NL route (no `preferred_locale` cookie) are inspected via `Accept-Language` header
- If the top-ranked language starts with `fr`, redirect to the FR equivalent of the requested page and set a `preferred_locale=fr` cookie (1 year)
- Otherwise, set `preferred_locale=nl` and continue — no redirect
- On subsequent visits, the cookie bypasses detection entirely
- Manual language switching (via nav) also sets the cookie, so user intent is respected
- Visitors who arrive directly at a `/fr/...` URL (e.g. via a shared link) do not get a cookie set on that request. On their next visit to the NL root, the middleware will fire and redirect them to FR again — this is acceptable behaviour and requires no special handling

### Middleware registration

`DetectPreferredLocale` is registered on the **NL route group only**. Because `stijlgids` is defined outside the NL group (as a standalone `Route::middleware('locale:nl')` call), it is automatically excluded.

Register inline on the NL route group in `routes/web.php`:

```php
// routes/web.php
Route::middleware(['locale:nl', DetectPreferredLocale::class])->group(function () {
    // NL routes...
});
```

### Cookie notes

The `preferred_locale` cookie contains only `'nl'` or `'fr'` — no sensitive data. Laravel encrypts cookies by default, but `$request->cookie('preferred_locale')` reads through Laravel's request object which handles decryption automatically, so no changes to `EncryptCookies` are needed.

### Equivalent URL resolution

FR routes use different path segments, not just a `/fr` prefix:

| NL path | FR path |
|---|---|
| `/` | `/fr` |
| `/activiteiten` | `/fr/activites` |
| `/activiteiten/{slug}` | `/fr/activites/{slug}` |
| `/activiteiten/{slug}/print` | `/fr/activites/{slug}/imprimer` |
| `/diensten` | `/fr/services` |
| `/weekmenu` | `/fr/menu-semaine` |
| `/contact` | `/fr/contact` |
| `/wie-is-wie` | `/fr/qui-est-qui` |

Note: `overzicht.blade.php` exists as a view but does not yet have a route. It is out of scope for this spec.

**Simple string/prefix replacement will not work.** Resolution must use named routes.

Route names follow the pattern `{locale}.{page}` (e.g. `nl.activiteiten.index` ↔ `fr.activiteiten.index`, `nl.home` ↔ `fr.home`). The middleware:

1. Gets the current named route: `$request->route()->getName()` → e.g. `nl.activiteiten.show`
2. Swaps the locale prefix using regex: `preg_replace('/^(nl|fr)\./', 'fr.', $routeName)` → `fr.activiteiten.show`
3. Gets current route parameters: `$request->route()->parameters()` → `['slug' => 'yoga-voor-senioren']`
4. Generates the target URL: `route('fr.activiteiten.show', $params)` → `/fr/activites/yoga-voor-senioren`

If `route($targetRoute, $params)` throws (e.g. the target locale route doesn't exist), fall back to the locale's home page:

```php
// app/Http/Middleware/DetectPreferredLocale.php
public function handle(Request $request, Closure $next): Response
{
    if ($request->cookie('preferred_locale')) {
        return $next($request);
    }

    // Take the first tag from the header (ignoring quality weights), then check language prefix.
    // e.g. "fr-BE,fr;q=0.9,nl;q=0.8" → "fr-BE" → "fr"
    $firstTag = explode(',', $request->header('Accept-Language', 'nl'))[0];
    $lang = substr(trim($firstTag), 0, 2);

    if ($lang === 'fr') {
        $frUrl = $this->resolveEquivalentUrl($request, 'fr');
        return redirect($frUrl)->cookie('preferred_locale', 'fr', 60 * 24 * 365);
    }

    return $next($request)->cookie('preferred_locale', 'nl', 60 * 24 * 365);
}

private function resolveEquivalentUrl(Request $request, string $targetLocale): string
{
    try {
        $routeName = $request->route()->getName();
        $targetRoute = preg_replace('/^(nl|fr)\./', $targetLocale . '.', $routeName);
        $params = $request->route()->parameters();
        return route($targetRoute, $params);
    } catch (\Exception) {
        return route($targetLocale . '.home');
    }
}
```

---

## 2. Translation String Migration

### Lang file structure

Existing files are kept and expanded; new files are added:

```
lang/
  nl/
    nav.php        (exists — expand with missing keys)
    activities.php (exists)
    forms.php      (exists)
    pages.php      (new — page-level content strings and arrays)
    common.php     (new — shared labels, eyebrows, CTAs)
  fr/
    nav.php        (exists — expand)
    activities.php (exists)
    forms.php      (exists)
    pages.php      (new)
    common.php     (new)
```

### Usage pattern — simple strings

```blade
{{-- Before --}}
{{ app()->getLocale() === 'fr' ? 'Activités' : 'Activiteiten' }}

{{-- After --}}
{{ __('nav.activities') }}
```

### Usage pattern — arrays

Lang files can return nested arrays. Use `trans()` (not `__()`) when the return value is an array, since `__()` may coerce it to a string in some Laravel versions:

```php
// lang/nl/pages.php
return [
    'diensten_services' => [
        'Wegwijs in socio-cultureel Brussel — Sociale dienst',
        'Partner in het eerstelijnszorgnetwerk in de Noordwijk',
        // ...
    ],
];
```

```blade
{{-- Before --}}
@php $services = app()->getLocale() === 'fr' ? [...] : [...]; @endphp

{{-- After --}}
@php $services = trans('pages.diensten_services'); @endphp
```

### Scope of views to migrate

- `resources/views/components/nav.blade.php`
- `resources/views/components/footer.blade.php`
- `resources/views/pages/diensten.blade.php`
- `resources/views/pages/wie-is-wie.blade.php`
- `resources/views/activiteiten/index.blade.php`
- `resources/views/activiteiten/show.blade.php`
- `resources/views/activiteiten/print.blade.php`
- `resources/views/livewire/activity-filter.blade.php`
- `resources/views/livewire/registration-form.blade.php`

Note: `Activiteit` model accessors (`getTitelAttribute()`, etc.) pull from DB columns (`titel_nl`/`titel_fr`) and are not affected by this migration.

---

## 3. Language Switcher (`/set-locale/{locale}`)

### Route

Added outside both locale groups (no locale middleware):

```php
Route::get('/set-locale/{locale}', [LocaleController::class, 'switch'])
    ->name('set-locale')
    ->where('locale', 'nl|fr');
```

### How the target URL is resolved

The nav blade template pre-resolves the target-locale URL before linking to `/set-locale`. It swaps the locale prefix in the current named route, then calls `route()` with the current parameters. If the route name is unavailable (edge case on unnamed routes), it falls back to the target locale's home page.

Note: the `stijlgids` route name has no locale prefix (`stijlgids`, not `nl.stijlgids`), so the `preg_replace` swap produces an invalid route name. The `try/catch` fallback in the nav snippet handles this gracefully — the language switcher on that page will fall back to the target locale's home page. This is acceptable since `stijlgids` is an internal tool not linked publicly.

The `redirect` query parameter passed to `/set-locale` is the **already-resolved target URL** in the destination locale — not the current URL. The controller does not need to perform any URL translation itself.

```blade
{{-- nav.blade.php --}}
@php
    $targetLocale  = app()->getLocale() === 'nl' ? 'fr' : 'nl';
    $currentName   = request()->route()?->getName();
    $targetRoute   = $currentName
        ? preg_replace('/^(nl|fr)\./', $targetLocale . '.', $currentName)
        : $targetLocale . '.home';
    try {
        $targetUrl = route($targetRoute, request()->route()?->parameters() ?? []);
    } catch (\Exception) {
        $targetUrl = route($targetLocale . '.home');
    }
@endphp
<a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}">
    {{ __('nav.language_switch') }}
</a>
```

### Controller logic

The `redirect` parameter is user-supplied and must be validated to prevent open redirects. Only allow relative URLs or URLs on the same host:

```php
// app/Http/Controllers/LocaleController.php
public function switch(Request $request, string $locale): RedirectResponse
{
    $redirect = $request->query('redirect', '/');

    // Prevent open redirect: only allow relative paths or same host
    $parsed = parse_url($redirect);
    if (!empty($parsed['host']) && $parsed['host'] !== $request->getHost()) {
        $redirect = '/';
    }

    return redirect($redirect)
        ->cookie('preferred_locale', $locale, 60 * 24 * 365);
}
```

The locale itself is validated by the route constraint (`where('locale', 'nl|fr')`).

---

## What is not in scope

- Translation of activity content stored in the database (already handled by model accessors)
- Admin panel (Filament) — Dutch only is fine for staff
- The `overzicht` view (no route exists yet)
- Any new pages not currently in the codebase

---

## Success criteria

1. A visitor with a French browser landing on `/` is redirected to `/fr` on their first visit
2. A visitor who manually switches language is not redirected back on subsequent visits
3. All NL/FR strings in views come from `__()` / `trans()` calls — no inline ternaries remain
4. The language switcher link in the nav works correctly on every page including activity detail and print pages with slugs
5. The language switcher controller rejects redirects to external hosts
6. A route resolution failure in the middleware or nav template falls back to the target locale's home page, not an exception
