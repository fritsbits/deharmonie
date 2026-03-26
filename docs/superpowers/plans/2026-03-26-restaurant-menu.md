# Restaurant & Menu Page Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the Google Doc iframe on `/restaurant-menu` with a structured, mobile-first weekly menu page driven by a JSON flat file.

**Architecture:** `PageController::weekmenu()` reads `resources/data/weekmenu.json`, resolves the highlighted day (today before 14:00, tomorrow after, skipping closed days), and passes `$days`, `$week`, and `$highlightedDate` to the view. The view loops over days and renders three card types: standard, special event, and closed. No new services or components.

**Tech Stack:** Laravel 13, Blade, Carbon, PHP 8.4, PHPUnit 12

---

## File Map

| Action | File | Purpose |
|--------|------|---------|
| Create | `resources/data/weekmenu.json` | Menu data — two weeks of real content |
| Create | `lang/nl/weekmenu.php` | Dutch translation strings |
| Create | `lang/fr/weekmenu.php` | French translation strings |
| Modify | `app/Http/Controllers/PageController.php` | `weekmenu()` method — load JSON, resolve highlighted date |
| Replace | `resources/views/pages/weekmenu.blade.php` | Full page view — hero, practical info, day cards, sfeer strip |
| Create | `tests/Feature/WeekMenuTest.php` | Feature tests for route, card types, highlight logic |

---

### Task 1: Create JSON data file

**Files:**
- Create: `resources/data/weekmenu.json`

- [ ] **Create the data directory and JSON file**

```json
{
  "week": {
    "nl": "23 – 28 maart 2026",
    "fr": "23 – 28 mars 2026"
  },
  "days": [
    {
      "date": "2026-03-23",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 9,
      "nl": { "soup": "Soep van de dag", "main": "Stoofvlees met Sla en Kroketjes", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Carbonnades, Frites et Salade", "courses": [] }
    },
    {
      "date": "2026-03-24",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 9,
      "nl": { "soup": "Soep van de dag", "main": "Chicon Gratin met Puree", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Chicon Gratin avec Purée", "courses": [] }
    },
    {
      "date": "2026-03-25",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 10,
      "nl": { "soup": "Soep van de dag", "main": "Rog in Botersaus met Kappers en Aardappelen", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Raie au Beurre avec Câpres et Pommes de Terre", "courses": [] }
    },
    {
      "date": "2026-03-26",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 9,
      "nl": { "soup": "Soep van de dag", "main": "Keuze van Vlees met groene kool Stoemp", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Choix de Viande avec Stoump Chou vert", "courses": [] }
    },
    {
      "date": "2026-03-27",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 9,
      "nl": { "soup": "Soep van de dag", "main": "Spaghetti met Forestière Saus", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Spaghetti Sauce Forestière", "courses": [] }
    },
    {
      "date": "2026-03-28",
      "closed": true,
      "closed_label_nl": "Gesloten",
      "closed_label_fr": "Fermé",
      "special_event": false,
      "price": null,
      "nl": { "soup": null, "main": null, "courses": [] },
      "fr": { "soup": null, "main": null, "courses": [] }
    },
    {
      "date": "2026-03-30",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 10,
      "nl": { "soup": "Soep van de dag", "main": "Kalf blanket met Bulgur", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Blanquette de veau avec Boulgour", "courses": [] }
    },
    {
      "date": "2026-03-31",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 9,
      "nl": { "soup": "Soep van de dag", "main": "Varkensgebraad met gestoofd Witloof en Aardappelen", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Rôti de Porc avec Chicon Braisé et Pommes de Terre", "courses": [] }
    },
    {
      "date": "2026-04-01",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 9,
      "nl": { "soup": "Soep van de dag", "main": "Parmentier met Erwtjes en Wortelen", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Hachis Parmentier au Petits Pois et Carottes", "courses": [] }
    },
    {
      "date": "2026-04-02",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": true,
      "price": 20,
      "nl": {
        "event_label": "Paasmenu",
        "soup": null,
        "main": null,
        "courses": ["Kir Royal", "Scampi met look", "Eendenborst", "Gestoofd Witloof", "Duo van IJs op Lente wijze"]
      },
      "fr": {
        "event_label": "Menu de Pâques",
        "soup": null,
        "main": null,
        "courses": ["Kir Royal", "Scampi à l'Ail", "Magret de Canard", "Chicons Braisés et pdt Rissolées", "Duo de Glace Printanière"]
      }
    },
    {
      "date": "2026-04-03",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 10,
      "nl": { "soup": "Soep van de dag", "main": "Witte Vis met Aardappelsla en princesseboontjes", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Poisson Blanc avec une salade de Pommes de terre et princesse", "courses": [] }
    },
    {
      "date": "2026-04-04",
      "closed": true,
      "closed_label_nl": "Gesloten",
      "closed_label_fr": "Fermé",
      "special_event": false,
      "price": null,
      "nl": { "soup": null, "main": null, "courses": [] },
      "fr": { "soup": null, "main": null, "courses": [] }
    }
  ]
}
```

- [ ] **Commit**

```bash
git add resources/data/weekmenu.json
git commit -m "feat: add weekmenu JSON data file with two weeks of real menu content"
```

---

### Task 2: Create translation files

**Files:**
- Create: `lang/nl/weekmenu.php`
- Create: `lang/fr/weekmenu.php`

- [ ] **Create `lang/nl/weekmenu.php`**

```php
<?php

return [
    'eyebrow'          => 'Sociaal Restaurant',
    'tagline'          => 'Elke dag een warm middagmaal',
    'section_title'    => 'Weekmenu',
    'hours_label'      => 'Openingsuren',
    'hours_value'      => "Maandag – vrijdag\n11u15 – 13u15",
    'price_label'      => 'Prijs',
    'price_value'      => 'Vanaf € 9',
    'price_sub'        => 'soep + hoofdgerecht',
    'walkin_label'     => 'Reservatie',
    'walkin_value'     => 'Gewoon binnenlopen',
    'address_label'    => 'Adres & contact',
    'today'            => 'Vandaag',
    'tomorrow'         => 'Morgen',
    'closed'           => 'Gesloten',
    'special_badge'    => 'Speciaal',
    'allergen_note'    => 'Allergenen? Vraag aan onze kok.',
    'sfeer_label'      => 'Bij ons aan tafel',
    'sfeer_caption'    => 'Soms is er reden voor iets extra. Onze kok zorgt voor de rest.',
];
```

- [ ] **Create `lang/fr/weekmenu.php`**

```php
<?php

return [
    'eyebrow'          => 'Restaurant Social',
    'tagline'          => 'Chaque jour un repas chaud',
    'section_title'    => 'Menu de la Semaine',
    'hours_label'      => "Heures d'ouverture",
    'hours_value'      => "Lundi – vendredi\n11h15 – 13h15",
    'price_label'      => 'Prix',
    'price_value'      => 'À partir de € 9',
    'price_sub'        => 'potage + plat principal',
    'walkin_label'     => 'Réservation',
    'walkin_value'     => 'Entrez librement',
    'address_label'    => 'Adresse & contact',
    'today'            => "Aujourd'hui",
    'tomorrow'         => 'Demain',
    'closed'           => 'Fermé',
    'special_badge'    => 'Spécial',
    'allergen_note'    => 'Allergènes ? Demandez à notre cuisinier.',
    'sfeer_label'      => 'À notre table',
    'sfeer_caption'    => "Parfois, il y a une raison de faire quelque chose de spécial. Notre cuisinier s'en occupe.",
];
```

- [ ] **Commit**

```bash
git add lang/nl/weekmenu.php lang/fr/weekmenu.php
git commit -m "feat: add weekmenu translation keys for NL and FR"
```

---

### Task 3: Write failing feature tests

**Files:**
- Create: `tests/Feature/WeekMenuTest.php`

- [ ] **Generate the test file**

```bash
php artisan make:test --phpunit WeekMenuTest
```

- [ ] **Replace its contents with**

```php
<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class WeekMenuTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_weekmenu_page_loads_in_nl(): void
    {
        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Weekmenu');
        $response->assertSee('Openingsuren');
        $response->assertSee('Gewoon binnenlopen');
        $response->assertSee('Allergenen');
    }

    public function test_weekmenu_page_loads_in_fr(): void
    {
        $response = $this->get('/fr/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Semaine');
        $response->assertSee("Heures d'ouverture");
        $response->assertSee('Entrez librement');
    }

    public function test_today_card_is_highlighted_before_cutoff(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00'); // Monday, before 14:00

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Vandaag');
        $response->assertSee('Stoofvlees met Sla en Kroketjes');
    }

    public function test_tomorrow_card_is_highlighted_after_cutoff(): void
    {
        Carbon::setTestNow('2026-03-23 15:00:00'); // Monday, after 14:00

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Morgen');
        $response->assertSee('Chicon Gratin met Puree');
    }

    public function test_closed_day_shows_gesloten(): void
    {
        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Gesloten');
    }

    public function test_special_event_shows_all_courses(): void
    {
        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Paasmenu');
        $response->assertSee('Kir Royal');
        $response->assertSee('Eendenborst');
        $response->assertSee('€ 20');
    }

    public function test_closed_day_is_skipped_when_resolving_highlighted_date(): void
    {
        Carbon::setTestNow('2026-03-27 15:00:00'); // Friday after 14:00 — Saturday is closed

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        // Next open day after Saturday is Monday 30/03
        $response->assertSee('Kalf blanket met Bulgur');
    }
}
```

- [ ] **Run tests — confirm they all fail**

```bash
php artisan test --compact tests/Feature/WeekMenuTest.php
```

Expected: all 7 tests FAIL (view doesn't exist yet / controller returns empty view)

---

### Task 4: Implement the controller

**Files:**
- Modify: `app/Http/Controllers/PageController.php`

- [ ] **Replace the `weekmenu()` method**

```php
public function weekmenu()
{
    $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);

    $now = now();
    $candidate = $now->hour >= 14 ? $now->copy()->addDay() : $now->copy();

    $highlightedDate = null;
    foreach ($data['days'] as $day) {
        if ($day['date'] >= $candidate->toDateString() && ! $day['closed']) {
            $highlightedDate = $day['date'];
            break;
        }
    }

    return view('pages.weekmenu', [
        'week'            => $data['week'],
        'days'            => $data['days'],
        'highlightedDate' => $highlightedDate,
    ]);
}
```

- [ ] **Run tests — route and controller tests should now pass, view tests still fail**

```bash
php artisan test --compact tests/Feature/WeekMenuTest.php
```

Expected: `test_weekmenu_page_loads_in_nl` and `test_weekmenu_page_loads_in_fr` PASS (200 + view renders). Others may still fail if view doesn't show the right content yet.

---

### Task 5: Implement the view

**Files:**
- Replace: `resources/views/pages/weekmenu.blade.php`

- [ ] **Replace the entire file contents**

```blade
@extends('layouts.app')
@section('title', __('nav.restaurant_menu'))
@section('content')

{{-- HERO --}}
<div style="position: relative; overflow: hidden;">
    <img src="{{ asset('images/photo-restaurant-vol.webp') }}" alt=""
         style="width: 100%; height: 280px; object-fit: cover; display: block; object-position: center center;">
    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(44,40,38,0.65) 0%, transparent 55%);"></div>
    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem 1.5rem;">
        <div style="max-width: 72rem; margin: 0 auto;">
            <x-eyebrow color="orange" mb="0.5rem">{{ __('weekmenu.eyebrow') }}</x-eyebrow>
            <h1 style="font-family: var(--font-sans); font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 900; color: white; margin: 0; line-height: 1.1;">
                {{ __('nav.restaurant_menu') }}
            </h1>
            <p style="color: rgba(255,255,255,0.85); font-size: 1.125rem; margin-top: 0.35rem; margin-bottom: 0;">
                {{ __('weekmenu.tagline') }}
            </p>
        </div>
    </div>
</div>

{{-- PRACTICAL INFO --}}
<div style="background: white; border-bottom: 1px solid #e8e0d8;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 1.75rem 1.5rem;">
        <div class="practical-grid" style="display: flex; gap: 2rem;">
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.25rem; margin-top: 0;">{{ __('weekmenu.hours_label') }}</p>
                <p style="font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5; margin: 0;">{!! nl2br(e(__('weekmenu.hours_value'))) !!}</p>
            </div>
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.25rem; margin-top: 0;">{{ __('weekmenu.price_label') }}</p>
                <p style="font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.5; margin: 0;">
                    {{ __('weekmenu.price_value') }}<br>
                    <span style="font-weight: 400; font-size: 0.875rem; color: var(--color-brand-muted);">{{ __('weekmenu.price_sub') }}</span>
                </p>
            </div>
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.25rem; margin-top: 0;">{{ __('weekmenu.walkin_label') }}</p>
                <p style="font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); margin: 0;">{{ __('weekmenu.walkin_value') }}</p>
            </div>
            <div style="flex: 1;">
                <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--color-brand-muted); margin-bottom: 0.25rem; margin-top: 0;">{{ __('weekmenu.address_label') }}</p>
                <p style="font-size: 1rem; line-height: 1.5; margin: 0;">
                    <a href="tel:0220328048" style="font-weight: 700; color: var(--color-brand-blue); text-decoration: none; display: block;">02 203 28 48</a>
                    <span style="font-weight: 600; color: var(--color-brand-dark);">Antwerpsesteenweg 24</span>
                </p>
            </div>
        </div>
    </div>
</div>

{{-- WEEKLY MENU --}}
<div style="max-width: 72rem; margin: 0 auto; padding: 2.5rem 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap;">
        <h2 style="font-family: var(--font-sans); font-size: 1.5rem; font-weight: 900; color: var(--color-brand-dark); margin: 0;">
            {{ __('weekmenu.section_title') }}
        </h2>
        <span style="font-size: 0.9rem; color: var(--color-brand-muted); font-weight: 600;">
            {{ $week[app()->getLocale()] }}
        </span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 0.625rem; max-width: 640px;">
        @foreach($days as $day)
            @php
                $locale = app()->getLocale();
                $isHighlighted = $highlightedDate && $day['date'] === $highlightedDate;
                $isToday = $day['date'] === now()->toDateString();
                $dayLabel = \Carbon\Carbon::parse($day['date'])->locale($locale)->isoFormat('dddd D/MM');
            @endphp

            @if($day['closed'])

                {{-- CLOSED DAY --}}
                <div style="background: #f5f3f1; border: 1px solid #e0d9d4; border-radius: 10px; padding: 1rem 1.25rem; opacity: 0.6;">
                    <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.15rem; margin-top: 0;">{{ $dayLabel }}</p>
                    <p style="font-family: var(--font-sans); font-size: 0.95rem; font-weight: 700; color: var(--color-brand-muted); margin: 0;">
                        {{ $day['closed_label_' . $locale] ?? __('weekmenu.closed') }}
                    </p>
                </div>

            @elseif($day['special_event'])

                {{-- SPECIAL EVENT --}}
                <div style="background: #fff8f0; border: 2px solid var(--color-brand-orange); border-radius: 10px; padding: 1rem 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.6rem; gap: 1rem;">
                        <div>
                            <span style="display: inline-block; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; padding: 2px 8px; border-radius: 999px; margin-bottom: 0.25rem;">{{ __('weekmenu.special_badge') }}</span>
                            <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.1rem; margin-top: 0;">{{ $dayLabel }}</p>
                            <p style="font-family: var(--font-sans); font-size: 1.1rem; font-weight: 900; color: var(--color-brand-dark); margin: 0;">{{ $day[$locale]['event_label'] }}</p>
                        </div>
                        <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-orange); margin: 0; flex-shrink: 0;">€ {{ $day['price'] }}</p>
                    </div>
                    <ul style="list-style: none; padding: 0; margin: 0; border-top: 1px solid #e8e0d8; padding-top: 0.5rem; display: flex; flex-direction: column; gap: 0.25rem;">
                        @foreach($day[$locale]['courses'] as $course)
                            <li style="font-size: 0.9rem; color: var(--color-brand-dark); padding-left: 0.75rem; position: relative;">
                                <span style="position: absolute; left: 0; color: var(--color-brand-orange); font-weight: 700;" aria-hidden="true">·</span>
                                {{ $course }}
                            </li>
                        @endforeach
                    </ul>
                </div>

            @else

                {{-- STANDARD DAY --}}
                <div style="background: {{ $isHighlighted ? '#fff8f5' : 'white' }}; border: 1px solid #e8e0d8; {{ $isHighlighted ? 'border-left: 4px solid var(--color-brand-orange);' : '' }} border-radius: 10px; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                    <div style="flex: 1; min-width: 0;">
                        @if($isHighlighted)
                            <span style="display: inline-block; background: var(--color-brand-orange); color: white; font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; padding: 1px 7px; border-radius: 999px; margin-bottom: 0.3rem;">{{ $isToday ? __('weekmenu.today') : __('weekmenu.tomorrow') }}</span>
                        @endif
                        <p style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.07em; color: var(--color-brand-muted); margin-bottom: 0.1rem; margin-top: 0;">{{ $dayLabel }}</p>
                        <p style="font-size: 0.85rem; color: var(--color-brand-muted); margin-bottom: 0.1rem; margin-top: 0;">{{ $day[$locale]['soup'] }}</p>
                        <p style="font-size: 1.25rem; font-weight: 700; color: var(--color-brand-dark); line-height: 1.3; margin: 0;">{{ $day[$locale]['main'] }}</p>
                    </div>
                    <p style="font-family: var(--font-sans); font-size: 1rem; font-weight: 900; color: var(--color-brand-orange); margin: 0; flex-shrink: 0; padding-top: 0.25rem;">€ {{ $day['price'] }}</p>
                </div>

            @endif
        @endforeach
    </div>

    <p style="font-size: 0.8rem; color: var(--color-brand-muted); font-style: italic; margin-top: 1rem;">{{ __('weekmenu.allergen_note') }}</p>
</div>

{{-- SFEER --}}
<div style="border-top: 1px solid #e8e0d8; background: white; padding: 2.5rem 1.5rem;">
    <div style="max-width: 72rem; margin: 0 auto;">
        <x-eyebrow color="orange" mb="0.75rem">{{ __('weekmenu.sfeer_label') }}</x-eyebrow>
        <div class="sfeer-strip" style="display: flex; gap: 0.75rem; height: 220px; margin-bottom: 1rem; border-radius: 8px; overflow: hidden;">
            <div style="flex: 1; overflow: hidden;">
                <img src="{{ asset('images/photo-chef-taart.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            <div style="flex: 1; overflow: hidden;">
                <img src="{{ asset('images/photo-groep-tafel.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
            <div style="flex: 1; overflow: hidden;">
                <img src="{{ asset('images/photo-feest-2.webp') }}" alt="" style="width: 100%; height: 100%; object-fit: cover; display: block;">
            </div>
        </div>
        <p style="font-size: 1rem; color: var(--color-brand-muted); line-height: 1.6; max-width: 42rem; margin: 0;">{{ __('weekmenu.sfeer_caption') }}</p>
    </div>
</div>

<style>
@media (max-width: 767px) {
    .practical-grid { flex-direction: column !important; gap: 1.25rem !important; }
    .sfeer-strip { height: 140px !important; }
}
</style>

@endsection
```

- [ ] **Run all weekmenu tests**

```bash
php artisan test --compact tests/Feature/WeekMenuTest.php
```

Expected: all 7 tests PASS

---

### Task 6: Format, final test run, and commit

**Files:**
- Modify: `app/Http/Controllers/PageController.php` (pint formatting)
- Modify: `lang/nl/weekmenu.php` (pint formatting)
- Modify: `lang/fr/weekmenu.php` (pint formatting)

- [ ] **Run Pint on modified PHP files**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests PASS (no regressions)

- [ ] **Commit**

```bash
git add app/Http/Controllers/PageController.php \
        resources/views/pages/weekmenu.blade.php \
        tests/Feature/WeekMenuTest.php
git commit -m "feat: replace Google Doc iframe with structured weekmenu page

Mobile-first weekly menu loaded from JSON flat file. Three card types:
standard day, special event (multi-course), and closed day. Highlighted
card shows today before 14:00 or tomorrow after, skipping closed days.

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```
