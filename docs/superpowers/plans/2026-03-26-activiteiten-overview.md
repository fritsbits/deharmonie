# Activiteiten Overview Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the bare calendar list at `/activiteiten` with an invitation-first page: photo strip → Reeksen cards → static special moments gallery → agenda link.

**Architecture:** The current `ActivityController::index()` gets a `$reeksen` query added and renders a rebuilt `overzicht.blade.php`. The existing Livewire calendar moves to a new `/activiteiten/agenda` route. Everything is server-rendered except the agenda page (which stays Livewire). Lucide icons are rendered via the `mallardduck/blade-lucide-icons` Blade component package.

**Tech Stack:** Laravel 13, Blade, Tailwind v4, Livewire 3, `mallardduck/blade-lucide-icons`, `ActiviteitTemplate` model (already exists at `app/Models/ActiviteitTemplate.php`)

---

## File map

| File | Action | What changes |
|------|--------|-------------|
| `app/Http/Controllers/ActivityController.php` | Modify | Add `agenda()` method; update `index()` to pass `$reeksen` |
| `routes/web.php` | Modify | Add NL + FR agenda routes |
| `resources/views/activiteiten/overzicht.blade.php` | Rewrite | New invitation page (photo strip, reeksen, special moments, agenda link) |
| `resources/views/activiteiten/agenda.blade.php` | Create | Calendar page (current overzicht content, moved here) |
| `resources/views/activiteiten/index.blade.php` | Modify | Update homepage activities section — photo-forward cards, less date-first |
| `lang/nl/activities.php` | Modify | Add new translation keys |
| `lang/fr/activities.php` | Modify | Add new translation keys |
| `tests/Feature/ActiviteitenOverviewTest.php` | Create | New test class |

---

## Task 1: Add translation strings

**Files:**
- Modify: `lang/nl/activities.php`
- Modify: `lang/fr/activities.php`

- [ ] **Step 1: Add NL strings**

Add to the `return []` array in `lang/nl/activities.php`:

```php
'reeksen_eyebrow' => 'Elke week',
'reeksen_heading' => 'Vaste activiteiten',
'reeksen_day_prefix' => 'Elke',
'special_moments_eyebrow' => 'Sfeer',
'special_moments_heading' => 'Bijzondere momenten',
'agenda_link' => 'Bekijk de volledige agenda',
'overview_hero_eyebrow' => 'Activiteiten',
```

- [ ] **Step 2: Add FR strings**

Add to `lang/fr/activities.php`:

```php
'reeksen_eyebrow' => 'Chaque semaine',
'reeksen_heading' => 'Activités régulières',
'reeksen_day_prefix' => 'Chaque',
'special_moments_eyebrow' => 'Ambiance',
'special_moments_heading' => 'Moments forts',
'agenda_link' => "Voir l'agenda complet",
'overview_hero_eyebrow' => 'Activités',
```

- [ ] **Step 3: Add day-of-week translation arrays to both files**

In `lang/nl/activities.php`:
```php
'days' => [
    0 => 'zondag', 1 => 'maandag', 2 => 'dinsdag',
    3 => 'woensdag', 4 => 'donderdag', 5 => 'vrijdag',
    6 => 'zaterdag', 7 => 'zondag',
],
```

In `lang/fr/activities.php`:
```php
'days' => [
    0 => 'dimanche', 1 => 'lundi', 2 => 'mardi',
    3 => 'mercredi', 4 => 'jeudi', 5 => 'vendredi',
    6 => 'samedi', 7 => 'dimanche',
],
```

- [ ] **Step 4: Commit**

```bash
git add lang/nl/activities.php lang/fr/activities.php
git commit -m "feat: add translation strings for activities overview redesign"
```

---

## Task 2: Install Lucide Blade icons package

**Files:**
- Run composer

- [ ] **Step 1: Install the package**

```bash
composer require mallardduck/blade-lucide-icons
```

Expected: package resolves and installs. Verify with:
```bash
composer show mallardduck/blade-lucide-icons
```

- [ ] **Step 2: Clear view cache**

```bash
php artisan view:clear
```

- [ ] **Step 3: Verify a Lucide component renders**

```bash
php artisan tinker --execute 'echo view("test-lucide")->render();'
```

Skip this step — verify in the browser once the view is built. Lucide components render as inline SVG via `<x-lucide-{name} />` syntax.

- [ ] **Step 4: Commit**

```bash
git add composer.json composer.lock
git commit -m "feat: install blade-lucide-icons for Reeksen section icons"
```

---

## Task 3: Add agenda route and move calendar view

**Files:**
- Create: `resources/views/activiteiten/agenda.blade.php`
- Modify: `app/Http/Controllers/ActivityController.php`
- Modify: `routes/web.php`
- Create: `tests/Feature/ActiviteitenOverviewTest.php`

- [ ] **Step 1: Write failing tests**

Create `tests/Feature/ActiviteitenOverviewTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitenOverviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_agenda_page_loads_for_nl(): void
    {
        $response = $this->get('/activiteiten/agenda');
        $response->assertStatus(200);
    }

    public function test_agenda_page_loads_for_fr(): void
    {
        $response = $this->get('/fr/activites/agenda');
        $response->assertStatus(200);
    }
}
```

- [ ] **Step 2: Run to confirm they fail**

```bash
php artisan test --compact tests/Feature/ActiviteitenOverviewTest.php
```

Expected: 2 failures — routes not found (404).

- [ ] **Step 3: Create the agenda view**

Create `resources/views/activiteiten/agenda.blade.php` with the current contents of `resources/views/activiteiten/overzicht.blade.php` — copy it exactly:

```blade
@extends('layouts.app')
@section('title', 'Agenda — ' . __('activities.all'))

@section('content')

<x-page-hero
    :eyebrow="__('nav.activities')"
    eyebrow-color="green"
    :heading="__('activities.overview_heading')"
    :lead="__('activities.overview_tagline')"
    bg="white"
/>

<div style="background: #eef5f1;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem 4rem;">
        <livewire:activity-overzicht />
    </div>
</div>

@endsection
```

- [ ] **Step 4: Add agenda() method to ActivityController**

In `app/Http/Controllers/ActivityController.php`, add after the `index()` method:

```php
public function agenda(): \Illuminate\View\View
{
    return view('activiteiten.agenda');
}
```

- [ ] **Step 5: Add agenda routes to routes/web.php**

In `routes/web.php`, add the agenda routes **before** the `{slug}` routes in each locale group (slug routes must stay last to avoid matching "agenda" as a slug):

```php
// NL group — add after the existing activiteiten.index route:
Route::get('/activiteiten/agenda', [ActivityController::class, 'agenda'])->name('nl.activiteiten.agenda');

// FR group — add after the existing activites route:
Route::get('/activites/agenda', [ActivityController::class, 'agenda'])->name('fr.activiteiten.agenda');
```

- [ ] **Step 6: Run tests**

```bash
php artisan test --compact tests/Feature/ActiviteitenOverviewTest.php
```

Expected: 2 tests pass.

- [ ] **Step 7: Commit**

```bash
git add resources/views/activiteiten/agenda.blade.php app/Http/Controllers/ActivityController.php routes/web.php tests/Feature/ActiviteitenOverviewTest.php
git commit -m "feat: add /activiteiten/agenda route with calendar view"
```

---

## Task 4: Update ActivityController::index() to pass Reeksen

**Files:**
- Modify: `app/Http/Controllers/ActivityController.php`

- [ ] **Step 1: Add failing test for overview page showing reeksen**

Add to `tests/Feature/ActiviteitenOverviewTest.php`:

```php
use App\Models\ActiviteitTemplate;

public function test_overview_page_shows_reeksen(): void
{
    $reeks = ActiviteitTemplate::factory()->create([
        'titel_nl' => 'Yoga op dinsdag',
        'dag_van_de_week' => 2,
        'startuur' => '10:00:00',
    ]);

    $response = $this->get('/activiteiten');
    $response->assertStatus(200);
    $response->assertSee('Yoga op dinsdag');
}

public function test_overview_page_loads_for_fr(): void
{
    $response = $this->get('/fr/activites');
    $response->assertStatus(200);
}
```

- [ ] **Step 2: Run to confirm they fail**

```bash
php artisan test --compact tests/Feature/ActiviteitenOverviewTest.php
```

Expected: `test_overview_page_shows_reeksen` fails — "Yoga op dinsdag" not found in response.

- [ ] **Step 3: Update ActivityController::index()**

Replace the current `index()` method in `app/Http/Controllers/ActivityController.php`:

```php
public function index(): \Illuminate\View\View
{
    $reeksen = \App\Models\ActiviteitTemplate::orderBy('dag_van_de_week')
        ->orderBy('startuur')
        ->get();

    return view('activiteiten.overzicht', compact('reeksen'));
}
```

- [ ] **Step 4: Run pint**

```bash
vendor/bin/pint app/Http/Controllers/ActivityController.php --format agent
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/ActivityController.php
git commit -m "feat: pass reeksen to activiteiten overview view"
```

---

## Task 5: Build the new overview page

**Files:**
- Rewrite: `resources/views/activiteiten/overzicht.blade.php`

The page has five sections: hero (existing component) → photo strip → Reeksen cards → special moments gallery → agenda link.

**Icon map** (keyed by template ID — assigned based on current DB IDs 1–18):
```php
$iconMap = [
    1  => 'message-circle',  // Conversatietafel Spaans
    2  => 'message-circle',  // Conversatietafel Engels
    3  => 'message-circle',  // Conversatietafel Italiaans
    4  => 'message-circle',  // Nederlandse conversatietafel
    5  => 'music-2',         // Country Line Dance
    6  => 'brain',           // Geheugenatelier
    7  => 'armchair',        // Stoel-gym met Nicole
    8  => 'monitor',         // Digitale workshop
    9  => 'circle-dot',      // Bingo
    10 => 'palette',         // Creativiteit workshop
    11 => 'zap',             // Zumba
    12 => 'gem',             // Diamond Painting
    13 => 'scissors',        // Naaiworkshop
    14 => 'shopping-bag',    // Boodschappendienst
    15 => 'dumbbell',        // Pilates & Fitness
    16 => 'info',            // Sociale Infopunt
    17 => 'cake',            // Verjaardagsfeest
    18 => 'landmark',        // Culturele uitstap
];
```

All icon names are valid Lucide icons — verify at lucide.dev if any fail to render.

**Color cycle** for icon backgrounds (same palette already used in `activity-filter.blade.php`):
```php
$bgColors = ['#f3dbd5','#d4e8df','#d5e0f0','#f5e8d3','#dde7d5','#e8d9ef','#d9e8f0'];
```

- [ ] **Step 1: Write the new overzicht.blade.php**

Replace the full contents of `resources/views/activiteiten/overzicht.blade.php`:

```blade
@extends('layouts.app')
@section('title', __('activities.overview_heading') . ' — De Harmonie')

@section('content')

{{-- HERO --}}
<x-page-hero
    :eyebrow="__('activities.overview_hero_eyebrow')"
    eyebrow-color="green"
    :heading="__('activities.overview_heading')"
    :lead="__('activities.overview_tagline')"
    bg="white"
/>

{{-- PHOTO STRIP --}}
<div style="display: flex; height: 280px; overflow: hidden;">
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-groep-tafel.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-buiten-activiteit.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-party.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block; object-position: center bottom;">
    </div>
    <div style="flex: 1; overflow: hidden;">
        <img src="{{ asset('images/photo-groep-actief.webp') }}" alt=""
             style="width: 100%; height: 100%; object-fit: cover; display: block;">
    </div>
</div>

{{-- REEKSEN --}}
<section style="background: var(--color-brand-bg); padding: 5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="blue" mb="0.75rem">{{ __('activities.reeksen_eyebrow') }}</x-eyebrow>
        <x-section-heading mb="2.5rem">{{ __('activities.reeksen_heading') }}</x-section-heading>

        @php
            $iconMap = [
                1 => 'message-circle', 2 => 'message-circle', 3 => 'message-circle',
                4 => 'message-circle', 5 => 'music-2', 6 => 'brain', 7 => 'armchair',
                8 => 'monitor', 9 => 'circle-dot', 10 => 'palette', 11 => 'zap',
                12 => 'gem', 13 => 'scissors', 14 => 'shopping-bag', 15 => 'dumbbell',
                16 => 'info', 17 => 'cake', 18 => 'landmark',
            ];
            $bgColors = ['#f3dbd5','#d4e8df','#d5e0f0','#f5e8d3','#dde7d5','#e8d9ef','#d9e8f0'];
            $days = __('activities.days');
        @endphp

        <div class="reeksen-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            @foreach ($reeksen as $index => $reeks)
                @php
                    $icon = $iconMap[$reeks->id] ?? 'calendar';
                    $bg = $bgColors[$index % count($bgColors)];
                    $dag = $days[$reeks->dag_van_de_week] ?? '';
                    $uur = substr($reeks->startuur, 0, 5);
                    $beschrijving = app()->getLocale() === 'fr'
                        ? ($reeks->beschrijving_fr ?? $reeks->beschrijving_nl)
                        : ($reeks->beschrijving_nl ?? $reeks->beschrijving_fr);
                @endphp
                <div style="background: white; border: 1px solid #e8e0d8; border-radius: 10px; padding: 1.25rem; display: flex; align-items: flex-start; gap: 1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: {{ $bg }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <x-dynamic-component :component="'lucide-' . $icon" style="width: 20px; height: 20px; color: var(--color-brand-dark);" />
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-family: var(--font-sans); font-weight: 800; font-size: 1rem; color: var(--color-brand-dark); margin: 0 0 0.2rem;">
                            {{ app()->getLocale() === 'fr' ? ($reeks->titel_fr ?? $reeks->titel_nl) : $reeks->titel_nl }}
                        </p>
                        <p style="font-size: 0.875rem; color: var(--color-brand-muted); margin: 0 0 0.35rem;">
                            {{ __('activities.reeksen_day_prefix') }} {{ $dag }} · {{ $uur }}
                        </p>
                        @if ($beschrijving)
                            <p style="font-size: 0.875rem; color: var(--color-brand-muted); margin: 0; line-height: 1.5;">
                                {{ Str::limit(strip_tags($beschrijving), 80) }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SPECIAL MOMENTS --}}
<section style="background: #eef5f1; padding: 5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="green" mb="0.75rem">{{ __('activities.special_moments_eyebrow') }}</x-eyebrow>
        <x-section-heading mb="2rem">{{ __('activities.special_moments_heading') }}</x-section-heading>

        <div class="moments-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; height: 320px;">
            {{-- Large photo left --}}
            <div style="border-radius: 12px; overflow: hidden;">
                <img src="{{ asset('images/photo-feest-2.webp') }}" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            {{-- Two smaller right --}}
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="flex: 1; border-radius: 12px; overflow: hidden;">
                    <img src="{{ asset('images/photo-buiten-event.webp') }}" alt=""
                         style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
                <div style="flex: 1; border-radius: 12px; overflow: hidden;">
                    <img src="{{ asset('images/photo-cake.jpg') }}" alt=""
                         style="width: 100%; height: 100%; object-fit: cover; display: block;">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FULL AGENDA LINK --}}
<section style="background: white; padding: 3rem 1.5rem; text-align: center;">
    <a href="{{ route(app()->getLocale() . '.activiteiten.agenda') }}"
       style="font-family: var(--font-sans); font-size: 1rem; font-weight: 700; color: var(--color-brand-blue); text-decoration: underline;">
        {{ __('activities.agenda_link') }} →
    </a>
</section>

<style>
@media (max-width: 767px) {
    .reeksen-grid { grid-template-columns: 1fr !important; }
    .moments-grid { grid-template-columns: 1fr !important; height: auto !important; }
    .moments-grid > div:last-child { display: none; }
    .moments-grid > div:first-child { height: 220px; }
}
</style>

@endsection
```

- [ ] **Step 2: Run the overview tests**

```bash
php artisan test --compact tests/Feature/ActiviteitenOverviewTest.php
```

Expected: all 4 tests pass (agenda NL, agenda FR, overview shows reeksen, overview FR loads).

- [ ] **Step 3: Run pint**

```bash
vendor/bin/pint resources/views/activiteiten/overzicht.blade.php --format agent
```

Pint doesn't lint Blade files — skip if no output.

- [ ] **Step 4: Verify in browser**

Visit `https://harmonie.test/activiteiten` — confirm:
- Photo strip renders (4 photos side by side)
- Reeksen cards appear in 2-col grid with icons and day/time
- Special moments photos show in asymmetric grid
- "Bekijk de volledige agenda →" link at bottom goes to `/activiteiten/agenda`
- `/activiteiten/agenda` still shows the Livewire calendar

- [ ] **Step 5: Commit**

```bash
git add resources/views/activiteiten/overzicht.blade.php
git commit -m "feat: rebuild activiteiten overview as invitation-first page"
```

---

## Task 6: Update homepage activities section

**Files:**
- Modify: `resources/views/activiteiten/index.blade.php`

The current homepage shows up to 3 upcoming activities as date-first text cards. Replace with photo-forward cards where the image/color is prominent and date is secondary.

- [ ] **Step 1: Locate the activities section in index.blade.php**

Find the `{{-- UPCOMING ACTIVITIES --}}` comment block (around line 93). The section currently has a `@forelse` loop rendering flex cards.

- [ ] **Step 2: Replace the card markup inside the @forelse loop**

Replace the `<a href="...">` card block (keep the `@forelse`/`@empty`/`@endforelse` wrapper) with:

```blade
@forelse ($activiteiten as $activiteit)
    @php
        $colors = ['#f3dbd5','#d4e8df','#d5e0f0'];
        $bg = $colors[$loop->index % count($colors)];
        $imgUrl = $activiteit->getFirstMediaUrl('afbeelding');
    @endphp
    <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
       style="flex: 1; display: block; text-decoration: none; border-radius: 10px; overflow: hidden; border: 1px solid #e8e0d8; {{ $activiteit->status->value === 'geannuleerd' ? 'opacity: 0.7;' : '' }}">
        {{-- Photo or color band --}}
        <div style="height: 160px; background: {{ $bg }}; overflow: hidden;">
            @if ($imgUrl)
                <img src="{{ $imgUrl }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            @endif
        </div>
        {{-- Card body --}}
        <div style="padding: 1rem 1.25rem 1.5rem; background: var(--color-brand-bg);">
            <p style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-brand-green); margin: 0 0 0.3rem;">
                <x-relative-date :datum="$activiteit->datum" />
            </p>
            <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--color-brand-dark); line-height: 1.2; margin: 0 0 0.25rem;">
                {{ $activiteit->titel }}
                @if ($activiteit->status->value === 'geannuleerd')
                    <x-badge type="geannuleerd" />
                @endif
            </h3>
            <p style="font-size: 0.9rem; color: var(--color-brand-muted); margin: 0;">
                {{ substr($activiteit->startuur, 0, 5) }} · {{ $activiteit->locatie }}
            </p>
        </div>
    </a>
@empty
    <p style="color: var(--color-brand-muted); padding: 1rem 0;">{{ __('activities.no_upcoming') }}</p>
@endforelse
```

- [ ] **Step 3: Update the "Alle activiteiten" link to point to the new overview**

The link already points to `nl.activiteiten.index` — that IS the new overview page. No change needed here.

- [ ] **Step 4: Run existing homepage test**

```bash
php artisan test --compact --filter=test_homepage_shows_published_activities
```

Expected: PASS — the test asserts `assertSee($gepubliceerd->titel_nl)` which still holds since the loop is kept.

- [ ] **Step 5: Verify in browser**

Visit `https://harmonie.test/` — confirm activities section shows photo/color band cards rather than text-only cards.

- [ ] **Step 6: Commit**

```bash
git add resources/views/activiteiten/index.blade.php
git commit -m "feat: update homepage activities section with photo-forward cards"
```

---

## Task 7: Run full test suite

- [ ] **Step 1: Run all tests**

```bash
php artisan test --compact
```

Expected: all tests pass. If `ActivityOverzichtTest` fails (it may reference the old overzicht content), update it to reflect the new overview page content.

- [ ] **Step 2: Fix any broken tests**

If `ActivityOverzichtTest.php` fails, open it and update assertions to match the new page content (e.g., assert `Vaste activiteiten` is visible instead of Livewire calendar content).

- [ ] **Step 3: Commit if any test files changed**

```bash
git add tests/
git commit -m "test: update overzicht tests to reflect new invitation page"
```
