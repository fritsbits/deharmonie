# Multi-Language Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add browser locale auto-detection, wire the language switcher, and replace all inline `getLocale()` ternaries with `__()` / `trans()` translation calls.

**Architecture:** A `DetectPreferredLocale` middleware on the NL route group reads the `Accept-Language` header on first visit and redirects French speakers to `/fr`. A `LocaleController` + `/set-locale/{locale}` route sets a 1-year cookie and redirects to a pre-resolved target URL. All views are migrated to use existing/new lang file keys.

**Tech Stack:** Laravel 13, PHP 8.3, `lang/nl/` and `lang/fr/` PHP array translation files, Blade templates, Livewire 3, PHPUnit feature tests.

**Spec:** `docs/superpowers/specs/2026-03-25-multilanguage-design.md`

---

## File Map

**Create:**
- `app/Http/Middleware/DetectPreferredLocale.php`
- `app/Http/Controllers/LocaleController.php`
- `lang/nl/common.php`
- `lang/fr/common.php`
- `lang/nl/pages.php`
- `lang/fr/pages.php`
- `tests/Feature/LocaleSwitchTest.php`
- `tests/Feature/DetectPreferredLocaleTest.php`

**Modify:**
- `routes/web.php` — add contact routes, DetectPreferredLocale to NL group, set-locale route
- `tests/Feature/BilingualRoutingTest.php` — add per-task assertions (file already exists)
- `lang/nl/nav.php` — add `wie_is_wie`
- `lang/fr/nav.php` — add `wie_is_wie`
- `lang/nl/activities.php` — add `at`, `label`, `all`
- `lang/fr/activities.php` — add `at`, `label`, `all`
- `lang/nl/forms.php` — add `message_label`
- `lang/fr/forms.php` — add `message_label`
- `resources/views/components/nav.blade.php`
- `resources/views/components/footer.blade.php`
- `resources/views/livewire/registration-form.blade.php`
- `resources/views/livewire/activity-filter.blade.php`
- `resources/views/activiteiten/show.blade.php`
- `resources/views/pages/diensten.blade.php`
- `resources/views/pages/wie-is-wie.blade.php`
- `resources/views/activiteiten/index.blade.php`

**Not touched:**
- `resources/views/activiteiten/print.blade.php` — already fully migrated to `__('activities.*')`
- `app/Models/Activiteit.php` — model accessors use DB columns directly, correct as-is

---

## Task 0: Add missing contact routes to routes/web.php

**File:** `routes/web.php`

The `PageController::contact()` method exists but `/contact` routes are missing from both locale groups. The nav template currently references `nl.contact` and `fr.contact`, so these must be added first.

- [ ] **Step 1: Add the contact route to the NL group**

In the NL `Route::middleware(...)` group, add after the `wie-is-wie` route:

```php
Route::get('/contact', [PageController::class, 'contact'])->name('nl.contact');
```

- [ ] **Step 2: Add the contact route to the FR group**

In the FR `Route::prefix('fr')` group, add after the `qui-est-qui` route:

```php
Route::get('/contact', [PageController::class, 'contact'])->name('fr.contact');
```

- [ ] **Step 3: Verify routes are registered**

```bash
cd /Users/frederikvincx/Herd/harmonie && php artisan route:list --name=contact
```

Expected: shows `nl.contact` and `fr.contact`.

- [ ] **Step 4: Run existing tests to confirm nothing broke**

```bash
php artisan test
```

Expected: all passing.

- [ ] **Step 5: Commit**

```bash
git add routes/web.php
git commit -m "feat: add missing nl.contact and fr.contact routes"
```

---

## Task 1: LocaleController + /set-locale route

**Files:**
- Create: `app/Http/Controllers/LocaleController.php`
- Create: `tests/Feature/LocaleSwitchTest.php`

- [ ] **Step 1: Write failing tests**

```php
// tests/Feature/LocaleSwitchTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    public function test_set_locale_redirects_and_sets_cookie(): void
    {
        $response = $this->get('/set-locale/fr?redirect=/fr');

        $response->assertRedirect('/fr');
        $response->assertCookie('preferred_locale', 'fr');
    }

    public function test_set_locale_nl_sets_cookie(): void
    {
        $response = $this->get('/set-locale/nl?redirect=/');

        $response->assertRedirect('/');
        $response->assertCookie('preferred_locale', 'nl');
    }

    public function test_set_locale_rejects_invalid_locale(): void
    {
        // Route constraint 'nl|fr' means any other value returns 404
        $response = $this->get('/set-locale/de?redirect=/');
        $response->assertStatus(404);
    }

    public function test_set_locale_rejects_external_redirect(): void
    {
        $response = $this->get('/set-locale/fr?redirect=https://evil.com');

        // Should redirect to '/' not to evil.com
        $response->assertRedirect('/');
    }

    public function test_set_locale_allows_relative_redirect(): void
    {
        $response = $this->get('/set-locale/fr?redirect=/fr/services');

        $response->assertRedirect('/fr/services');
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
cd /Users/frederikvincx/Herd/harmonie && php artisan test --filter=LocaleSwitchTest
```

Expected: FAIL — route and controller don't exist yet.

- [ ] **Step 3: Create the controller**

```php
// app/Http/Controllers/LocaleController.php
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
```

- [ ] **Step 4: Add route to routes/web.php**

Add this block at the top of the file, before the NL group. Add the import too.

```php
use App\Http\Controllers\LocaleController;

Route::get('/set-locale/{locale}', [LocaleController::class, 'switch'])
    ->name('set-locale')
    ->where('locale', 'nl|fr');
```

- [ ] **Step 5: Run tests to confirm they pass**

```bash
php artisan test --filter=LocaleSwitchTest
```

Expected: 5 passing.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/LocaleController.php tests/Feature/LocaleSwitchTest.php routes/web.php
git commit -m "feat: add LocaleController and /set-locale route with open-redirect protection"
```

---

## Task 2: DetectPreferredLocale middleware

**Files:**
- Create: `app/Http/Middleware/DetectPreferredLocale.php`
- Create: `tests/Feature/DetectPreferredLocaleTest.php`

- [ ] **Step 1: Write failing tests**

```php
// tests/Feature/DetectPreferredLocaleTest.php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DetectPreferredLocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_french_browser_is_redirected_to_fr_on_first_visit(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-BE,fr;q=0.9,nl;q=0.5'])
            ->get('/');

        $response->assertRedirect();
        $this->assertStringStartsWith('/fr', $response->headers->get('Location'));
        $response->assertCookie('preferred_locale', 'fr');
    }

    public function test_dutch_browser_is_not_redirected(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'nl-BE,nl;q=0.9'])
            ->get('/');

        $response->assertStatus(200);
        $response->assertCookie('preferred_locale', 'nl');
    }

    public function test_cookie_prevents_redirect_on_subsequent_visits(): void
    {
        $response = $this->withHeaders(['Accept-Language' => 'fr-BE'])
            ->withCookie('preferred_locale', 'nl')
            ->get('/');

        $response->assertStatus(200); // No redirect despite French header
    }

    public function test_fr_routes_are_not_affected_by_middleware(): void
    {
        // Middleware only runs on NL group; visiting FR directly should always work
        $response = $this->withHeaders(['Accept-Language' => 'nl-BE'])
            ->get('/fr');

        $response->assertStatus(200);
    }

    public function test_french_browser_on_activity_page_redirects_to_fr_equivalent(): void
    {
        $activiteit = \App\Models\Activiteit::factory()->create(['status' => 'gepubliceerd']);

        $response = $this->withHeaders(['Accept-Language' => 'fr-BE'])
            ->get('/activiteiten/' . $activiteit->slug);

        $response->assertRedirect();
        $this->assertStringContainsString('/fr/activites/' . $activiteit->slug, $response->headers->get('Location'));
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --filter=DetectPreferredLocaleTest
```

Expected: Several FAIL — middleware doesn't exist yet.

- [ ] **Step 3: Create the middleware**

Note: `$request->cookie('preferred_locale')` returns the decrypted value because Laravel's `EncryptCookies` middleware runs before custom middleware and decrypts inbound cookies. Laravel's `assertCookie($name, $value)` also correctly checks against decrypted values. No changes to `EncryptCookies` are needed.

```php
// app/Http/Middleware/DetectPreferredLocale.php
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
}
```

- [ ] **Step 4: Register middleware on the NL route group in routes/web.php**

Change the NL group opening line from:

```php
Route::middleware('locale:nl')->group(function () {
```

to:

```php
Route::middleware(['locale:nl', \App\Http\Middleware\DetectPreferredLocale::class])->group(function () {
```

- [ ] **Step 5: Run tests to confirm they pass**

```bash
php artisan test --filter=DetectPreferredLocaleTest
```

Expected: 5 passing.

- [ ] **Step 6: Run full test suite to check nothing broke**

```bash
php artisan test
```

Expected: all passing.

- [ ] **Step 7: Commit**

```bash
git add app/Http/Middleware/DetectPreferredLocale.php tests/Feature/DetectPreferredLocaleTest.php routes/web.php
git commit -m "feat: add DetectPreferredLocale middleware — redirect French browsers to /fr on first visit"
```

---

## Task 3: Expand lang files + create common.php and pages.php

**Files:**
- Modify: `lang/nl/nav.php`, `lang/fr/nav.php`
- Modify: `lang/nl/activities.php`, `lang/fr/activities.php`
- Modify: `lang/nl/forms.php`, `lang/fr/forms.php`
- Create: `lang/nl/common.php`, `lang/fr/common.php`
- Create: `lang/nl/pages.php`, `lang/fr/pages.php`

- [ ] **Step 1: Add `wie_is_wie` to nav.php files**

Note: `language_switch` already exists in both nav.php files (`'Français'` in NL, `'Nederlands'` in FR). Only `wie_is_wie` is new.

In `lang/nl/nav.php`, add to the array:
```php
'wie_is_wie' => 'Wie is wie',
```

In `lang/fr/nav.php`, add:
```php
'wie_is_wie' => 'Qui est qui',
```

- [ ] **Step 2: Add `at`, `label`, `all` to activities.php files**

Note: `activities.php` in both locales already has `cancelled`, `cancellation_notice`, `registration_closed`, `register`, `location`, `date`, `time`, `price`, `print`, `back`, `free`, `full`. Only `at`, `label`, and `all` are new keys.

In `lang/nl/activities.php`, add:
```php
'at' => 'om',
'label' => 'ACTIVITEIT',
'all' => 'Alle activiteiten',
```

In `lang/fr/activities.php`, add:
```php
'at' => 'à',
'label' => 'ACTIVITÉ',
'all' => 'Toutes les activités',
```

- [ ] **Step 3: Add `message_label` to forms.php files**

In `lang/nl/forms.php`, add:
```php
'message_label' => 'Bericht',
```

In `lang/fr/forms.php`, add:
```php
'message_label' => 'Message',
```

- [ ] **Step 4: Create lang/nl/common.php**

```php
<?php
return [
    'quick_links'    => 'Snel naar',
    'supported_by'   => 'Met steun van',
    'follow_facebook' => 'Volg De Harmonie op Facebook',
];
```

- [ ] **Step 5: Create lang/fr/common.php**

```php
<?php
return [
    'quick_links'    => 'Vers',
    'supported_by'   => 'Avec le soutien de',
    'follow_facebook' => 'Suivez De Harmonie sur Facebook',
];
```

- [ ] **Step 6: Create lang/nl/pages.php**

```php
<?php
return [
    'home_title' => 'Home',

    // Wie is wie
    'wie_is_wie_title'    => 'Wie is wie ?',
    'team_eyebrow'        => 'HET TEAM',
    'governance'          => 'BESTUUR',
    'board_organ'         => 'Bestuursorgaan',
    'neighborhood_council' => 'Buurtraad Noordwijk',

    // Diensten
    'diensten_title'           => 'Diensten',
    'diensten_eyebrow'         => 'DIENSTEN',
    'diensten_heading'         => 'De Harmonie is er voor jou',
    'diensten_intro'           => 'De Harmonie helpt senioren uit de Noordwijk in het dagelijks leven. We organiseren activiteiten en diensten in ons eigen centrum, in de buurt, maar ook bij mensen thuis.',
    'diensten_services_heading' => 'Onze diensten',
    'diensten_services'        => [
        'Wegwijs in socio-cultureel Brussel — Sociale dienst',
        'Partner in het eerstelijnszorgnetwerk in de Noordwijk',
        'Diensten, Activiteiten en Uitstappen voor Senioren — Creatief · Ontspannend · Cultureel · Vormend · Informatief · Sportief',
        'Sociaal restaurant, afhaal en levering aan huis',
        'Catering & Verhuur voor buurtbewoners & -organisaties',
        'Hulp bij de Grote Kuis',
        'Boodschappendienst & Vervoersdienst',
        'Klusjesdienst & Poetsdienst',
        'Tweedehands Klerenwinkel & Retouches',
    ],
    'grote_kuis_eyebrow'        => 'PROJECT',
    'grote_kuis_title'          => 'Hulp bij de Grote Kuis',
    'grote_kuis_description'    => 'Met dit project willen we je helpen met de \'Grote Kuis\'. Samen met onze poetsers en klussers nemen we je woning onder handen. We kunnen kleine werken of herstellingen doen, we geven alle spullen een grondige poetsbeurt en we kunnen je ook helpen met je administratie.',
    'grote_kuis_examples_label' => 'Waarbij kan je bijvoorbeeld hulp krijgen?',
    'grote_kuis_examples'       => ['Oven kuisen', 'Kraantje repareren', 'Ruiten wassen', 'Tapijt kuisen', 'Dampkap installeren', 'Gordijnen wassen', 'Toilet schilderen'],
    'grote_kuis_cta'            => 'Heb je interesse of ken je iemand die hiervoor interesse heeft? Laat het ons zeker weten!',
];
```

- [ ] **Step 7: Create lang/fr/pages.php**

```php
<?php
return [
    'home_title' => 'Accueil',

    // Qui est qui
    'wie_is_wie_title'    => 'Qui est qui ?',
    'team_eyebrow'        => 'L\'ÉQUIPE',
    'governance'          => 'GOUVERNANCE',
    'board_organ'         => 'Organe d\'administration',
    'neighborhood_council' => 'Conseil de quartier Noordwijk',

    // Services
    'diensten_title'           => 'Services',
    'diensten_eyebrow'         => 'SERVICES',
    'diensten_heading'         => 'De Harmonie est là pour vous',
    'diensten_intro'           => 'De Harmonie aide les seniors du quartier Noordwijk dans leur vie quotidienne. Nous organisons des activités et des services dans notre propre centre, dans le quartier, mais aussi chez les personnes à domicile.',
    'diensten_services_heading' => 'Nos services',
    'diensten_services'        => [
        'Parcours dans la vie socioculturelle de Bruxelles — Service social',
        'Partenaire du réseau de soins primaires du quartier Nord',
        'Services, activités et sorties pour les seniors — Créatif · Détente · Culturel · Formateur · Informatif · Sportif',
        'Restaurant social, plats à emporter et livraison à domicile',
        'Restauration et location pour les habitants et les organisations locales',
        'Aide au Grand Nettoyage',
        'Service de courses et de transport',
        'Service de nettoyage et de bricolage',
        'Boutique de vêtements d\'occasion et retouches',
    ],
    'grote_kuis_eyebrow'        => 'PROJET',
    'grote_kuis_title'          => 'Aide au Grand Nettoyage',
    'grote_kuis_description'    => 'Avec ce projet, nous voulons vous aider avec le \'Grand Nettoyage\'. Avec nos agents de nettoyage et bricoleurs, nous prenons en charge votre domicile. Nous pouvons effectuer de petits travaux ou réparations, donner un nettoyage complet à toutes vos affaires et vous aider avec votre administration.',
    'grote_kuis_examples_label' => 'Exemples',
    'grote_kuis_examples'       => ['Nettoyer le four', 'Réparer un robinet', 'Laver les vitres', 'Nettoyer le tapis', 'Installer une hotte', 'Laver les rideaux', 'Peindre les toilettes'],
    'grote_kuis_cta'            => 'Vous êtes intéressé(e) ou vous connaissez quelqu\'un qui pourrait l\'être ? Faites-le nous savoir !',
];
```

- [ ] **Step 8: Verify lang files load correctly**

```bash
php -r "define('LARAVEL_START', microtime(true)); require 'vendor/autoload.php'; \$app = require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); app()->setLocale('fr'); echo __('pages.diensten_title') . PHP_EOL;"
```

Expected output: `Services`

- [ ] **Step 9: Commit**

```bash
git add lang/
git commit -m "feat: expand lang files — add common.php, pages.php, new keys in nav/activities/forms"
```

---

## Task 4: Migrate nav.blade.php

**File:** `resources/views/components/nav.blade.php`

The nav currently uses inline ternaries and has an unwired language switcher. After this task: ternaries replaced, language switcher wired to `/set-locale`.

- [ ] **Step 1: Add a test assertion for nav translation**

In `tests/Feature/BilingualRoutingTest.php`, add to the existing test class:

```php
public function test_nav_shows_fr_labels_on_fr_routes(): void
{
    $response = $this->get('/fr');
    $response->assertSee('Activités');
    $response->assertSee('Services');
}

public function test_nav_shows_nl_labels_on_nl_routes(): void
{
    $response = $this->get('/');
    $response->assertSee('Activiteiten');
    $response->assertSee('Diensten');
}

public function test_nav_language_switcher_link_present(): void
{
    $response = $this->get('/');
    $response->assertSee('set-locale', false); // link to /set-locale exists
}
```

- [ ] **Step 2: Run the new assertions to confirm they fail**

```bash
php artisan test --filter=BilingualRoutingTest::test_nav_shows_fr_labels_on_fr_routes
php artisan test --filter=BilingualRoutingTest::test_nav_language_switcher_link_present
```

Expected: FAIL.

- [ ] **Step 3: Rewrite nav.blade.php**

Replace the entire file content with:

```blade
<header style="background-color: var(--color-brand-blue); position: relative;">
    <div class="max-w-5xl mx-auto flex items-center" style="padding: 1.25rem 1.5rem;">
        <a href="{{ route(app()->getLocale() . '.home') }}" class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" class="h-8 w-auto brightness-0 invert">
        </a>
        <nav class="hidden md:flex items-center gap-8" style="margin-left: auto; font-family: var(--font-sans);">
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.activities') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.services') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.menu') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.contact') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.contact') }}
            </a>
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
            <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem; opacity: 0.75;">
               {{ __('nav.language_switch') }}
            </a>
        </nav>
        <!-- Mobile toggle -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="md:hidden p-2" style="color: white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div x-show="open" class="absolute top-full left-0 right-0 px-6 py-4 space-y-3 md:hidden z-50"
                 style="background-color: var(--color-brand-dark)">
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="block text-sm font-semibold" style="color: white">{{ __('nav.activities') }}</a>
                <a href="{{ route(app()->getLocale() . '.diensten') }}" class="block text-sm font-semibold" style="color: white">{{ __('nav.services') }}</a>
                <a href="{{ route(app()->getLocale() . '.weekmenu') }}" class="block text-sm font-semibold" style="color: white">{{ __('nav.menu') }}</a>
                <a href="{{ route(app()->getLocale() . '.contact') }}" class="block text-sm font-semibold" style="color: white">{{ __('nav.contact') }}</a>
                <a href="{{ route('set-locale', ['locale' => $targetLocale, 'redirect' => $targetUrl]) }}" class="block text-sm font-semibold" style="color: white; opacity: 0.75;">{{ __('nav.language_switch') }}</a>
            </div>
        </div>
    </div>
</header>
```

Note: `$targetLocale`, `$targetRoute`, `$targetUrl` are computed in the `@php` block inside the desktop nav. These variables are available in the same Blade scope for the mobile menu too.

- [ ] **Step 4: Run tests**

```bash
php artisan test --filter=BilingualRoutingTest
```

Expected: all passing.

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/nav.blade.php tests/Feature/BilingualRoutingTest.php
git commit -m "feat: migrate nav to translation strings and wire language switcher"
```

---

## Task 5: Migrate footer.blade.php

**File:** `resources/views/components/footer.blade.php`

Footer has hardcoded NL strings that need FR equivalents.

- [ ] **Step 1: Add test assertions**

In `tests/Feature/BilingualRoutingTest.php`, add:

```php
public function test_footer_shows_fr_labels_on_fr_routes(): void
{
    $response = $this->get('/fr');
    $response->assertSee('Avec le soutien de');
    $response->assertSee('Suivez De Harmonie sur Facebook');
}
```

- [ ] **Step 2: Run to confirm it fails**

```bash
php artisan test --filter=BilingualRoutingTest::test_footer_shows_fr_labels_on_fr_routes
```

- [ ] **Step 3: Update footer.blade.php**

Replace hardcoded NL strings with `__()` calls. Only the strings in the left and right columns need to change; the center column is logo-only. Specific replacements:

Replace `Snel naar` with `{{ __('common.quick_links') }}`

Replace the `Diensten` link text with `{{ __('nav.services') }}`

Replace the `Wie is wie` link text with `{{ __('nav.wie_is_wie') }}`

Replace `Met steun van` with `{{ __('common.supported_by') }}`

Replace `Volg De Harmonie op Facebook` with `{{ __('common.follow_facebook') }}`

- [ ] **Step 4: Run tests**

```bash
php artisan test --filter=BilingualRoutingTest
```

Expected: all passing.

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/footer.blade.php
git commit -m "feat: migrate footer to translation strings"
```

---

## Task 6: Migrate registration-form.blade.php

**File:** `resources/views/livewire/registration-form.blade.php`

Has ternaries for field labels and hardcoded 'Submit' (English). Already uses `__('forms.success')`.

- [ ] **Step 1: Update the view**

Make these replacements:

- `app()->getLocale() === 'fr' ? 'Votre nom' : 'Je naam'` → `{{ __('forms.name') }}`
- `app()->getLocale() === 'fr' ? 'Votre numéro de téléphone' : 'Je telefoonnummer'` → `{{ __('forms.phone') }}`
- `app()->getLocale() === 'fr' ? 'Votre email' : 'Je email'` → `{{ __('forms.email') }}`
- `Bericht *` label → `{{ __('forms.message_label') }} *`
- `<span wire:loading.remove>Submit</span>` → `<span wire:loading.remove>{{ __('forms.submit') }}</span>`

- [ ] **Step 2: Run tests**

```bash
php artisan test --filter=BilingualRoutingTest
```

Expected: all passing.

- [ ] **Step 3: Commit**

```bash
git add resources/views/livewire/registration-form.blade.php
git commit -m "feat: migrate registration form field labels to translation strings"
```

---

## Task 7: Migrate activity-filter.blade.php

**File:** `resources/views/livewire/activity-filter.blade.php`

Has a hardcoded `om` (NL for "at" before time) that should be `à` in French.

- [ ] **Step 1: Find the line**

The line reads:
```blade
om {{ substr($activiteit->startuur, 0, 5) }}
```

- [ ] **Step 2: Replace it**

```blade
{{ __('activities.at') }} {{ substr($activiteit->startuur, 0, 5) }}
```

- [ ] **Step 3: Run tests**

```bash
php artisan test
```

Expected: all passing.

- [ ] **Step 4: Commit**

```bash
git add resources/views/livewire/activity-filter.blade.php
git commit -m "feat: translate time preposition in activity-filter (om → à in French)"
```

---

## Task 8: Migrate show.blade.php

**File:** `resources/views/activiteiten/show.blade.php`

Multiple ternaries for labels, sidebar headings, and registration section. The two bilingual intro paragraphs in the registration section (lines 71-78) are intentionally showing both languages simultaneously — leave them as-is.

- [ ] **Step 1: Make the following replacements**

- `app()->getLocale() === 'fr' ? 'Toutes les activités' : 'Alle activiteiten'` → `{{ __('activities.all') }}`
- `$activiteit->notice ?? (app()->getLocale() === 'fr' ? 'Cette activité est annulée.' : 'Deze activiteit is geannuleerd.')` → `$activiteit->notice ?? __('activities.cancellation_notice')`
- `app()->getLocale() === 'fr' ? 'ACTIVITÉ' : 'ACTIVITEIT'` → `{{ __('activities.label') }}`
- `app()->getLocale() === 'fr' ? 'S\'inscrire' : 'Inschrijven'` (h3 heading in registration box) → `{{ __('activities.register') }}`
- `app()->getLocale() === 'fr' ? 'Inscription fermée (activité annulée).' : 'Inschrijving gesloten (activiteit geannuleerd).'` → `{{ __('activities.registration_closed') }}`
- `app()->getLocale() === 'fr' ? 'Date' : 'Datum'` → `{{ __('activities.date') }}`
- `app()->getLocale() === 'fr' ? 'Prix' : 'Prijs'` → `{{ __('activities.price') }}`
- `app()->getLocale() === 'fr' ? 'Lieu' : 'Locatie'` → `{{ __('activities.location') }}`
- `app()->getLocale() === 'fr' ? 'Imprimer' : 'Afdrukken'` → `{{ __('activities.print') }}`

- [ ] **Step 2: Run tests**

```bash
php artisan test
```

Expected: all passing.

- [ ] **Step 3: Commit**

```bash
git add resources/views/activiteiten/show.blade.php
git commit -m "feat: migrate activity detail view to translation strings"
```

---

## Task 9: Migrate diensten.blade.php

**File:** `resources/views/pages/diensten.blade.php`

This page has the most ternaries. They all map to keys in the new `pages.php` lang file. All `@php` array blocks become single `trans()` calls.

- [ ] **Step 1: Make the following replacements**

`@section('title', ...)`:
- `app()->getLocale() === 'fr' ? 'Services' : 'Diensten'` → `__('pages.diensten_title')`

`<x-eyebrow ...>`:
- `app()->getLocale() === 'fr' ? 'SERVICES' : 'DIENSTEN'` → `__('pages.diensten_eyebrow')`

`<h1 ...>`:
- `app()->getLocale() === 'fr' ? 'De Harmonie est là pour vous' : 'De Harmonie is er voor jou'` → `__('pages.diensten_heading')`

`<p ...>` intro paragraph:
- The multi-line ternary → `{{ __('pages.diensten_intro') }}`

`<h2>` services section:
- `app()->getLocale() === 'fr' ? 'Nos services' : 'Onze diensten'` → `__('pages.diensten_services_heading')`

`@php $services = ...` block:
```blade
@php $services = trans('pages.diensten_services'); @endphp
```

`<x-eyebrow ...>` grote kuis:
- `app()->getLocale() === 'fr' ? 'PROJET' : 'PROJECT'` → `__('pages.grote_kuis_eyebrow')`

`<h2>` grote kuis title:
- `app()->getLocale() === 'fr' ? 'Aide au Grand Nettoyage' : 'Hulp bij de Grote Kuis'` → `__('pages.grote_kuis_title')`

`<p>` grote kuis description:
- Multi-line ternary → `{{ __('pages.grote_kuis_description') }}`

`<p>` examples label:
- `app()->getLocale() === 'fr' ? 'Exemples' : 'Waarbij kan je bijvoorbeeld hulp krijgen?'` → `__('pages.grote_kuis_examples_label')`

`@php $examples = ...` block:
```blade
@php $examples = trans('pages.grote_kuis_examples'); @endphp
```

`<p>` CTA:
- Multi-line ternary → `{{ __('pages.grote_kuis_cta') }}`

- [ ] **Step 2: Run tests**

```bash
php artisan test
```

Expected: all passing.

- [ ] **Step 3: Commit**

```bash
git add resources/views/pages/diensten.blade.php
git commit -m "feat: migrate diensten page to translation strings"
```

---

## Task 10: Migrate wie-is-wie.blade.php

**File:** `resources/views/pages/wie-is-wie.blade.php`

Has ternaries for title, eyebrow, and team/governance labels. The `$teams` array has `'nl'` and `'fr'` keys — replace the ternary that selects between them with `$team[app()->getLocale()]`.

- [ ] **Step 1: Make the following replacements**

`@section('title', ...)`:
- `app()->getLocale() === 'fr' ? 'Qui est qui ?' : 'Wie is wie ?'` → `__('pages.wie_is_wie_title')`

`<x-eyebrow>`:
- `app()->getLocale() === 'fr' ? 'L\'ÉQUIPE' : 'HET TEAM'` → `__('pages.team_eyebrow')`

`<h1>`:
- `app()->getLocale() === 'fr' ? 'Qui est qui ?' : 'Wie is wie ?'` → `__('pages.wie_is_wie_title')`

In the teams loop, `{{ app()->getLocale() === 'fr' ? $team['fr'] : $team['nl'] }}`:
- Replace with `{{ $team[app()->getLocale()] }}`

Governance eyebrow `<p>`:
- `app()->getLocale() === 'fr' ? 'GOUVERNANCE' : 'BESTUUR'` → `__('pages.governance')`

Board organ label:
- `app()->getLocale() === 'fr' ? 'Organe d\'administration' : 'Bestuursorgaan'` → `__('pages.board_organ')`

Neighbourhood council label:
- `app()->getLocale() === 'fr' ? 'Conseil de quartier Noordwijk' : 'Buurtraad Noordwijk'` → `__('pages.neighborhood_council')`

- [ ] **Step 2: Run tests**

```bash
php artisan test
```

Expected: all passing.

- [ ] **Step 3: Commit**

```bash
git add resources/views/pages/wie-is-wie.blade.php
git commit -m "feat: migrate wie-is-wie page to translation strings"
```

---

## Task 11: Migrate activiteiten/index.blade.php

**File:** `resources/views/activiteiten/index.blade.php`

The homepage is intentionally bilingual in many places (both NL and FR text shown simultaneously as a community design choice). Only the `@section('title', ...)` ternary needs migrating.

- [ ] **Step 1: Replace the single ternary**

`@section('title', app()->getLocale() === 'fr' ? 'Accueil' : 'Home')`

→

`@section('title', __('pages.home_title'))`

- [ ] **Step 2: Run tests**

```bash
php artisan test
```

Expected: all passing.

- [ ] **Step 3: Confirm no remaining inline ternaries**

```bash
grep -n "getLocale() ===" resources/views/activiteiten/index.blade.php
```

Expected: no output (or only in intentionally bilingual display sections — any remaining matches should be inspected and confirmed intentional).

- [ ] **Step 4: Commit**

```bash
git add resources/views/activiteiten/index.blade.php
git commit -m "feat: migrate homepage page title to translation string"
```

---

## Task 12: Final verification

- [ ] **Step 1: Confirm no remaining getLocale ternaries across all view files**

```bash
grep -rn "getLocale() ===" resources/views/
```

Expected: zero results. Note: the homepage `index.blade.php` uses `app()->getLocale()` in non-ternary expressions (e.g. `route(app()->getLocale() . '.weekmenu')`). These are correct and will not appear in this grep since they are not `=== 'fr'` comparisons. Any remaining `getLocale() ===` hits are untranslated strings that must be migrated.

- [ ] **Step 2: Run full test suite**

```bash
php artisan test
```

Expected: all passing.

- [ ] **Step 3: Clear compiled views and cache**

```bash
php artisan view:clear && php artisan cache:clear
```

- [ ] **Step 4: Take Playwright screenshots to verify both locales**

Use the existing script at `scripts/screenshot.cjs` or write a `/tmp` script. Check:
- Homepage `/` shows NL nav labels, language switcher shows "Français"
- Homepage `/fr` shows FR nav labels, language switcher shows "Nederlands"
- `/diensten` page renders in NL; `/fr/services` renders in FR
- Activity detail page shows correct sidebar labels (Datum/Date, Prijs/Prix, Locatie/Lieu)
