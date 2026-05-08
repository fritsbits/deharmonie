# Bilingual routing cleanup

**Date:** 2026-05-07
**Status:** Design — pending user review

## Problem

Two parts of the current bilingual routing setup were flagged as feeling unconventional and worth cleaning up:

1. **Asymmetric URL structure.** NL routes have no prefix (`/activiteiten`, `/over-ons`); FR routes have `/fr` (`/fr/activites`, `/fr/a-propos`). Every locale-aware operation has to special-case "is this NL or not?", and the structure is non-obvious to a new contributor.
2. **`SetLocale` middleware takes the locale as a parameter.** Each route group declares `middleware('locale:nl')` or `middleware('locale:fr')`, baking the locale into route registration rather than inferring it from the URL the request is on. This is explicit, but unconventional — most Laravel apps with locale-aware routing read the locale from the URL once, not pass it as a route-level constant.

Out of scope for this refactor (intentionally not changed):

- The pattern `route(app()->getLocale() . '.foo')` in views remains — refactoring all 28 callsites would either require a UrlGenerator subclass (un-standard) or a global helper function (parallel API). The user prefers to keep the verbose-but-standard view code over either alternative.
- Per-locale named routes (`nl.foo`, `fr.foo`) stay. There is no single-name-per-page abstraction.
- The two route registration blocks remain unrolled (no `foreach` loop). The user prefers the boring repetition over the loop's mental overhead.

## Goals

- Symmetric URLs: NL becomes `/nl/...`, FR remains `/fr/...`.
- `SetLocale` middleware no longer takes a parameter. It infers locale from the URL.
- Bare `/` redirects to `/nl/...` or `/fr/...` based on Accept-Language (current first-visit UX preserved).
- All existing tests that go through `route('nl.foo')` / `route('fr.foo')` continue to pass without modification (the route names are unchanged, only the URLs they resolve to change).

## Non-goals

- No 301 redirects from old unprefixed URLs. Existing inbound links to `/activiteiten/...` will return 404. (User decision: site is community-driven, SEO equity on these URLs is low, and they prefer cleaner code over a redirect layer.)
- No package dependency. `mcamara/laravel-localization` and `niels-numbers/laravel-localizer` were considered and rejected.
- No view changes. The `route(app()->getLocale() . '.foo')` pattern is kept.
- No `lroute()` helper or `UrlGenerator` subclass.
- Filament admin (`/admin`) is unaffected — it lives outside the locale prefix groups.
- The language-switcher controller (`LocaleController::switch`) keeps its current behavior. The cross-locale URL resolution in `nav.blade.php` continues to work because `route('nl.foo')` ↔ `route('fr.foo')` swap still produces a correct URL after the prefix change.

## Architecture

### Route registration (`routes/web.php`)

Two unrolled route groups, each with `Route::prefix($locale)` and `middleware(SetLocale::class)`. The middleware no longer receives an argument.

```php
Route::get('/set-locale/{locale}', [LocaleController::class, 'switch'])
    ->name('set-locale')
    ->where('locale', 'nl|fr');

// Bare root: detect preferred locale and redirect
Route::get('/', [LocaleController::class, 'detect'])->name('root');

// NL group
Route::prefix('nl')->middleware(SetLocale::class)->group(function () {
    Route::get('/', [ActivityController::class, 'home'])->name('nl.home');
    Route::get('/activiteiten', [ActivityController::class, 'index'])->name('nl.activiteiten.index');
    Route::get('/activiteiten/agenda', [ActivityController::class, 'agenda'])->name('nl.activiteiten.agenda');
    Route::get('/activiteiten/{slug}', [ActivityController::class, 'show'])->name('nl.activiteiten.show');
    Route::get('/restaurant-menu', [PageController::class, 'weekmenu'])->name('nl.weekmenu');
    Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('nl.weekmenu.print');
    Route::get('/over-ons', [PageController::class, 'overOns'])->name('nl.over-ons');
    Route::get('/contact', [PageController::class, 'contact'])->name('nl.contact');
    Route::get('/vrijwilligers', [PageController::class, 'vrijwilligers'])->name('nl.vrijwilligers');
    Route::get('/wie-is-wie', [PageController::class, 'wieIsWie'])->name('nl.wie-is-wie');
});

// FR group
Route::prefix('fr')->middleware(SetLocale::class)->group(function () {
    Route::get('/', [ActivityController::class, 'home'])->name('fr.home');
    Route::get('/activites', [ActivityController::class, 'index'])->name('fr.activiteiten.index');
    Route::get('/activites/agenda', [ActivityController::class, 'agenda'])->name('fr.activiteiten.agenda');
    Route::get('/activites/{slug}', [ActivityController::class, 'show'])->name('fr.activiteiten.show');
    Route::get('/restaurant-menu', [PageController::class, 'weekmenu'])->name('fr.weekmenu');
    Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('fr.weekmenu.print');
    Route::get('/a-propos', [PageController::class, 'overOns'])->name('fr.over-ons');
    Route::get('/contact', [PageController::class, 'contact'])->name('fr.contact');
    Route::get('/benevoles', [PageController::class, 'vrijwilligers'])->name('fr.vrijwilligers');
    Route::get('/qui-est-qui', [PageController::class, 'wieIsWie'])->name('fr.wie-is-wie');
});

// Stijlgids (auth-required, NL only)
Route::middleware([SetLocale::class, 'auth'])
    ->prefix('nl')
    ->get('/stijlgids', fn () => view('stijlgids'))
    ->name('stijlgids');

// Dev-only icon preview (no locale)
Route::get('/_dev/icon-preview', fn () => view('dev.icon-preview'));
```

Notes:

- The `/stijlgids` route gains a `/nl` prefix to match the new structure. Its name (`stijlgids`) is unchanged.
- The dev icon preview is unaffected — it has no locale concern.
- Route names (`nl.foo`, `fr.foo`) are unchanged. Tests using `route('nl.over-ons')` etc. resolve to `/nl/over-ons` automatically.

### `SetLocale` middleware

```php
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->segment(1);
        if (in_array($locale, ['nl', 'fr'], true)) {
            app()->setLocale($locale);
        }
        return $next($request);
    }
}
```

- No parameter.
- The `in_array` guard means non-locale paths (admin, livewire, the `/up` health check, the dev icon preview) leave `app()->getLocale()` at its default — the middleware is a no-op when the URL doesn't start with a known locale segment.
- For `/set-locale/{locale}` the segment(1) is `set-locale`, not `nl`/`fr`, so the middleware is a no-op there too. That route doesn't need locale set anyway — it just sets a cookie and redirects.

### `bootstrap/app.php`

Remove the `'locale'` middleware alias. It's no longer parameterized, so no alias is needed:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->redirectGuestsTo('/admin/login');
})
```

`SetLocale::class` is referenced directly in `routes/web.php`.

### Bare `/` behavior

The current `DetectPreferredLocale` middleware runs on the NL group and is responsible for redirecting first-visit FR browsers to `/fr/...`. After this refactor, `/` is no longer a real page — it becomes a single route that resolves to a redirect.

Move detection logic into `LocaleController::detect()`:

```php
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
```

The existing `DetectPreferredLocale` middleware class is deleted — its responsibilities collapse into `LocaleController::detect()` (for the bare `/`) and disappear elsewhere (no need to detect once the URL is already localized).

The `resolveEquivalentUrl` helper inside `DetectPreferredLocale` is also dropped: it was used to map an NL URL to the equivalent FR URL when the middleware redirected mid-navigation. With detection only running on `/`, there's nothing to remap — we just route to the locale's home.

### Language switcher

`LocaleController::switch` is unchanged. It takes a `?redirect=` query param, validates it, sets the cookie, and redirects.

The nav (`resources/views/components/nav.blade.php:39-47`) computes the cross-locale equivalent URL by swapping the `nl.` / `fr.` prefix on the current route name and calling `route()` with the same parameters. This still works after the refactor because route names are unchanged — `route('fr.over-ons')` simply resolves to `/fr/a-propos` instead of (the same) `/fr/a-propos`. No changes needed.

Edge case: when on `/` (the bare root, named `root`), the nav's `preg_replace('/^(nl|fr)\./', ...)` does not match, so `$targetRoute` stays as `'root'` and `route('root')` returns `/` — clicking the language switch from `/` lands you back on `/`, which then re-detects via Accept-Language. In practice users won't see the nav on `/` because it redirects immediately, but the behavior is benign if they do.

Implementation note for `LocaleController::detect()`: the `/` route does not run `SetLocale` middleware (it is not under a locale prefix), so `app()->getLocale()` is at the framework default during this action. The redirect uses route names (`nl.home` / `fr.home`) directly, not the current locale, so this does not matter — but a reviewer should not be surprised that the action runs without locale context set.

## Data flow

| URL | Middleware | `app()->getLocale()` | Resolves to |
|---|---|---|---|
| `/` | (none) | default (nl) | redirect to `/nl` or `/fr` based on cookie/Accept-Language |
| `/nl` | `SetLocale` | `nl` | `nl.home` → ActivityController::home |
| `/nl/activiteiten` | `SetLocale` | `nl` | `nl.activiteiten.index` |
| `/fr/activites` | `SetLocale` | `fr` | `fr.activiteiten.index` |
| `/fr/activiteiten` | `SetLocale` | `fr` | 404 (the slug doesn't exist under FR) |
| `/activiteiten` (old) | (none) | default | 404 (no route registered) |
| `/admin` | (none) | default | Filament |
| `/set-locale/fr?redirect=/nl/over-ons` | (none) | default | sets cookie, redirects to `/nl/over-ons` |

## Tests

### Existing tests that need updating

Tests that hit unprefixed NL paths directly (not via `route()`) need to add the `/nl` prefix:

- `BilingualRoutingTest::test_homepage_loads_in_nl` — `$this->get('/')` followed by `assertSee('Activiteiten')` becomes `$this->get('/nl')` and assert. Add a separate test for the `/` redirect behavior.
- `BilingualRoutingTest::test_activity_detail_resolves_by_slug` — `/activiteiten/$slug` → `/nl/activiteiten/$slug`.
- `BilingualRoutingTest::test_nl_locale_set_on_default_routes` — `$this->get('/')` → `$this->get('/nl')`. The `/` test moves to the new redirect-test bucket.
- `BilingualRoutingTest::test_nav_shows_nl_labels_on_nl_routes` — `$this->get('/')` → `$this->get('/nl')`.
- `BilingualRoutingTest::test_nl_nav_shows_fr_as_link` — same.
- Any test using `$this->get('/activiteiten/...')` literally.

Tests that go through `route('nl.foo')` need no changes — the route name still exists, only the URL it resolves to changed:

- `OverOnsPageTest`, `WieIsWiePageTest`, `VrijwilligersPageTest`, `RegistrationFormTest`, `ActivityControllerTest::test_homepage_*` (line 203), `ErrorPagesTest`, `AgendaClickableRowsTest` — all unchanged.

### New tests

- `RootRedirectTest::test_root_redirects_to_nl_for_dutch_browser`
- `RootRedirectTest::test_root_redirects_to_fr_for_french_browser`
- `RootRedirectTest::test_root_respects_preferred_locale_cookie`
- `RootRedirectTest::test_root_sets_preferred_locale_cookie_on_redirect`
- `BilingualRoutingTest::test_unprefixed_old_url_returns_404` — assert that `/activiteiten/foo` 404s (regression guard against accidentally re-introducing).
- `BilingualRoutingTest::test_fr_prefix_with_nl_slug_returns_404` — `/fr/activiteiten` should 404 (the slug only exists under NL).

### Tests to delete

- `DetectPreferredLocaleTest` — the middleware is gone; its behavior is now covered by `RootRedirectTest`.

## Migration / rollout considerations

This is a breaking URL change. Bookmarks, external inbound links, and search engine entries pointing at unprefixed NL URLs will 404 after deploy. Per the user's explicit decision, this is accepted. Communicate it to the user once more before deploying so there is no ambiguity.

No data migrations are needed. No env or config changes besides removing the `'locale'` middleware alias.

## Decisions log

- **Symmetric URLs (NL gets `/nl` prefix):** chosen over keeping NL unprefixed. Eliminates the special-case branching the user found awkward.
- **`SetLocale` reads from URL segment, no parameter:** chosen over keeping the parameter or moving to a single `{locale}` route placeholder. Direct, idiomatic, and the `Route::prefix($locale)` already encodes which locale a group serves.
- **Two unrolled route blocks, not a `foreach` loop:** chosen for readability. Loops over locales with closure-captured `$locale` and `__('routes.foo', [], $locale)` were considered and rejected as too clever for the gain.
- **No package:** evaluated `mcamara/laravel-localization` (rejected: breaks `route:cache`, maintainer recommends moving away) and `niels-numbers/laravel-localizer` (rejected: user prefers no dependency). User accepts the cost of writing route registration twice.
- **No `lroute()` helper or `UrlGenerator` subclass:** views keep `route(app()->getLocale() . '.foo')`. The pattern is verbose but standard Laravel; alternatives introduce either a parallel API or a framework-extension surface.
- **No 301 redirects from old URLs:** old unprefixed URLs return 404. User accepts the inbound-link breakage in exchange for not maintaining a redirect layer.
- **Bare `/` redirects via Accept-Language:** preserves current first-visit UX. The detection logic moves from a middleware on the NL group to a controller action on `/`.
