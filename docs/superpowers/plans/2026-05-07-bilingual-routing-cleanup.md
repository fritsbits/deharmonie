# Bilingual Routing Cleanup Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Move NL routes to `/nl` prefix (symmetric with `/fr`) and make `SetLocale` middleware infer locale from the URL instead of taking it as a parameter, without changing route names, view code, or adding a package dependency.

**Architecture:** Two unrolled `Route::prefix()` groups (one per locale) sharing a parameterless `SetLocale` middleware that reads `$request->segment(1)`. Bare `/` becomes a redirect endpoint backed by a new `LocaleController::detect()` action that does Accept-Language / cookie detection. The existing `DetectPreferredLocale` middleware class is deleted — its responsibilities collapse into the new controller action and disappear elsewhere.

**Tech Stack:** Laravel 13, PHP 8.4, PHPUnit 12. No new dependencies.

**Reference spec:** `docs/superpowers/specs/2026-05-07-bilingual-routing-cleanup-design.md`

---

## File Structure

**New:**
- `tests/Feature/RootRedirectTest.php` — replaces `DetectPreferredLocaleTest`. Covers the new `/` redirect behavior end-to-end.

**Modified:**
- `app/Http/Middleware/SetLocale.php` — drop the parameter, read locale from `$request->segment(1)`.
- `app/Http/Controllers/LocaleController.php` — add `detect()` action that handles Accept-Language and cookie-based locale detection, returns a redirect to the locale's home.
- `routes/web.php` — add `/nl` prefix to NL group, wire `/` to `LocaleController::detect`, drop the `:nl`/`:fr` middleware parameter syntax, prefix the stijlgids route with `/nl`.
- `bootstrap/app.php` — remove the `'locale'` middleware alias.
- `tests/Feature/BilingualRoutingTest.php` — update path literals (`/` → `/nl`, `/activiteiten/...` → `/nl/activiteiten/...`).
- `tests/Feature/StijlgidsTest.php` — update path literals (`/stijlgids` → `/nl/stijlgids`).

**Deleted:**
- `app/Http/Middleware/DetectPreferredLocale.php` — superseded by `LocaleController::detect()`.
- `tests/Feature/DetectPreferredLocaleTest.php` — superseded by `RootRedirectTest`.

**Unchanged (verified):**
- All Blade views — they keep `route(app()->getLocale() . '.foo')`. Route names are not changing, so the URLs they resolve to update automatically.
- All other feature tests using `route('nl.foo')` / `route('fr.foo')` — same reason.
- Filament admin (`/admin`) — outside the locale prefix groups.

---

## Task 1: Add `LocaleController::detect()` action and its test

**Why first:** new code with no impact on existing routes. Adding it before touching `routes/web.php` means we have a working detect action ready when we wire up `/` in Task 3.

**Files:**
- Modify: `app/Http/Controllers/LocaleController.php`
- Create: `tests/Feature/RootRedirectTest.php`

- [ ] **Step 1: Write the failing test for `RootRedirectTest`**

Create `tests/Feature/RootRedirectTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class RootRedirectTest extends TestCase
{
    public function test_dutch_browser_is_redirected_to_nl_home(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'nl-BE,nl;q=0.9'])
            ->get('/');

        $response->assertRedirect('/nl');
        $response->assertCookie('preferred_locale', 'nl');
    }

    public function test_french_browser_is_redirected_to_fr_home(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-BE,fr;q=0.9,nl;q=0.5'])
            ->get('/');

        $response->assertRedirect('/fr');
        $response->assertCookie('preferred_locale', 'fr');
    }

    public function test_preferred_locale_cookie_overrides_accept_language(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-BE'])
            ->withCookie('preferred_locale', 'nl')
            ->get('/');

        $response->assertRedirect('/nl');
    }

    public function test_unknown_accept_language_falls_back_to_nl(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'de-DE,de;q=0.9'])
            ->get('/');

        $response->assertRedirect('/nl');
        $response->assertCookie('preferred_locale', 'nl');
    }
}
```

- [ ] **Step 2: Run the test and verify it fails**

Run: `php artisan test --compact --filter=RootRedirectTest`
Expected: FAIL — the `/` route still hits the existing handler (which serves the NL homepage). Tests will fail because the response isn't a redirect.

- [ ] **Step 3: Add `detect()` method to `LocaleController`**

Modify `app/Http/Controllers/LocaleController.php` — add this method below `switch()`:

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

The `Request` and `RedirectResponse` imports are already present in the file from `switch()`.

- [ ] **Step 4: Verify the test still fails for the same reason**

Run: `php artisan test --compact --filter=RootRedirectTest`
Expected: FAIL — `detect()` exists but isn't wired to a route yet.

This is intentional: we're not wiring the route until Task 3 because the NL homepage at `/` is still the active behavior. The action is in place ahead of time so Task 3 can wire it in one move.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/LocaleController.php tests/Feature/RootRedirectTest.php
git commit -m "feat(locale): add LocaleController::detect for bare-root redirect"
```

---

## Task 2: Make `SetLocale` middleware parameterless (URL-segment-based)

**Why before route changes:** rewriting the middleware now, with backward-compatible behavior, lets Task 3 simplify `routes/web.php` cleanly. We accept the parameter for backwards compat during this task — the parameter is dropped entirely in Task 4 after `routes/web.php` no longer uses it.

**Files:**
- Modify: `app/Http/Middleware/SetLocale.php`
- Test: `tests/Feature/BilingualRoutingTest.php` (existing tests must still pass)

- [ ] **Step 1: Run existing tests to establish green baseline**

Run: `php artisan test --compact tests/Feature/BilingualRoutingTest.php`
Expected: all tests PASS (current behavior).

- [ ] **Step 2: Rewrite `SetLocale::handle` to infer from URL with optional parameter override**

Replace the contents of `app/Http/Middleware/SetLocale.php`:

```php
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
```

Key change: `$locale` is now nullable; when no parameter is passed, the middleware reads `$request->segment(1)`. Existing `middleware('locale:nl')` calls in `routes/web.php` continue to work because the parameter is honored when present. The `in_array` guard prevents setting locale to a random URL segment like `'admin'` or `'_dev'`.

- [ ] **Step 3: Run tests to verify backwards compatibility**

Run: `php artisan test --compact tests/Feature/BilingualRoutingTest.php`
Expected: all tests PASS — the parameterized callsites in `routes/web.php` still pass `'nl'` or `'fr'` and the middleware respects them.

- [ ] **Step 4: Commit**

```bash
git add app/Http/Middleware/SetLocale.php
git commit -m "refactor(locale): SetLocale middleware infers locale from URL when no arg passed"
```

---

## Task 3: Switch URL structure — add `/nl` prefix, wire `/` to detect, update path-literal tests

**This is the lockstep change.** `routes/web.php`, `bootstrap/app.php`, and the path-literal tests all change in the same task because they're tightly coupled: the moment `/` stops serving the NL homepage, every test that does `$this->get('/')` and asserts NL content must change.

**Files:**
- Modify: `routes/web.php`
- Modify: `bootstrap/app.php`
- Modify: `tests/Feature/BilingualRoutingTest.php`
- Modify: `tests/Feature/StijlgidsTest.php`

- [ ] **Step 1: Update `tests/Feature/BilingualRoutingTest.php` for the new URL structure (write tests first)**

Replace the existing test methods that hit `/` directly. The full replacement file:

```php
<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BilingualRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_in_nl(): void
    {
        $response = $this->get('/nl');
        $response->assertStatus(200);
        $response->assertSee('Activiteiten');
    }

    public function test_homepage_loads_in_fr(): void
    {
        $response = $this->get('/fr/activites');
        $response->assertStatus(200);
        $response->assertSee('Activités');
    }

    public function test_activity_detail_resolves_by_slug(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);
        $this->get('/nl/activiteiten/'.$activiteit->slug)->assertStatus(200);
        $this->get('/fr/activites/'.$activiteit->slug)->assertStatus(200);
    }

    public function test_nl_locale_set_on_nl_routes(): void
    {
        $this->get('/nl');
        $this->assertEquals('nl', app()->getLocale());
    }

    public function test_fr_locale_set_on_fr_routes(): void
    {
        $this->get('/fr/activites');
        $this->assertEquals('fr', app()->getLocale());
    }

    public function test_nav_shows_fr_labels_on_fr_routes(): void
    {
        $response = $this->get('/fr');
        $response->assertSee('Activités');
        $response->assertSee('À propos');
    }

    public function test_nav_shows_nl_labels_on_nl_routes(): void
    {
        $response = $this->get('/nl');
        $response->assertSee('Activiteiten');
        $response->assertSee('Over ons');
    }

    public function test_nl_nav_shows_fr_as_link(): void
    {
        $response = $this->get('/nl');
        $response->assertSee('set-locale', false);
        $response->assertSee('>FR<', false);
        $response->assertSee('>NL<', false);
    }

    public function test_fr_nav_shows_nl_as_link(): void
    {
        $response = $this->get('/fr');
        $response->assertSee('set-locale', false);
        $response->assertSee('>NL<', false);
        $response->assertSee('>FR<', false);
    }

    public function test_footer_shows_fr_labels_on_fr_routes(): void
    {
        $response = $this->get('/fr');
        $response->assertSee('Avec le soutien de');
        $response->assertSee('Facebook');
    }

    public function test_old_unprefixed_url_returns_404(): void
    {
        $this->get('/activiteiten')->assertStatus(404);
    }

    public function test_fr_prefix_with_nl_slug_returns_404(): void
    {
        // /fr/activiteiten doesn't exist — only /fr/activites does
        $this->get('/fr/activiteiten')->assertStatus(404);
    }
}
```

Changes from the original:
- `test_homepage_loads_in_nl`: `/` → `/nl`
- `test_activity_detail_resolves_by_slug`: `/activiteiten/...` → `/nl/activiteiten/...`
- `test_nl_locale_set_on_default_routes` renamed to `test_nl_locale_set_on_nl_routes`, body uses `/nl`
- `test_nav_shows_nl_labels_on_nl_routes`: `/` → `/nl`
- `test_nl_nav_shows_fr_as_link`: `/` → `/nl`
- Two new tests added: `test_old_unprefixed_url_returns_404` and `test_fr_prefix_with_nl_slug_returns_404`

- [ ] **Step 2: Update `tests/Feature/StijlgidsTest.php` for the new URL**

Replace path literals in `tests/Feature/StijlgidsTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class StijlgidsTest extends TestCase
{
    public function test_stijlgids_returns_200(): void
    {
        $response = $this->actingAs(User::factory()->make())->get('/nl/stijlgids');
        $response->assertStatus(200);
    }

    public function test_stijlgids_redirects_guests(): void
    {
        $this->get('/nl/stijlgids')->assertStatus(302);
    }

    public function test_stijlgids_has_all_section_anchors(): void
    {
        $response = $this->actingAs(User::factory()->make())->get('/nl/stijlgids');

        foreach ([
            'kleurenpalet', 'typografie', 'knoppen', 'formulieren', 'badges',
            'navigatie', 'hero', 'activiteitenlijst', 'activiteit-detail',
            'registratieformulier', 'diensten', 'voettekst',
        ] as $anchor) {
            $response->assertSee('id="' . $anchor . '"', false);
        }
    }

    public function test_stijlgids_is_noindex(): void
    {
        $response = $this->actingAs(User::factory()->make())->get('/nl/stijlgids');
        $response->assertSee('noindex', false);
    }
}
```

- [ ] **Step 3: Run the updated tests and verify they fail**

Run: `php artisan test --compact tests/Feature/BilingualRoutingTest.php tests/Feature/StijlgidsTest.php tests/Feature/RootRedirectTest.php`
Expected: FAIL — routes still serve the old structure (`/` is NL home, `/stijlgids` is NL, `/nl` doesn't exist).

- [ ] **Step 4: Rewrite `routes/web.php` with the new structure**

Replace the contents of `routes/web.php`:

```php
<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/set-locale/{locale}', [LocaleController::class, 'switch'])
    ->name('set-locale')
    ->where('locale', 'nl|fr');

// Bare root: detect preferred locale and redirect to /nl or /fr
Route::get('/', [LocaleController::class, 'detect'])->name('root');

// NL routes
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

// FR routes
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

// Stijlgids (internal design system reference — auth required, NL only)
Route::prefix('nl')->middleware([SetLocale::class, 'auth'])
    ->get('/stijlgids', fn () => view('stijlgids'))
    ->name('stijlgids');

// Categorie icon variants preview — temporary, for icon selection
Route::get('/_dev/icon-preview', fn () => view('dev.icon-preview'));
```

Changes from the original:
- `DetectPreferredLocale` import removed.
- `SetLocale` import added (was previously referenced via the `'locale'` alias).
- `Route::get('/', ...)` for the bare root now hits `LocaleController::detect`, named `root`.
- NL group gets `Route::prefix('nl')` and uses `middleware(SetLocale::class)` instead of `middleware(['locale:nl', DetectPreferredLocale::class])`.
- FR group uses `middleware(SetLocale::class)` instead of `middleware('locale:fr')`.
- Stijlgids route gets `Route::prefix('nl')` so it's served at `/nl/stijlgids`. Route name (`stijlgids`) is unchanged.

- [ ] **Step 5: Remove the `'locale'` middleware alias from `bootstrap/app.php`**

Modify `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo('/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

The `$middleware->alias([...])` block is removed in full. The `'locale'` alias was the only entry — `routes/web.php` now references `SetLocale::class` directly.

- [ ] **Step 6: Run all the affected tests**

Run: `php artisan test --compact tests/Feature/BilingualRoutingTest.php tests/Feature/StijlgidsTest.php tests/Feature/RootRedirectTest.php`
Expected: PASS — all three test files green.

- [ ] **Step 7: Run the full suite to catch any tests that hit literal paths I missed**

Run: `php artisan test --compact`
Expected: PASS for everything *except* `DetectPreferredLocaleTest.php`. That test will FAIL because:
- `test_dutch_browser_is_not_redirected` hits `/` and expects a 200 — `/` now redirects (302).
- `test_cookie_prevents_redirect_on_subsequent_visits` hits `/` and expects a 200 — same reason.
- `test_french_browser_on_activity_page_redirects_to_fr_equivalent` hits `/activiteiten/$slug` which is now a 404.

These failures are expected and resolved in Task 4 by deleting `DetectPreferredLocaleTest.php` entirely (its behavior is now covered by `RootRedirectTest`).

If any *other* test fails, stop and read the failure carefully. The most likely cause is a test that uses a literal path the spec didn't enumerate. Add the `/nl` prefix to that test before continuing.

- [ ] **Step 8: Commit**

```bash
git add routes/web.php bootstrap/app.php tests/Feature/BilingualRoutingTest.php tests/Feature/StijlgidsTest.php
git commit -m "refactor(routes): symmetric /nl + /fr prefixes, parameterless SetLocale"
```

---

## Task 4: Delete `DetectPreferredLocale` middleware and its test

**Why now:** Task 3 left the suite green except for `DetectPreferredLocaleTest`. The class file is unreferenced (its sole importer, `routes/web.php`, no longer imports it). Removing both is a clean delete.

**Files:**
- Delete: `app/Http/Middleware/DetectPreferredLocale.php`
- Delete: `tests/Feature/DetectPreferredLocaleTest.php`

- [ ] **Step 1: Verify nothing references `DetectPreferredLocale` outside the doomed files**

Run: `grep -rn "DetectPreferredLocale" /Users/frederikvincx/Herd/deharmonie --include="*.php" --exclude-dir=vendor`
Expected: only two matches — the class file itself and its test file. If anything else matches, stop and investigate before deleting.

- [ ] **Step 2: Delete the middleware class**

Run: `rm /Users/frederikvincx/Herd/deharmonie/app/Http/Middleware/DetectPreferredLocale.php`

- [ ] **Step 3: Delete the test file**

Run: `rm /Users/frederikvincx/Herd/deharmonie/tests/Feature/DetectPreferredLocaleTest.php`

- [ ] **Step 4: Run the full suite**

Run: `php artisan test --compact`
Expected: all tests PASS. No more failures from `DetectPreferredLocaleTest`, and `RootRedirectTest` covers the redirect behavior the deleted tests previously asserted.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Middleware/DetectPreferredLocale.php tests/Feature/DetectPreferredLocaleTest.php
git commit -m "refactor(locale): remove DetectPreferredLocale, replaced by LocaleController::detect"
```

---

## Task 5: Final cleanup — remove the optional `?string $locale` parameter from `SetLocale`

**Why now:** Task 2 left the parameter in for backwards compatibility while routes still passed `:nl`/`:fr`. After Task 3, no caller passes the parameter. We can drop it for clarity.

**Files:**
- Modify: `app/Http/Middleware/SetLocale.php`

- [ ] **Step 1: Verify no callsite passes a parameter to `SetLocale`**

Run: `grep -rEn "SetLocale[^.]|locale:" /Users/frederikvincx/Herd/deharmonie/routes /Users/frederikvincx/Herd/deharmonie/bootstrap --include="*.php"`
Expected: matches only `SetLocale::class` references in `routes/web.php` (no `:nl`, `:fr`, or `'locale:...'` strings remain).

- [ ] **Step 2: Simplify `SetLocale::handle` signature**

Replace the contents of `app/Http/Middleware/SetLocale.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1);

        if (in_array($locale, ['nl', 'fr'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
```

The `?string $locale = null` parameter and the null-coalesce are gone. Behavior is unchanged for the actual callsites.

- [ ] **Step 3: Run the full suite**

Run: `php artisan test --compact`
Expected: all tests PASS.

- [ ] **Step 4: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`
Expected: no errors. May reformat.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Middleware/SetLocale.php
git commit -m "refactor(locale): drop SetLocale parameter, URL segment is sole source"
```

---

## Task 6: Manual smoke test in the browser

**Why:** UI behavior — the CLAUDE.md instruction says "for UI or frontend changes, start the dev server and use the feature in a browser before reporting the task as complete." Tests cover routing and redirects but not "does the language switcher actually flip from /nl/over-ons to /fr/a-propos when I click it."

The site is served at `https://deharmonie.test` by Herd — no `php artisan serve` needed.

- [ ] **Step 1: Verify `/` redirects to `/nl` or `/fr` based on browser language**

Open a fresh incognito window. Visit `https://deharmonie.test/`. Should land on `/nl` (assuming the browser sends an NL-leaning Accept-Language) or `/fr`.

- [ ] **Step 2: Visit `/nl` and `/fr` directly**

`https://deharmonie.test/nl` → Dutch homepage.
`https://deharmonie.test/fr` → French homepage.

- [ ] **Step 3: Click around the nav on both locales**

On `/nl`: click each nav link (Activiteiten, Restaurant Menu, Over ons, Contact). Each should resolve under `/nl/...`.
On `/fr`: same — each should resolve under `/fr/...`.

- [ ] **Step 4: Test the language switcher**

From `/nl/over-ons`, click "FR" in the nav. Should land on `/fr/a-propos`. Click "NL" back. Should land on `/nl/over-ons`.

- [ ] **Step 5: Verify old URLs are now 404**

`https://deharmonie.test/activiteiten` → 404.
`https://deharmonie.test/over-ons` → 404.

- [ ] **Step 6: Verify Filament admin is unaffected**

`https://deharmonie.test/admin` → admin login screen still works. No locale prefix.

- [ ] **Step 7: If everything looks right, no commit needed (no code changed)**

If you find a bug during smoke testing, write a test for it first, fix it, commit. Don't ship a fix without a regression test.

---

## Self-Review

**Spec coverage:**
- Symmetric URLs (`/nl` + `/fr`) — Task 3, Step 4 (`routes/web.php`).
- `SetLocale` parameterless, URL-segment-based — Tasks 2 (initial) and 5 (final).
- Bare `/` redirects via Accept-Language — Task 1 (controller action) + Task 3 (route wiring) + Task 1's test (`RootRedirectTest`).
- `'locale'` alias removed from `bootstrap/app.php` — Task 3, Step 5.
- `DetectPreferredLocale` middleware deleted — Task 4.
- Existing tests using `route('nl.foo')` / `route('fr.foo')` continue to pass — verified by Task 3, Step 7 running the full suite.
- Path-literal tests updated (`BilingualRoutingTest`, `StijlgidsTest`) — Task 3, Steps 1–2.
- Old unprefixed URLs return 404 — covered by `test_old_unprefixed_url_returns_404` in Task 3, Step 1.
- `/fr/activiteiten` returns 404 — covered by `test_fr_prefix_with_nl_slug_returns_404` in Task 3, Step 1.
- Stijlgids route prefixed `/nl` — Task 3, Step 4 (route file) + Step 2 (test).
- Filament `/admin` unaffected — verified by smoke test, Task 6, Step 6.
- No package, no view changes, no helper, no UrlGenerator subclass — none of these tasks introduce them.

**Placeholder scan:** none. Every step has actual code or actual commands.

**Type/method consistency:**
- `LocaleController::detect()` signature `(Request $request): RedirectResponse` matches its usage in routes/web.php (`[LocaleController::class, 'detect']`).
- `SetLocale::handle()` evolves: `(Request, Closure, ?string $locale = null): Response` in Task 2 → `(Request, Closure): Response` in Task 5. No callsite breaks because Task 3 stops passing the parameter before Task 5 removes it.
- Route names referenced by tests (`nl.home`, `fr.home`, `nl.activiteiten.index` etc.) match the names in the rewritten `routes/web.php`.

No issues found.
