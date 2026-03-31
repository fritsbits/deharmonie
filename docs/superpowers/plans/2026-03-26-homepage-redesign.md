# Homepage Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the homepage to match the approved UX mockup — new hero copy, static menu preview, activities as cards, compact service cards, simplified practical bar.

**Architecture:** Pure blade/view changes. No new models, no controller changes. Translation strings added to `lang/nl/pages.php` and `lang/fr/pages.php`. Existing `$activiteiten` variable (already limited to 3) powers the activity cards. Menu preview is static placeholder content for client validation.

**Tech Stack:** Laravel 13, Blade templates, Tailwind v4 (inline styles for complex layouts per CLAUDE.md), PHPUnit feature tests.

**Spec:** `docs/superpowers/specs/2026-03-26-homepage-redesign.md`

---

## File Map

| File | Action | What changes |
|------|--------|-------------|
| `lang/nl/pages.php` | Modify | Add new keys; update `home_activities_heading` |
| `lang/fr/pages.php` | Modify | Same — FR translations |
| `resources/views/activiteiten/index.blade.php` | Modify | Full section rework |
| `tests/Feature/ActivityControllerTest.php` | Modify | Update stale assertions; add new ones |

---

## Task 1: Add translation keys (NL + FR)

**Files:**
- Modify: `lang/nl/pages.php`
- Modify: `lang/fr/pages.php`

- [ ] **Step 1: Add NL keys**

In `lang/nl/pages.php`, add these keys (before the `// Wie is wie` comment):

```php
// Homepage hero
'home_hero_eyebrow' => 'Noordwijk · Brussel · Al 50 jaar',
'home_hero_heading_line1' => 'Eet mee.',
'home_hero_heading_line2' => 'Doe mee.',
'home_hero_heading_line3' => 'Kom langs.',
'home_hero_subheading' => 'Het kloppend hart van de Noordwijk. Elke dag een warm onthaal, een maaltijd en activiteiten voor iedereen uit de buurt.',
'home_hero_cta_activities' => 'Bekijk activiteiten',
'home_hero_cta_menu' => 'Weekmenu →',

// Homepage menu preview
'home_menu_label' => 'Restaurant & Menu',
'home_menu_preview_heading' => 'Vandaag & morgen aan tafel',
'home_menu_soup_included' => 'Soep van de dag inbegrepen',
'home_menu_link' => 'Volledig weekmenu bekijken →',
'home_menu_today_badge' => 'Vandaag',

// Homepage service cards
'home_services_section_heading' => 'Voor iedereen uit de buurt',
'home_service_restaurant_title' => 'Samen aan tafel',
'home_service_restaurant_body' => 'Elke dag een warme maaltijd in ons sociaal restaurant. Takeaway en thuisbezorging mogelijk.',
'home_service_restaurant_price' => 'Vanaf € 9',
'home_service_restaurant_link' => 'Meer over het restaurant →',
'home_service_activities_title' => 'Activiteiten & workshops',
'home_service_activities_body' => 'Van Italiaans leren tot country line dance. Elke week iets om bij te leren of gewoon te genieten.',
'home_service_activities_link' => 'Bekijk agenda →',
'home_service_home_title' => 'Bij u thuis',
'home_service_home_body' => 'Poetsen, boodschappen, vervoer, klusjes en maaltijden aan huis. Zodat u thuis kan blijven wonen.',
'home_service_home_link' => 'Bekijk thuisdiensten →',

// Homepage practical bar
'home_practical_address_label' => 'Adres',
'home_practical_hours_label' => 'Openingsuren',
'home_practical_contact_label' => 'Contact',
```

Also update the existing `home_activities_heading` value:
```php
'home_activities_heading' => 'Komende activiteiten',
```

- [ ] **Step 2: Add FR keys**

In `lang/fr/pages.php`, add the same keys with French translations:

```php
// Homepage hero
'home_hero_eyebrow' => 'Noordwijk · Bruxelles · 50 ans déjà',
'home_hero_heading_line1' => 'Mangez ensemble.',
'home_hero_heading_line2' => 'Participez.',
'home_hero_heading_line3' => 'Venez nous voir.',
'home_hero_subheading' => 'Le cœur battant du Noordwijk. Chaque jour un accueil chaleureux, un repas et des activités pour tout le quartier.',
'home_hero_cta_activities' => 'Voir les activités',
'home_hero_cta_menu' => 'Menu de la semaine →',

// Homepage menu preview
'home_menu_label' => 'Restaurant & Menu',
'home_menu_preview_heading' => "Aujourd'hui & demain à table",
'home_menu_soup_included' => 'Potage du jour inclus',
'home_menu_link' => 'Voir le menu complet →',
'home_menu_today_badge' => "Aujourd'hui",

// Homepage service cards
'home_services_section_heading' => 'Pour tout le quartier',
'home_service_restaurant_title' => 'À table ensemble',
'home_service_restaurant_body' => 'Chaque jour un repas chaud dans notre restaurant social. À emporter et livraison à domicile possible.',
'home_service_restaurant_price' => 'À partir de € 9',
'home_service_restaurant_link' => 'En savoir plus →',
'home_service_activities_title' => 'Activités & ateliers',
'home_service_activities_body' => "De l'italien à la country line dance. Chaque semaine quelque chose à apprendre ou simplement à apprécier.",
'home_service_activities_link' => "Voir l'agenda →",
'home_service_home_title' => 'Chez vous',
'home_service_home_body' => 'Nettoyage, courses, transport, bricolage et repas à domicile. Pour rester chez soi le plus longtemps possible.',
'home_service_home_link' => 'Voir les services →',

// Homepage practical bar
'home_practical_address_label' => 'Adresse',
'home_practical_hours_label' => "Heures d'ouverture",
'home_practical_contact_label' => 'Contact',
```

Also update `home_activities_heading`:
```php
'home_activities_heading' => 'Prochaines activités',
```

- [ ] **Step 3: Verify homepage loads**

```bash
php artisan test --compact --filter=test_homepage_shows_published_activities
```

Expected: PASS (page loads, existing content still present for now)

- [ ] **Step 4: Commit**

```bash
git add lang/nl/pages.php lang/fr/pages.php
git commit -m "feat: add homepage redesign translation keys"
```

---

## Task 2: Update the hero section

**Files:**
- Modify: `resources/views/activiteiten/index.blade.php`
- Modify: `tests/Feature/ActivityControllerTest.php`

- [ ] **Step 1: Write failing test**

In `tests/Feature/ActivityControllerTest.php`, update `test_homepage_shows_published_activities` — replace the old hero assertions with new ones:

```php
// Replace these old assertions:
// $response->assertSee('Dienstencentrum');
// $response->assertSee('Quartier Noordwijk');
// With:
$response->assertSee('Eet mee');
$response->assertSee('Doe mee');
$response->assertSee('Kom langs');
$response->assertSee('Bekijk activiteiten');
```

- [ ] **Step 2: Run test to confirm it fails**

```bash
php artisan test --compact --filter=test_homepage_shows_published_activities
```

Expected: FAIL — `assertSee('Eet mee')` fails because old hero copy is still in place.

- [ ] **Step 3: Replace hero section in blade**

In `resources/views/activiteiten/index.blade.php`, replace the entire `{{-- HERO --}}` section (lines 7–31) with:

```blade
{{-- HERO --}}
<section style="background-color: white; overflow: hidden;">
    <div class="hero-inner" style="display: flex; align-items: stretch; min-height: 400px;">

        {{-- Copy --}}
        <div class="hero-copy" style="flex: 1; display: flex; align-items: center;">
            <div style="max-width: 72rem; width: 100%; margin: 0 auto; padding: 3rem 1.5rem;">
                <x-eyebrow mb="1rem">{{ __('pages.home_hero_eyebrow') }}</x-eyebrow>
                <h1 style="font-family: var(--font-sans); font-size: 4rem; font-weight: 900; line-height: 1.05; color: var(--color-brand-dark); margin-bottom: 1rem;">
                    {{ __('pages.home_hero_heading_line1') }}<br>
                    {{ __('pages.home_hero_heading_line2') }}<br>
                    {{ __('pages.home_hero_heading_line3') }}
                </h1>
                <p style="font-size: 1.125rem; line-height: 1.6; color: var(--color-brand-muted); margin-bottom: 1.75rem; max-width: 38rem;">
                    {{ __('pages.home_hero_subheading') }}
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
                       style="background: var(--color-brand-orange); color: white; padding: 0.75rem 1.5rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none;">
                        {{ __('pages.home_hero_cta_activities') }}
                    </a>
                    <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
                       style="background: transparent; color: var(--color-brand-blue); padding: 0.75rem 1.5rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1rem; text-decoration: none; border: 2px solid var(--color-brand-blue);">
                        {{ __('pages.home_hero_cta_menu') }}
                    </a>
                </div>
            </div>
        </div>

        {{-- Right photo --}}
        <div class="hero-col-image" style="flex: 0 0 42%; overflow: hidden;">
            <img src="{{ asset('images/photo-restaurant-vol.webp') }}" alt=""
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
        </div>

    </div>
</section>
```

- [ ] **Step 4: Run test to confirm it passes**

```bash
php artisan test --compact --filter=test_homepage_shows_published_activities
```

Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/activiteiten/index.blade.php tests/Feature/ActivityControllerTest.php
git commit -m "feat: update homepage hero with new copy and photo"
```

---

## Task 3: Add static menu preview section

**Files:**
- Modify: `resources/views/activiteiten/index.blade.php`
- Modify: `tests/Feature/ActivityControllerTest.php`

- [ ] **Step 1: Write failing test**

Add a new test to `ActivityControllerTest`:

```php
public function test_homepage_shows_menu_preview(): void
{
    $response = $this->get('/');
    $response->assertSee('Vandaag');
    $response->assertSee('Soep van de dag inbegrepen');
    $response->assertSee('Volledig weekmenu bekijken');
}
```

- [ ] **Step 2: Run test to confirm it fails**

```bash
php artisan test --compact --filter=test_homepage_shows_menu_preview
```

Expected: FAIL

- [ ] **Step 3: Add menu preview section to blade**

In the blade, insert this section directly after the `{{-- COMMUNITY PHOTO STRIP --}}` section (after line 49):

```blade
{{-- MENU PREVIEW (static — to be wired to Weekmenu model in future) --}}
<section style="background-color: #fff8f5; border-top: 3px solid var(--color-brand-orange); padding: 2.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="orange" mb="0.5rem">{{ __('pages.home_menu_label') }}</x-eyebrow>
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.25rem;">
            <h2 style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: var(--color-brand-dark);">
                {{ __('pages.home_menu_preview_heading') }}
            </h2>
        </div>
        {{-- TODO: Replace static content with dynamic Weekmenu model query --}}
        <div style="display: flex; gap: 1rem;">
            {{-- Today --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.25rem 1.5rem; border: 1px solid #e8e0d8; position: relative;">
                <span style="position: absolute; top: -10px; left: 1rem; background: var(--color-brand-orange); color: white; font-size: 0.6875rem; font-weight: 800; padding: 2px 10px; border-radius: 999px; text-transform: uppercase; letter-spacing: 0.06em;">
                    {{ __('pages.home_menu_today_badge') }}
                </span>
                <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.4rem;">Maandag 30/03</p>
                <p style="font-size: 1rem; font-weight: 700; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Kalf blanket met bulgur</p>
                <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.75rem;">{{ __('pages.home_menu_soup_included') }}</p>
                <p style="font-size: 1.25rem; font-weight: 900; color: var(--color-brand-orange); font-family: var(--font-sans);">€ 10</p>
            </div>
            {{-- Tomorrow --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.25rem 1.5rem; border: 1px solid #e8e0d8;">
                <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.4rem;">Dinsdag 31/03</p>
                <p style="font-size: 1rem; font-weight: 700; color: var(--color-brand-dark); margin-bottom: 0.25rem;">Varkensgebraad met gestoofd witloof</p>
                <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.75rem;">{{ __('pages.home_menu_soup_included') }}</p>
                <p style="font-size: 1.25rem; font-weight: 900; color: var(--color-brand-orange); font-family: var(--font-sans);">€ 9</p>
            </div>
        </div>
        <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
           style="display: inline-block; margin-top: 1rem; font-size: 0.9375rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
            {{ __('pages.home_menu_link') }}
        </a>
    </div>
</section>
```

- [ ] **Step 4: Run test to confirm it passes**

```bash
php artisan test --compact --filter=test_homepage_shows_menu_preview
```

Expected: PASS

- [ ] **Step 5: Commit**

```bash
git add resources/views/activiteiten/index.blade.php tests/Feature/ActivityControllerTest.php
git commit -m "feat: add static menu preview section to homepage"
```

---

## Task 4: Replace alternating sections with activity cards + service cards + practical bar

This task removes the three large alternating sections (restaurant, activities, services) and the big contact section, and replaces them with the compact new sections.

**Files:**
- Modify: `resources/views/activiteiten/index.blade.php`
- Modify: `tests/Feature/ActivityControllerTest.php`

- [ ] **Step 1: Update test assertions**

In `test_homepage_shows_published_activities`, replace the three old section heading assertions:

```php
// Remove these:
// $response->assertSee('Elke dag samen aan tafel');
// $response->assertSee('Creatief, cultureel en sportief');
// $response->assertSee('Ook hulp waar u het nodig heeft');

// Add these:
$response->assertSee('Komende activiteiten');
$response->assertSee('Samen aan tafel');
$response->assertSee('Bij u thuis');
$response->assertSee('Activiteiten');
$response->assertSee('Antwerpsesteenweg 24');
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact --filter=test_homepage_shows_published_activities
```

Expected: FAIL — new assertions not met yet.

- [ ] **Step 3: Replace sections in blade**

In `resources/views/activiteiten/index.blade.php`, delete everything from `{{-- SECTION 1: Restaurant --}}` through the end of `{{-- CONTACT / OPENING HOURS --}}` (lines 51–203), and replace with:

```blade
{{-- UPCOMING ACTIVITIES --}}
<section style="background-color: white; padding: 3.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.25rem;">
            <div>
                <x-eyebrow mb="0.4rem">{{ __('nav.activities') }}</x-eyebrow>
                <h2 style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: var(--color-brand-dark);">
                    {{ __('pages.home_activities_heading') }}
                </h2>
            </div>
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               style="font-family: var(--font-sans); font-weight: 700; font-size: 0.9375rem; color: var(--color-brand-green); text-decoration: underline; white-space: nowrap;">
                {{ __('activities.all') }} →
            </a>
        </div>
        <div class="activity-cards-grid" style="display: flex; gap: 1rem;">
            @forelse ($activiteiten as $activiteit)
                <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                   style="flex: 1; display: block; background: var(--color-brand-bg); border: 1px solid #e8e0d8; border-radius: 8px; padding: 1.25rem 1.25rem; text-decoration: none; {{ $activiteit->status->value === 'geannuleerd' ? 'opacity: 0.6;' : '' }}">
                    <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-orange); margin-bottom: 0.35rem;">
                        {{ ucfirst($activiteit->datum->locale(app()->getLocale())->isoFormat('ddd')) }}
                        {{ $activiteit->datum->format('j/n') }}
                    </p>
                    <p style="font-size: 1rem; font-weight: 800; color: var(--color-brand-dark); line-height: 1.2; margin-bottom: 0.35rem;">
                        {{ $activiteit->titel }}
                        @if ($activiteit->status->value === 'geannuleerd')
                            <x-badge type="geannuleerd">&times;</x-badge>
                        @endif
                    </p>
                    <p style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-bottom: 0.75rem;">
                        {{ substr($activiteit->startuur, 0, 5) }} · {{ $activiteit->locatie }}
                    </p>
                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
                        {{ __('activities.register') }} →
                    </span>
                </a>
            @empty
                <p style="color: var(--color-brand-muted); padding: 1rem 0;">{{ __('activities.no_upcoming') }}</p>
            @endforelse
        </div>
    </div>
</section>

{{-- WHAT WE DO — SERVICE CARDS --}}
<section style="background-color: #f2f6fb; padding: 3.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow mb="0.4rem">{{ __('nav.services') }}</x-eyebrow>
        <h2 style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 1.5rem;">
            {{ __('pages.home_services_section_heading') }}
        </h2>
        <div class="service-cards-grid" style="display: flex; gap: 1rem; align-items: stretch;">
            {{-- Restaurant --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem; border-bottom: 3px solid var(--color-brand-orange);">
                <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 0.5rem;">
                    {{ __('pages.home_service_restaurant_title') }}
                </p>
                <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 0.75rem;">
                    {{ __('pages.home_service_restaurant_body') }}
                </p>
                <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-orange); margin-bottom: 0.75rem;">
                    {{ __('pages.home_service_restaurant_price') }}
                </p>
                <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
                   style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
                    {{ __('pages.home_service_restaurant_link') }}
                </a>
            </div>
            {{-- Activities --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem; border-bottom: 3px solid var(--color-brand-green);">
                <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 0.5rem;">
                    {{ __('pages.home_service_activities_title') }}
                </p>
                <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 0.75rem;">
                    {{ __('pages.home_service_activities_body') }}
                </p>
                <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
                   style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
                    {{ __('pages.home_service_activities_link') }}
                </a>
            </div>
            {{-- Home services --}}
            <div style="flex: 1; background: white; border-radius: 8px; padding: 1.5rem; border-bottom: 3px solid var(--color-brand-blue);">
                <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-dark); margin-bottom: 0.5rem;">
                    {{ __('pages.home_service_home_title') }}
                </p>
                <p style="font-size: 0.9375rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 0.75rem;">
                    {{ __('pages.home_service_home_body') }}
                </p>
                <a href="{{ route(app()->getLocale() . '.diensten') }}"
                   style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
                    {{ __('pages.home_service_home_link') }}
                </a>
            </div>
        </div>
    </div>
</section>

{{-- PRACTICAL INFO --}}
<section id="contact" style="background-color: var(--color-brand-bg); border-top: 1px solid #e8e0d8; padding: 2rem 1.5rem;">
    <div class="practical-grid" style="max-width: 72rem; margin: 0 auto; display: flex; gap: 3rem; align-items: start;">
        <div>
            <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                {{ __('pages.home_practical_address_label') }}
            </p>
            <p style="font-size: 0.9375rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5;">
                Antwerpsesteenweg 24<br>1000 Brussel
            </p>
        </div>
        <div>
            <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                {{ __('pages.home_practical_hours_label') }}
            </p>
            <p style="font-size: 0.9375rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5;">
                {{ __('pages.home_hours_weekdays') }}<br>{{ __('pages.home_hours_saturday') }}
            </p>
        </div>
        <div>
            <p style="font-size: 0.6875rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-brand-muted); margin-bottom: 0.35rem;">
                {{ __('pages.home_practical_contact_label') }}
            </p>
            <p style="font-size: 0.9375rem; line-height: 1.6;">
                <a href="tel:0220328048" style="font-weight: 700; color: var(--color-brand-blue); text-decoration: none;">02/203.28.48</a><br>
                <a href="mailto:info@deharmonie.be" style="color: var(--color-brand-blue); text-decoration: none;">info@deharmonie.be</a>
            </p>
        </div>
    </div>
</section>
```

Also update the responsive `<style>` block at the bottom of the blade — replace the existing block with:

```blade
<style>
/* sm — mobile */
@media (max-width: 767px) {
    .hero-inner { flex-direction: column !important; min-height: auto !important; }
    .hero-col-image { display: none; }
    .hero-copy div { padding: 2.5rem 1.25rem !important; }
    .hero-copy h1 { font-size: 2.75rem !important; }
    .activity-cards-grid { flex-direction: column !important; }
    .service-cards-grid { flex-direction: column !important; }
    .practical-grid { flex-direction: column !important; gap: 1.5rem !important; }
}
/* md — tablet */
@media (min-width: 768px) and (max-width: 1023px) {
    .hero-copy h1 { font-size: 3rem !important; }
}
</style>
```

- [ ] **Step 4: Run all tests**

```bash
php artisan test --compact --filter=ActivityControllerTest
```

Expected: All PASS. If `test_homepage_shows_published_activities` fails on `assertSee('activities.register')`, check that the `activities.register` key exists in `lang/nl/activities.php`.

- [ ] **Step 5: Check `activities.register` and `activities.no_upcoming` keys exist**

```bash
php artisan tinker --execute 'echo __("activities.register") . " | " . __("activities.no_upcoming");'
```

If either shows its raw key string, add the missing entries:
- `lang/nl/activities.php`: `'register' => 'Inschrijven'`, `'no_upcoming' => 'Geen activiteiten gepland.'`
- `lang/fr/activities.php`: `'register' => "S'inscrire"`, `'no_upcoming' => 'Aucune activité prévue.'`

Note: `home_hours_weekdays` and `home_hours_saturday` already exist in the lang files — no action needed.

- [ ] **Step 6: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 7: Final test run**

```bash
php artisan test --compact
```

Expected: All tests pass.

- [ ] **Step 8: Commit**

```bash
git add resources/views/activiteiten/index.blade.php tests/Feature/ActivityControllerTest.php lang/
git commit -m "feat: replace homepage sections with activity cards, service cards and practical bar"
```

---

## Done

The homepage now shows:
- New hero: "Eet mee. Doe mee. Kom langs." with photo and 2 CTAs
- Static menu preview: today + tomorrow with price
- Activity cards: 3 upcoming activities
- Service cards: restaurant / activities / home services
- Practical bar: address, hours, contact

**Next steps (not in this plan):**
- Wire menu preview to real `Weekmenu` model (future plan)
- Validate with Cynthia tomorrow and confirm audience priorities
