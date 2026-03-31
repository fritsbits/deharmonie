# Activiteiten Overzicht Redesign — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the current overview page with a warmer layout: 4 thematic paper cards for recurring activities, plus a bijzondere momenten section with a photo grid, upcoming events sidebar, and Facebook follow block.

**Architecture:** The controller gains a second query for upcoming special activities (`template_id IS NULL`). The view is fully replaced — the thematic section uses hardcoded theme configs (IDs, colors, photos, copy) rendered against the existing `$reeksen` collection. No new models, routes, or Livewire components.

**Tech Stack:** Laravel 13, Blade, inline CSS (no Tailwind — existing page convention), Carbon for date formatting, `ActiviteitTemplate` + `Activiteit` Eloquent models.

---

## File map

| File | Change |
|---|---|
| `lang/nl/activities.php` | Add 4 new translation keys |
| `lang/fr/activities.php` | Add 4 new translation keys |
| `app/Http/Controllers/ActivityController.php` | Add `$bijzondereActiviteiten` query to `index()` |
| `resources/views/activiteiten/overzicht.blade.php` | Full rewrite |
| `tests/Feature/ActiviteitenOverviewTest.php` | Update 1 test + add 3 new tests |

---

## Task 1: Add translation keys

**Files:**
- Modify: `lang/nl/activities.php`
- Modify: `lang/fr/activities.php`

- [ ] **Step 1: Add NL keys**

In `lang/nl/activities.php`, add after `'overview_tagline'`:

```php
'overview_tagline' => 'Elke week staan er vaste activiteiten op het programma — van bewegen tot knutselen en gesprekken. Daarnaast organiseren we bijzondere momenten waarvoor je je beter op voorhand inschrijft.',
'special_moments_intro' => 'Regelmatig organiseren we iets extra\'s — een feest, een uitstap, een themamiddag. Schrijf je in via de site of via het secretariaat.',
'facebook_follow_heading' => 'Volg De Harmonie op Facebook',
'facebook_follow_body' => 'Foto\'s, nieuwtjes en aankondigingen van bijzondere activiteiten',
'upcoming_activities_heading' => 'Aankomende activiteiten',
'upcoming_activities_subline' => 'Inschrijven aanbevolen',
```

Note: `overview_tagline` already exists with old copy — **replace** the existing value, don't add a duplicate.

- [ ] **Step 2: Add FR keys**

In `lang/fr/activities.php`, same positions:

```php
'overview_tagline' => 'Chaque semaine, des activités sont au programme — du sport à la création et aux échanges. Nous organisons aussi des moments forts pour lesquels il vaut mieux s\'inscrire à l\'avance.',
'special_moments_intro' => 'Régulièrement, nous organisons quelque chose de spécial — une fête, une sortie, un après-midi à thème. Inscrivez-vous via le site ou via le secrétariat.',
'facebook_follow_heading' => 'Suivez De Harmonie sur Facebook',
'facebook_follow_body' => 'Photos, actualités et annonces d\'activités spéciales',
'upcoming_activities_heading' => 'Activités à venir',
'upcoming_activities_subline' => 'Inscription recommandée',
```

Again: replace the existing `overview_tagline` value.

- [ ] **Step 3: Run pint**

```bash
vendor/bin/pint lang/nl/activities.php lang/fr/activities.php --format agent
```

- [ ] **Step 4: Commit**

```bash
git add lang/nl/activities.php lang/fr/activities.php
git commit -m "feat: add translation keys for overzicht redesign"
```

---

## Task 2: Controller — add bijzondere activiteiten query

**Files:**
- Modify: `app/Http/Controllers/ActivityController.php`
- Test: `tests/Feature/ActiviteitenOverviewTest.php`

- [ ] **Step 1: Write the failing test**

In `tests/Feature/ActiviteitenOverviewTest.php`, add this test:

```php
public function test_overview_page_passes_bijzondere_activiteiten_to_view(): void
{
    // A special activiteit (template_id IS NULL, future date, published)
    $special = \App\Models\Activiteit::factory()->create([
        'template_id' => null,
        'datum' => now()->addDays(5)->format('Y-m-d'),
        'status' => 'gepubliceerd',
        'titel_nl' => 'Speciale uitstap',
    ]);

    // A recurring activiteit (template_id IS NOT NULL) — should NOT appear
    $template = \App\Models\ActiviteitTemplate::factory()->create();
    $recurring = \App\Models\Activiteit::factory()->create([
        'template_id' => $template->id,
        'datum' => now()->addDays(3)->format('Y-m-d'),
        'status' => 'gepubliceerd',
        'titel_nl' => 'Herhalende activiteit',
    ]);

    $response = $this->get('/activiteiten');
    $response->assertStatus(200);
    $response->assertViewHas('bijzondereActiviteiten');

    $bijzondere = $response->viewData('bijzondereActiviteiten');
    $this->assertTrue($bijzondere->contains('id', $special->id));
    $this->assertFalse($bijzondere->contains('id', $recurring->id));
}
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter=test_overview_page_passes_bijzondere_activiteiten_to_view
```

Expected: FAIL — `ViewData 'bijzondereActiviteiten' missing from view`

- [ ] **Step 3: Update the controller**

Replace the `index()` method in `app/Http/Controllers/ActivityController.php`:

```php
public function index(): View
{
    $reeksen = ActiviteitTemplate::orderBy('dag_van_de_week')
        ->orderBy('startuur')
        ->get()
        ->keyBy('id');

    $bijzondereActiviteiten = Activiteit::whereNull('template_id')
        ->where('datum', '>=', today())
        ->where('status', 'gepubliceerd')
        ->orderBy('datum')
        ->limit(5)
        ->get();

    return view('activiteiten.overzicht', compact('reeksen', 'bijzondereActiviteiten'));
}
```

- [ ] **Step 4: Run test to verify it passes**

```bash
php artisan test --compact --filter=test_overview_page_passes_bijzondere_activiteiten_to_view
```

Expected: PASS

- [ ] **Step 5: Run pint**

```bash
vendor/bin/pint app/Http/Controllers/ActivityController.php --format agent
```

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/ActivityController.php tests/Feature/ActiviteitenOverviewTest.php
git commit -m "feat: pass bijzondereActiviteiten to overzicht view"
```

---

## Task 3: Rewrite the overzicht view

**Files:**
- Modify: `resources/views/activiteiten/overzicht.blade.php`
- Test: `tests/Feature/ActiviteitenOverviewTest.php`

This task replaces the entire view body. Read the existing file once before overwriting so you understand what's being removed.

**Context for the view:**
- `$reeksen` is now a Collection keyed by template `id` (e.g. `$reeksen->get(5)` → the Zumba template)
- `$bijzondereActiviteiten` is a Collection of `Activiteit` models with `template_id IS NULL`
- The `days` translation array maps `dag_van_de_week` int → day name: `1 = maandag`, `2 = dinsdag`, etc.
- Theme groupings are hardcoded. Template IDs per theme (based on production data):
  - **Beweeg mee**: `[5, 7, 11, 15]`
  - **Maak iets**: `[10, 12, 13]`
  - **Praat & leer**: `[1, 2, 3, 4, 6, 8]`
  - **Vier mee**: `[9, 14, 16, 17, 18]`

- [ ] **Step 1: Update the existing `test_overview_page_shows_reeksen` test**

The current test asserts a specific template title appears in the page. With the new hardcoded theme grouping by ID, this is unreliable (IDs in test DB won't match production IDs). Replace this test with one that asserts theme names appear:

In `tests/Feature/ActiviteitenOverviewTest.php`, replace `test_overview_page_shows_reeksen`:

```php
public function test_overview_page_shows_theme_names(): void
{
    $response = $this->get('/activiteiten');
    $response->assertStatus(200);
    $response->assertSee('Beweeg mee');
    $response->assertSee('Maak iets');
    $response->assertSee('Praat &amp; leer');
    $response->assertSee('Vier mee');
}
```

Note: `assertSee` HTML-encodes by default, so `&` in "Praat & leer" becomes `&amp;` — use `assertSee('Praat &amp; leer')` or `assertSeeText('Praat & leer')`.

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact --filter=test_overview_page_shows_theme_names
```

Expected: FAIL — theme names not in the current view

- [ ] **Step 3: Write the new view**

Replace `resources/views/activiteiten/overzicht.blade.php` entirely with:

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

{{-- THEMATIC CARDS --}}
<section style="background: #fff8f5; padding: 5rem 1.5rem 6rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="blue" mb="0.75rem">{{ __('activities.reeksen_eyebrow') }}</x-eyebrow>
        <x-section-heading mb="0">{{ __('activities.reeksen_heading') }}</x-section-heading>

        @php
            $days = __('activities.days');
            $isFr = app()->getLocale() === 'fr';

            $themes = [
                [
                    'name'       => $isFr ? 'Bougez avec nous' : 'Beweeg mee',
                    'tagline'    => $isFr ? 'À votre rythme — pas besoin d\'être sportif.' : 'Op eigen tempo — je hoeft geen sportman te zijn.',
                    'color'      => '#eb6643',
                    'photo'      => 'photo-groep-actief.webp',
                    'tape'       => null,
                    'rotate'     => '-2deg',
                    'margin_top' => '1.5rem',
                    'ids'        => [5, 7, 11, 15],
                ],
                [
                    'name'       => $isFr ? 'Créez ensemble' : 'Maak iets',
                    'tagline'    => $isFr ? 'Les mains à l\'ouvrage — calme, convivial, ensemble.' : 'Met de handen bezig — rustig, gezellig, samen.',
                    'color'      => '#81b59c',
                    'photo'      => 'photo-visitors-2.webp',
                    'tape'       => 'rgba(129,181,156,0.2)',
                    'rotate'     => '1.8deg',
                    'margin_top' => '0',
                    'ids'        => [10, 12, 13],
                ],
                [
                    'name'       => $isFr ? 'Parlez & apprenez' : 'Praat & leer',
                    'tagline'    => $isFr ? 'Quatre langues, la mémoire, le numérique.' : 'Vier talen, het geheugen oefenen, digitaal leren.',
                    'color'      => '#4679bc',
                    'photo'      => 'photo-samen.webp',
                    'tape'       => 'rgba(70,121,188,0.15)',
                    'rotate'     => '-1deg',
                    'margin_top' => '2.5rem',
                    'ids'        => [1, 2, 3, 4, 6, 8],
                ],
                [
                    'name'       => $isFr ? 'Fêtez avec nous' : 'Vier mee',
                    'tagline'    => $isFr ? 'Bingo, sorties, anniversaires — toujours de la compagnie.' : 'Bingo, uitstappen, verjaardagen — altijd gezelschap.',
                    'color'      => '#d4956a',
                    'photo'      => 'photo-party.webp',
                    'tape'       => 'rgba(235,102,67,0.18)',
                    'rotate'     => '2.2deg',
                    'margin_top' => '0.5rem',
                    'ids'        => [9, 14, 16, 17, 18],
                ],
            ];
        @endphp

        <div class="theme-cards" style="display: flex; gap: 2rem; align-items: flex-start; margin-top: 3rem; padding-bottom: 2rem;">
            @foreach ($themes as $theme)
                @php
                    $templates = $reeksen->only($theme['ids'])->values();
                @endphp
                <div class="theme-card" style="flex: 1; background: white; border-radius: 2px; border: 1px solid rgba(44,40,38,0.07); position: relative; transform: rotate({{ $theme['rotate'] }}); margin-top: {{ $theme['margin_top'] }};">
                    {{-- tape decoration --}}
                    @if ($theme['tape'])
                        <div style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%) rotate(-1.5deg); width: 55px; height: 20px; background: {{ $theme['tape'] }}; border-radius: 2px; z-index: 10;"></div>
                    @endif

                    {{-- color band --}}
                    <div style="height: 4px; background: {{ $theme['color'] }};"></div>

                    {{-- photo --}}
                    <div style="height: 140px; overflow: hidden;">
                        <img src="{{ asset('images/' . $theme['photo']) }}" alt=""
                             style="width: 100%; height: 100%; object-fit: cover; display: block;">
                    </div>

                    {{-- card body --}}
                    <div style="padding: 1.25rem 1.5rem 1.75rem;">
                        <p style="font-family: var(--font-sans); font-size: 1.25rem; font-weight: 900; color: var(--color-brand-dark); margin: 0 0 0.25rem;">
                            {{ $theme['name'] }}
                        </p>
                        <p style="font-size: 0.9rem; color: var(--color-brand-muted); line-height: 1.5; margin-bottom: 1rem; font-style: italic;">
                            {{ $theme['tagline'] }}
                        </p>
                        <ul style="list-style: none; padding: 0; border-top: 1px dashed #e4dbd3; padding-top: 0.75rem;">
                            @foreach ($templates as $t)
                                @php
                                    $dag = $days[$t->dag_van_de_week] ?? '';
                                    $uur = substr($t->startuur, 0, 5);
                                    $titel = $isFr ? ($t->titel_fr ?? $t->titel_nl) : $t->titel_nl;
                                @endphp
                                <li style="font-size: 0.875rem; color: var(--color-brand-dark); padding: 0.3rem 0; display: flex; justify-content: space-between; align-items: baseline; gap: 0.5rem; border-bottom: 1px solid rgba(44,40,38,0.05);">
                                    <span style="font-weight: 600;">{{ $titel }}</span>
                                    <span style="font-size: 0.75rem; color: var(--color-brand-muted); white-space: nowrap; flex-shrink: 0;">
                                        {{ ucfirst($dag) }} · {{ $uur }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- CTA --}}
        <div style="text-align: center; margin-top: 2.5rem;">
            <a href="{{ route(app()->getLocale() . '.activiteiten.agenda') }}"
               style="display: inline-block; border: 2px solid var(--color-brand-dark); color: var(--color-brand-dark); padding: 0.875rem 2.25rem; border-radius: 6px; font-family: var(--font-sans); font-weight: 700; font-size: 1.0625rem; text-decoration: none;">
                {{ __('activities.agenda_link') }} →
            </a>
        </div>
    </div>
</section>

{{-- BIJZONDERE MOMENTEN --}}
<section style="background: #eef5f1; padding: 5rem 1.5rem 5.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="green" mb="0.75rem">{{ __('activities.special_moments_eyebrow') }}</x-eyebrow>
        <x-section-heading mb="0.75rem">{{ __('activities.special_moments_heading') }}</x-section-heading>
        <p style="font-size: 1.1rem; color: var(--color-brand-muted); line-height: 1.6; max-width: 600px; margin-bottom: 0;">
            {{ __('activities.special_moments_intro') }}
        </p>

        {{-- 3-col grid: big photo | 2 stacked photos | upcoming events --}}
        <div class="moments-layout" style="display: grid; grid-template-columns: 2fr 1.5fr 1.5fr; grid-template-rows: 240px 220px; gap: 0.875rem; margin-top: 2rem;">

            {{-- Big photo — spans both rows --}}
            <div style="grid-row: 1 / 3; border-radius: 10px; overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-feest-2.webp') }}" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem 1.25rem 1rem; background: linear-gradient(to top, rgba(20,16,14,0.85) 0%, rgba(20,16,14,0.5) 50%, transparent 100%);">
                    <p style="font-family: var(--font-sans); font-weight: 800; font-size: 1rem; color: white; margin: 0; line-height: 1.3; text-shadow: 0 1px 4px rgba(0,0,0,0.4);">Feest van 51 jaar De Harmonie</p>
                </div>
            </div>

            {{-- Middle top photo --}}
            <div style="border-radius: 10px; overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-buiten-event.webp') }}" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 1.5rem 1rem 0.75rem; background: linear-gradient(to top, rgba(20,16,14,0.85) 0%, rgba(20,16,14,0.5) 50%, transparent 100%);">
                    <p style="font-family: var(--font-sans); font-weight: 800; font-size: 0.9rem; color: white; margin: 0; text-shadow: 0 1px 4px rgba(0,0,0,0.4);">Culturele uitstap</p>
                </div>
            </div>

            {{-- Upcoming events card — spans both rows --}}
            <div style="grid-row: 1 / 3; background: white; border-radius: 10px; border: 1px solid #d8ece5; overflow: hidden; display: flex; flex-direction: column;">
                <div style="padding: 1rem 1.25rem 0.75rem; border-bottom: 1px solid #eef5f1;">
                    <p style="font-family: var(--font-sans); font-size: 0.9375rem; font-weight: 800; color: var(--color-brand-dark); margin: 0 0 0.1rem;">
                        {{ __('activities.upcoming_activities_heading') }}
                    </p>
                    <p style="font-size: 0.75rem; color: var(--color-brand-muted); margin: 0;">
                        {{ __('activities.upcoming_activities_subline') }}
                    </p>
                </div>

                @forelse ($bijzondereActiviteiten as $activiteit)
                    @php
                        $date = \Carbon\Carbon::parse($activiteit->datum);
                        $titel = app()->getLocale() === 'fr'
                            ? ($activiteit->titel_fr ?? $activiteit->titel_nl)
                            : $activiteit->titel_nl;
                        $uur = substr($activiteit->startuur, 0, 5);
                    @endphp
                    <div style="display: flex; align-items: flex-start; gap: 0.875rem; padding: 0.875rem 1.25rem; border-bottom: 1px solid #f0ebe6;">
                        <div style="flex-shrink: 0; width: 40px; text-align: center;">
                            <div style="font-family: var(--font-sans); font-size: 1.25rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1;">
                                {{ $date->format('d') }}
                            </div>
                            <div style="font-family: var(--font-sans); font-size: 0.6rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-top: 0.1rem;">
                                {{ $date->locale(app()->getLocale())->isoFormat('MMM') }}
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-dark); margin: 0 0 0.2rem; line-height: 1.3;">
                                {{ $titel }}
                            </p>
                            <p style="font-size: 0.775rem; color: var(--color-brand-muted); margin: 0 0 0.35rem;">
                                {{ $uur }}
                            </p>
                            <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                               style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; color: var(--color-brand-orange); text-decoration: none;">
                                {{ __('activities.register') }} →
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="padding: 1.5rem 1.25rem; color: var(--color-brand-muted); font-size: 0.875rem;">
                        {{ __('activities.no_upcoming') }}
                    </div>
                @endforelse
            </div>

            {{-- Middle bottom photo --}}
            <div style="border-radius: 10px; overflow: hidden; position: relative;">
                <img src="{{ asset('images/photo-cake.jpg') }}" alt=""
                     style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 1.5rem 1rem 0.75rem; background: linear-gradient(to top, rgba(20,16,14,0.85) 0%, rgba(20,16,14,0.5) 50%, transparent 100%);">
                    <p style="font-family: var(--font-sans); font-weight: 800; font-size: 0.9rem; color: white; margin: 0; text-shadow: 0 1px 4px rgba(0,0,0,0.4);">Verjaardagsfeest</p>
                </div>
            </div>

        </div>

        {{-- Facebook follow block --}}
        <div style="margin-top: 1.5rem; display: flex; align-items: center; gap: 1.25rem; background: white; border-radius: 10px; padding: 1.25rem 1.5rem; border: 1px solid #d8ece5;">
            <div style="width: 40px; height: 40px; background: #1877f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg viewBox="0 0 24 24" style="fill: white; width: 22px; height: 22px;" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.235 2.686.235v2.97h-1.514c-1.491 0-1.956.93-1.956 1.886v2.268h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                </svg>
            </div>
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-weight: 700; font-size: 1rem; color: var(--color-brand-dark); margin: 0 0 0.1rem;">
                    {{ __('activities.facebook_follow_heading') }}
                </p>
                <p style="font-size: 0.875rem; color: var(--color-brand-muted); margin: 0;">
                    {{ __('activities.facebook_follow_body') }}
                </p>
            </div>
            <a href="https://www.facebook.com/deharmoniebrussel/" target="_blank" rel="noopener"
               style="font-family: var(--font-sans); font-weight: 700; font-size: 0.875rem; color: #1877f2; text-decoration: none; white-space: nowrap; flex-shrink: 0;">
                facebook.com/deharmoniebrussel →
            </a>
        </div>
    </div>
</section>

<style>
@media (max-width: 767px) {
    .theme-cards { flex-direction: column !important; gap: 3rem !important; padding-bottom: 1rem !important; }
    .theme-card { margin-top: 0 !important; transform: none !important; }
    .moments-layout { grid-template-columns: 1fr !important; grid-template-rows: 260px auto auto !important; }
    .moments-layout > div[style*="grid-row: 1 / 3"] { grid-row: auto !important; }
}
</style>

@endsection
```

- [ ] **Step 4: Run the theme names test**

```bash
php artisan test --compact --filter=test_overview_page_shows_theme_names
```

Expected: PASS

- [ ] **Step 5: Run all overview tests**

```bash
php artisan test --compact tests/Feature/ActiviteitenOverviewTest.php
```

Expected: all pass (including the controller test from Task 2)

- [ ] **Step 6: Commit**

```bash
git add resources/views/activiteiten/overzicht.blade.php tests/Feature/ActiviteitenOverviewTest.php
git commit -m "feat: rewrite activiteiten overzicht with thematic cards and bijzondere momenten"
```

---

## Task 4: Add remaining tests

**Files:**
- Test: `tests/Feature/ActiviteitenOverviewTest.php`

- [ ] **Step 1: Add upcoming events test**

Add to `tests/Feature/ActiviteitenOverviewTest.php`:

```php
public function test_bijzondere_momenten_shows_upcoming_special_activities(): void
{
    $special = \App\Models\Activiteit::factory()->create([
        'template_id' => null,
        'datum' => now()->addDays(10)->format('Y-m-d'),
        'status' => 'gepubliceerd',
        'titel_nl' => 'Zomerfeest',
    ]);

    // Past activity — should NOT appear
    \App\Models\Activiteit::factory()->create([
        'template_id' => null,
        'datum' => now()->subDay()->format('Y-m-d'),
        'status' => 'gepubliceerd',
        'titel_nl' => 'Oud evenement',
    ]);

    // Draft — should NOT appear
    \App\Models\Activiteit::factory()->create([
        'template_id' => null,
        'datum' => now()->addDays(3)->format('Y-m-d'),
        'status' => 'concept',
        'titel_nl' => 'Ongepubliceerd',
    ]);

    $response = $this->get('/activiteiten');
    $response->assertStatus(200);
    $response->assertSee('Zomerfeest');
    $response->assertDontSee('Oud evenement');
    $response->assertDontSee('Ongepubliceerd');
}

public function test_agenda_cta_link_correct_for_nl(): void
{
    $response = $this->get('/activiteiten');
    $response->assertStatus(200);
    $response->assertSee('/activiteiten/agenda');
}

public function test_agenda_cta_link_correct_for_fr(): void
{
    $response = $this->get('/fr/activites');
    $response->assertStatus(200);
    $response->assertSee('/fr/activites/agenda');
}
```

- [ ] **Step 2: Run the new tests**

```bash
php artisan test --compact tests/Feature/ActiviteitenOverviewTest.php
```

Expected: all 7 tests pass

- [ ] **Step 3: Run full test suite to confirm no regressions**

```bash
php artisan test --compact
```

Expected: all green

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/ActiviteitenOverviewTest.php
git commit -m "test: add bijzondere momenten and agenda CTA tests for overzicht"
```

---

## Self-review

**Spec coverage:**
- ✅ Hero lead copy — Task 1 (translation key) + Task 3 (view uses `overview_tagline`)
- ✅ Photo strip removed — Task 3 (not in new view)
- ✅ 4 theme cards, paper aesthetic, staggered — Task 3
- ✅ Theme names NL + FR — Task 3 (`$isFr` switch in `$themes` array)
- ✅ Tape decorations — Task 3
- ✅ Photos per theme — Task 3
- ✅ Activity list per card with day + time — Task 3
- ✅ Agenda ghost button below cards — Task 3
- ✅ Bijzondere momenten 3-col grid — Task 3
- ✅ Upcoming events sidebar card — Task 3 + Task 2 (controller query)
- ✅ `template_id IS NULL` filter, `status = gepubliceerd`, future dates only — Task 2
- ✅ Intro copy + Facebook follow block — Task 3
- ✅ New translation keys incl. FR — Task 1
- ✅ Mobile responsive — Task 3 (`<style>` block)
- ✅ Existing tests preserved — Task 3 (updated theme names test), Task 4

**No placeholders found.**

**Type consistency:** `$reeksen->only($theme['ids'])` — `only()` works on keyed collections (keyed by `id` in controller). Consistent throughout. `$bijzondereActiviteiten` named identically in controller and view. `route(app()->getLocale() . '.activiteiten.show', $activiteit->slug)` — matches existing route naming convention (`nl.activiteiten.show`).
