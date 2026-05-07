# Volunteer Discovery Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Surface the Vrijwilligers page through a section nav on all About pages, a dedicated section on the Over Ons page, and a slim strip on the homepage — removing Vrijwilligers from the main nav.

**Architecture:** Three coordinated Blade view changes plus lang file additions. No new routes, models, or controllers. The section nav lives entirely in `nav.blade.php` and activates via route-name detection. All copy is bilingual (NL + FR).

**Tech Stack:** Laravel 13, Blade, Tailwind v4 inline styles (existing pattern)

---

### Task 1: Add copy keys (NL + FR)

**Files:**
- Modify: `lang/nl/pages.php`
- Modify: `lang/fr/pages.php`

- [ ] **Add to `lang/nl/pages.php`, inside the `// Over ons` block:**

```php
    'over_ons_vrijwilligers_eyebrow' => 'Doe mee',
    'over_ons_vrijwilligers_heading' => 'Word vrijwilliger bij De Harmonie',
    'over_ons_vrijwilligers_lead' => 'Heb je een paar uur per maand en wil je iets betekenen voor de buurt? We zijn altijd op zoek naar enthousiaste vrijwilligers die mee activiteiten begeleiden.',
    'over_ons_vrijwilligers_cta' => 'Meer over vrijwilligerswerk',
    'over_ons_photo_vrijwilligers_alt' => 'Vrijwilligerswerk bij De Harmonie',
```

- [ ] **Add to `lang/nl/pages.php`, as a new `// Homepage volunteer strip` block after the existing homepage keys:**

```php
    // Homepage volunteer strip
    'home_vrijwilligers_heading' => 'Wil je meehelpen bij De Harmonie?',
    'home_vrijwilligers_cta' => 'Word vrijwilliger',
```

- [ ] **Add to `lang/fr/pages.php`, inside the `// Over ons` block:**

```php
    'over_ons_vrijwilligers_eyebrow' => 'Participez',
    'over_ons_vrijwilligers_heading' => 'Devenez bénévole à De Harmonie',
    'over_ons_vrijwilligers_lead' => 'Vous avez quelques heures par mois et souhaitez contribuer au quartier ? Nous recherchons des bénévoles enthousiastes pour co-animer nos activités.',
    'over_ons_vrijwilligers_cta' => 'En savoir plus',
    'over_ons_photo_vrijwilligers_alt' => 'Bénévolat à De Harmonie',
```

- [ ] **Add to `lang/fr/pages.php`, as a new `// Homepage volunteer strip` block:**

```php
    // Homepage volunteer strip
    'home_vrijwilligers_heading' => 'Vous souhaitez aider à De Harmonie ?',
    'home_vrijwilligers_cta' => 'Devenir bénévole',
```

- [ ] **Run Pint:**
```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Commit:**
```bash
git add lang/nl/pages.php lang/fr/pages.php
git commit -m "feat(i18n): add copy keys for Over Ons volunteer section and homepage strip"
```

---

### Task 2: Section nav in header

**Files:**
- Modify: `resources/views/components/nav.blade.php`
- Test: `tests/Feature/VrijwilligersPageTest.php`

The section nav is a dark blue sub-row (`#3a68a8`) appended inside the `<header>` element. It renders only on the three About-section pages, detected by route name. The standalone Vrijwilligers link is removed from both desktop and mobile menus.

- [ ] **Write failing tests** — add to `tests/Feature/VrijwilligersPageTest.php`:

```php
public function test_vrijwilligers_page_shows_section_nav(): void
{
    $response = $this->get(route('nl.vrijwilligers'));

    $response->assertSee('#3a68a8');
}

public function test_over_ons_page_shows_section_nav(): void
{
    $response = $this->get(route('nl.over-ons'));

    $response->assertSee('#3a68a8');
}

public function test_homepage_does_not_show_section_nav(): void
{
    $response = $this->get(route('nl.home'));

    $response->assertDontSee('#3a68a8');
}
```

- [ ] **Run tests to verify the first two fail, the third passes:**
```bash
php artisan test --compact --filter=test_vrijwilligers_page_shows_section_nav
php artisan test --compact --filter=test_over_ons_page_shows_section_nav
php artisan test --compact --filter=test_homepage_does_not_show_section_nav
```
Expected: first two FAIL, third PASSES.

- [ ] **Add `@php` block at the very top of `resources/views/components/nav.blade.php`** (before the `<header>` tag):

```blade
@php
$aboutSubnavRoutes = [
    'nl.over-ons', 'nl.wie-is-wie', 'nl.vrijwilligers',
    'fr.over-ons', 'fr.wie-is-wie', 'fr.vrijwilligers',
];
$showAboutSubnav = in_array(request()->route()?->getName(), $aboutSubnavRoutes);
$currentRoute = request()->route()?->getName() ?? '';
@endphp
```

- [ ] **Remove the desktop Vrijwilligers link** from the `<nav class="hidden md:flex ...">` block. Find and delete these lines:

```blade
            <a href="{{ route(app()->getLocale() . '.vrijwilligers') }}"
               class="font-semibold hover:opacity-75 transition-opacity"
               style="color: white; font-size: 1.125rem;">
               {{ __('nav.vrijwilligers') }}
            </a>
```

- [ ] **Remove the mobile Vrijwilligers link** from the `<div id="mobile-menu" ...>` block. Find and delete this line:

```blade
                <a href="{{ route(app()->getLocale() . '.vrijwilligers') }}" class="block font-semibold" style="color: white; padding: 1rem 0; font-size: 1.25rem; font-family: var(--font-sans); border-bottom: 1px solid rgba(255,255,255,0.15);">{{ __('nav.vrijwilligers') }}</a>
```

- [ ] **Add the section nav sub-row** just before the closing `</header>` tag:

```blade
    @if($showAboutSubnav)
    <div style="background: #3a68a8; border-top: 1px solid rgba(255,255,255,0.12);">
        <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem; display: flex; gap: 0;">
            @foreach ([
                ['route' => app()->getLocale() . '.over-ons',      'label' => __('nav.over_ons')],
                ['route' => app()->getLocale() . '.wie-is-wie',    'label' => __('nav.wie_is_wie')],
                ['route' => app()->getLocale() . '.vrijwilligers', 'label' => __('nav.vrijwilligers')],
            ] as $tab)
            @php $isActive = ($currentRoute === $tab['route']); @endphp
            <a href="{{ route($tab['route']) }}"
               style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: {{ $isActive ? 'white' : 'rgba(255,255,255,0.6)' }}; text-decoration: none; padding: 0.5rem 0.75rem; border-bottom: 2px solid {{ $isActive ? 'white' : 'transparent' }}; display: inline-block; transition: color 0.15s, border-color 0.15s;"
               onmouseover="if (!{{ $isActive ? 'true' : 'false' }}) this.style.color='rgba(255,255,255,0.85)'" onmouseout="if (!{{ $isActive ? 'true' : 'false' }}) this.style.color='rgba(255,255,255,0.6)'">
                {{ $tab['label'] }}
            </a>
            @endforeach
        </div>
    </div>
    @endif
```

- [ ] **Run all three tests:**
```bash
php artisan test --compact --filter=test_vrijwilligers_page_shows_section_nav
php artisan test --compact --filter=test_over_ons_page_shows_section_nav
php artisan test --compact --filter=test_homepage_does_not_show_section_nav
```
Expected: all three PASS.

- [ ] **Run the broader nav-related test suite to check for regressions:**
```bash
php artisan test --compact tests/Feature/VrijwilligersPageTest.php tests/Feature/OverOnsPageTest.php tests/Feature/WieIsWiePageTest.php tests/Feature/BilingualRoutingTest.php
```
Expected: all PASS.

- [ ] **Run Pint:**
```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Commit:**
```bash
git add resources/views/components/nav.blade.php tests/Feature/VrijwilligersPageTest.php
git commit -m "feat(nav): add About section sub-nav row, remove Vrijwilligers from main nav"
```

---

### Task 3: Volunteer section on Over Ons page

**Files:**
- Modify: `resources/views/pages/over-ons.blade.php`
- Test: `tests/Feature/OverOnsPageTest.php`

A new section is inserted directly before `{{-- TEAM REFERENCE --}}` (currently around line 194). Image on the left (`photo-handwerk.webp`), text on the right, orange accent — mirroring the visual rhythm of the Team section below it.

- [ ] **Write failing tests** — add to `tests/Feature/OverOnsPageTest.php`:

```php
public function test_over_ons_shows_volunteer_section(): void
{
    $response = $this->get(route('nl.over-ons'));

    $response->assertSee('Word vrijwilliger bij De Harmonie');
    $response->assertSee('Meer over vrijwilligerswerk');
}

public function test_fr_over_ons_shows_volunteer_section(): void
{
    $response = $this->get(route('fr.over-ons'));

    $response->assertSee('Devenez bénévole à De Harmonie');
    $response->assertSee('En savoir plus');
}
```

- [ ] **Run to verify they fail:**
```bash
php artisan test --compact --filter=test_over_ons_shows_volunteer_section
php artisan test --compact --filter=test_fr_over_ons_shows_volunteer_section
```
Expected: both FAIL.

- [ ] **Insert the volunteer section** in `resources/views/pages/over-ons.blade.php`, directly before the `{{-- TEAM REFERENCE --}}` comment:

```blade
{{-- VOLUNTEER CTA --}}
<section style="background: var(--color-brand-bg); border-top: 1px solid #e8e5e2; padding: 3.5rem 0;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem;">
        <div class="over-ons-vrijwilligers-layout" style="display: flex; gap: 3rem; align-items: center;">

            {{-- Left: photo --}}
            <div class="over-ons-vrijwilligers-img img-outline" style="flex: 0 0 300px; height: 260px; overflow: hidden; border-radius: 12px;">
                <img src="{{ asset('images/photo-handwerk.webp') }}"
                     alt="{{ __('pages.over_ons_photo_vrijwilligers_alt') }}"
                     loading="lazy"
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>

            {{-- Right: text --}}
            <div style="flex: 1; min-width: 0;">
                <x-eyebrow size="sm" color="orange" mb="0.5rem">{{ __('pages.over_ons_vrijwilligers_eyebrow') }}</x-eyebrow>
                <h2 style="font-family: var(--font-sans); font-size: clamp(1.5rem, 2.5vw, 2rem); font-weight: 900; color: var(--color-brand-dark); line-height: 1.15; margin-bottom: 1rem;">
                    {{ __('pages.over_ons_vrijwilligers_heading') }}
                </h2>
                <p style="font-size: 1.125rem; line-height: 1.7; color: var(--color-brand-muted); margin-bottom: 1.75rem;">
                    {{ __('pages.over_ons_vrijwilligers_lead') }}
                </p>
                <a href="{{ route(app()->getLocale() . '.vrijwilligers') }}"
                   class="over-ons-team-link"
                   style="display: inline-block; font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-orange); border: 1.5px solid var(--color-brand-orange); padding: 0.6rem 1.25rem; border-radius: 3px; text-decoration: none; letter-spacing: 0.03em;">
                    {{ __('pages.over_ons_vrijwilligers_cta') }} →
                </a>
            </div>

        </div>
    </div>
</section>

```

- [ ] **Add mobile responsiveness** — find the `@media (max-width: 767px)` block in the `<style>` tag at the bottom of `over-ons.blade.php` and add inside it:

```css
    .over-ons-vrijwilligers-layout { flex-direction: column !important; gap: 2rem !important; }
    .over-ons-vrijwilligers-img { flex: none !important; width: 100% !important; }
```

- [ ] **Run tests:**
```bash
php artisan test --compact --filter=test_over_ons_shows_volunteer_section
php artisan test --compact --filter=test_fr_over_ons_shows_volunteer_section
```
Expected: both PASS.

- [ ] **Run full Over Ons test suite to check for regressions:**
```bash
php artisan test --compact tests/Feature/OverOnsPageTest.php
```
Expected: all PASS.

- [ ] **Run Pint:**
```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Commit:**
```bash
git add resources/views/pages/over-ons.blade.php tests/Feature/OverOnsPageTest.php
git commit -m "feat(over-ons): add volunteer section above team reference"
```

---

### Task 4: Homepage volunteer strip

**Files:**
- Modify: `resources/views/activiteiten/index.blade.php`
- Test: `tests/Feature/ActivityControllerTest.php`

A narrow green band inserted between the social proof photo strip and the practical info section. Heading on the left, white pill button on the right.

- [ ] **Write failing test** — add to `tests/Feature/ActivityControllerTest.php`:

```php
public function test_homepage_shows_volunteer_strip(): void
{
    $response = $this->get(route('nl.home'));

    $response->assertSee('Wil je meehelpen bij De Harmonie?');
    $response->assertSee('Word vrijwilliger');
}
```

- [ ] **Run to verify it fails:**
```bash
php artisan test --compact --filter=test_homepage_shows_volunteer_strip
```
Expected: FAIL.

- [ ] **Insert the volunteer strip** in `resources/views/activiteiten/index.blade.php`, between the `{{-- SOCIAL PROOF PHOTO STRIP --}}` block and the `{{-- PRACTICAL INFO --}}` section:

```blade
{{-- VOLUNTEER STRIP --}}
<section style="background: var(--color-brand-green); padding: 2rem 1.5rem;">
    <div class="volunteer-strip-inner" style="max-width: 72rem; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; gap: 1.5rem;">
        <p style="font-family: var(--font-sans); font-size: 1.25rem; font-weight: 800; color: white; margin: 0; line-height: 1.3;">
            {{ __('pages.home_vrijwilligers_heading') }}
        </p>
        <a href="{{ route(app()->getLocale() . '.vrijwilligers') }}"
           class="press-scale"
           style="background: white; color: var(--color-brand-green); font-family: var(--font-sans); font-size: 1rem; font-weight: 700; text-decoration: none; padding: 0.75rem 1.75rem; border-radius: 999px; white-space: nowrap; flex-shrink: 0;">
            {{ __('pages.home_vrijwilligers_cta') }}
        </a>
    </div>
</section>

```

- [ ] **Add mobile responsiveness** — find the `@media (max-width: 767px)` block in the `<style>` tag at the bottom of `activiteiten/index.blade.php` and add inside it:

```css
    .volunteer-strip-inner { flex-direction: column !important; align-items: flex-start !important; }
```

- [ ] **Run test:**
```bash
php artisan test --compact --filter=test_homepage_shows_volunteer_strip
```
Expected: PASS.

- [ ] **Run homepage-related tests to check for regressions:**
```bash
php artisan test --compact tests/Feature/ActivityControllerTest.php tests/Feature/VrijwilligersPageTest.php
```
Expected: all PASS.

- [ ] **Run Pint:**
```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Commit:**
```bash
git add resources/views/activiteiten/index.blade.php tests/Feature/ActivityControllerTest.php
git commit -m "feat(home): add volunteer strip between photo strip and practical info"
```

---

### Final verification

- [ ] **Run the full test suite:**
```bash
php artisan test --compact
```
Expected: all PASS.
