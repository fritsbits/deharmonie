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
2. Migration of all inline ternaries to `__()` translation strings
3. A `/set-locale/{locale}` route that powers the language switcher and persists preference

---

## 1. Browser Locale Detection & Redirect

### Behaviour

- First-time visitors to any NL route (no `preferred_locale` cookie) are inspected via `Accept-Language` header
- If the top-ranked language starts with `fr`, redirect to the FR equivalent of the requested page and set a `preferred_locale=fr` cookie (1 year)
- Otherwise, set `preferred_locale=nl` and continue — no redirect
- On subsequent visits, the cookie bypasses detection entirely
- Manual language switching (via nav) also sets the cookie, so user intent is respected

### Implementation

A new `DetectPreferredLocale` middleware:

```php
// app/Http/Middleware/DetectPreferredLocale.php
public function handle(Request $request, Closure $next): Response
{
    if ($request->cookie('preferred_locale')) {
        return $next($request);
    }

    $lang = substr($request->header('Accept-Language', 'nl'), 0, 2);

    if ($lang === 'fr') {
        $frUrl = $this->resolveEquivalentUrl($request, 'fr');
        return redirect($frUrl)->cookie('preferred_locale', 'fr', 60 * 24 * 365);
    }

    return $next($request)->cookie('preferred_locale', 'nl', 60 * 24 * 365);
}
```

Registered only on the NL route group (adding it to FR routes would cause redirect loops).

### Equivalent URL resolution

Route names follow the pattern `{locale}.{page}` (e.g. `nl.activiteiten.index` ↔ `fr.activiteiten.index`). The middleware swaps the locale prefix and passes through any route parameters (e.g. activity `{slug}`) unchanged.

---

## 2. Translation String Migration

### Lang file structure

Existing files are kept; new files are added:

```
lang/
  nl/
    nav.php        (exists — expand with missing keys)
    activities.php (exists)
    forms.php      (exists)
    pages.php      (new — page-level content)
    common.php     (new — shared labels, eyebrows, CTAs)
  fr/
    nav.php        (exists — expand)
    activities.php (exists)
    forms.php      (exists)
    pages.php      (new)
    common.php     (new)
```

### Usage pattern

All inline ternaries in views are replaced:

```blade
{{-- Before --}}
{{ app()->getLocale() === 'fr' ? 'Activités' : 'Activiteiten' }}

{{-- After --}}
{{ __('nav.activities') }}
```

For arrays (e.g. the services list in `diensten.blade.php`):

```blade
{{-- Before --}}
@php $services = app()->getLocale() === 'fr' ? [...] : [...]; @endphp

{{-- After --}}
@php $services = __('pages.diensten_services'); @endphp
```

### Scope of views to migrate

- `resources/views/components/nav.blade.php`
- `resources/views/components/footer.blade.php`
- `resources/views/pages/diensten.blade.php`
- `resources/views/pages/wie-is-wie.blade.php`
- `resources/views/activiteiten/index.blade.php`
- `resources/views/activiteiten/show.blade.php`
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

### Controller logic

1. Validate `locale` is `nl` or `fr`
2. Set `preferred_locale` cookie (1 year)
3. Resolve the equivalent page in the target locale from the `redirect` query parameter or current route name
4. Redirect there

```php
public function switch(Request $request, string $locale): RedirectResponse
{
    $redirect = $request->query('redirect', '/');
    $response = redirect($this->resolveLocaleUrl($redirect, $locale));
    return $response->cookie('preferred_locale', $locale, 60 * 24 * 365);
}
```

### Nav wiring

```blade
<a href="{{ route('set-locale', ['locale' => app()->getLocale() === 'nl' ? 'fr' : 'nl', 'redirect' => url()->current()]) }}">
    {{ __('nav.language_switch') }}
</a>
```

---

## What is not in scope

- Translation of activity content stored in the database (already handled by model accessors)
- Admin panel (Filament) — Dutch only is fine for staff
- Any new pages not currently in the codebase

---

## Success criteria

1. A visitor with a French browser landing on `/` is redirected to `/fr` on their first visit
2. A visitor who manually switches language is not redirected back on subsequent visits
3. All NL/FR strings in views come from `__()` calls — no inline ternaries remain
4. The language switcher link in the nav works correctly on every page including activity detail pages with slugs
