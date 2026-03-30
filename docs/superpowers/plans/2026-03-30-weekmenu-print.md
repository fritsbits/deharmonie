# Weekmenu Print / PDF View — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a locale-aware print/PDF view for the week menu, accessible via a small link in the Livewire menu card.

**Architecture:** A new `PageController::weekmenuPrint()` method reads `weekmenu.json`, filters to the requested week (via `?week=` query param), and renders a standalone Blade view. The view has a fixed on-brand top bar with a print button, and `@media print` styles that hide the bar and render clean A4 output. The Livewire component appends the current `$weekOffset` to the print URL.

**Tech Stack:** Laravel 13, Blade, Carbon, CSS `@media print`

---

## Files

| Action | Path | Purpose |
|--------|------|---------|
| Create | `tests/Feature/WeekMenuPrintTest.php` | Feature tests for the print route |
| Modify | `lang/nl/weekmenu.php` | Add `print_link` translation key |
| Modify | `lang/fr/weekmenu.php` | Add `print_link` translation key |
| Modify | `routes/web.php` | Add print routes to NL and FR groups |
| Modify | `app/Http/Controllers/PageController.php` | Add `weekmenuPrint()` method |
| Create | `resources/views/pages/weekmenu-print.blade.php` | Standalone print view |
| Modify | `resources/views/livewire/week-menu.blade.php` | Add print link below allergen note |

---

## Task 1: Write failing tests

**Files:**
- Create: `tests/Feature/WeekMenuPrintTest.php`

- [ ] **Step 1: Create the test file**

```bash
php artisan make:test --phpunit WeekMenuPrintTest
```

- [ ] **Step 2: Replace the generated file with the full test suite**

Replace the contents of `tests/Feature/WeekMenuPrintTest.php` with:

```php
<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;

class WeekMenuPrintTest extends TestCase
{
    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_print_route_loads_in_nl(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
    }

    public function test_print_route_loads_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
    }

    public function test_print_view_shows_nl_content(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Stoofvlees met Sla en Kroketjes');
        $response->assertSee('Soep van de dag');
    }

    public function test_print_view_shows_fr_content(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Carbonnades, Frites et Salade');
        $response->assertSee('Potage du jour');
    }

    public function test_print_view_shows_closed_day(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Gesloten'); // Saturday March 28 is closed
    }

    public function test_print_view_shows_closed_day_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Fermé'); // Saturday March 28 is closed
    }

    public function test_print_view_responds_for_next_week(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=1');

        $response->assertStatus(200);
        $response->assertSee('Kalf blanket met Bulgur'); // Monday March 30
    }

    public function test_print_view_does_not_contain_nav(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertDontSee('<nav', false);
    }
}
```

- [ ] **Step 3: Run tests to confirm they all fail**

```bash
php artisan test --compact tests/Feature/WeekMenuPrintTest.php
```

Expected: all tests FAIL (route not found / 404).

---

## Task 2: Add translation keys

**Files:**
- Modify: `lang/nl/weekmenu.php`
- Modify: `lang/fr/weekmenu.php`

- [ ] **Step 1: Add `print_link` to NL translations**

In `lang/nl/weekmenu.php`, add after the `'allergen_note'` line:

```php
    'print_link' => 'Afdrukken / PDF',
```

- [ ] **Step 2: Add `print_link` to FR translations**

In `lang/fr/weekmenu.php`, add after the `'allergen_note'` line:

```php
    'print_link' => 'Imprimer / PDF',
```

---

## Task 3: Add print routes

**Files:**
- Modify: `routes/web.php`

- [ ] **Step 1: Add the print route to the NL group**

In `routes/web.php`, in the NL `Route::middleware(['locale:nl', ...])` group, add after the existing `nl.weekmenu` route:

```php
Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('nl.weekmenu.print');
```

- [ ] **Step 2: Add the print route to the FR group**

In `routes/web.php`, in the FR `Route::prefix('fr')->middleware('locale:fr')` group, add after the existing `fr.weekmenu` route:

```php
Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('fr.weekmenu.print');
```

- [ ] **Step 3: Verify routes are registered**

```bash
php artisan route:list --name=weekmenu
```

Expected output includes:
```
GET  restaurant-menu/print     nl.weekmenu.print
GET  fr/restaurant-menu/print  fr.weekmenu.print
```

---

## Task 4: Add the controller method

**Files:**
- Modify: `app/Http/Controllers/PageController.php`

- [ ] **Step 1: Add the `weekmenuPrint` method**

Add the following imports at the top of `PageController.php` (after the existing `use` statements):

```php
use Carbon\Carbon;
use Illuminate\Http\Request;
```

Then add this method to the `PageController` class:

```php
public function weekmenuPrint(Request $request): \Illuminate\Contracts\View\View
{
    $weekOffset = (int) $request->query('week', 0);
    $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);
    $locale = app()->getLocale();

    $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
    $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

    $days = array_values(array_filter(
        $data['days'],
        fn ($day) => $day['date'] >= $weekStart->toDateString()
            && $day['date'] <= $weekEnd->toDateString()
    ));

    if (! empty($days)) {
        $first = Carbon::parse($days[0]['date'])->locale($locale);
        $last = Carbon::parse(end($days)['date'])->locale($locale);
        $weekLabel = $first->month === $last->month
            ? $first->isoFormat('D').' – '.$last->isoFormat('D MMMM YYYY')
            : $first->isoFormat('D MMMM').' – '.$last->isoFormat('D MMMM YYYY');
    } else {
        $weekLabel = $weekStart->locale($locale)->isoFormat('D MMM')
            .' – '
            .$weekEnd->locale($locale)->isoFormat('D MMM YYYY');
    }

    return view('pages.weekmenu-print', compact('days', 'weekLabel', 'locale'));
}
```

- [ ] **Step 2: Run Pint to format the file**

```bash
vendor/bin/pint --dirty --format agent app/Http/Controllers/PageController.php
```

- [ ] **Step 3: Run tests — routes and controller should now resolve**

```bash
php artisan test --compact tests/Feature/WeekMenuPrintTest.php
```

Expected: tests now fail with a view error (view not found), not a 404.

---

## Task 5: Create the print view

**Files:**
- Create: `resources/views/pages/weekmenu-print.blade.php`

- [ ] **Step 1: Create the view**

Create `resources/views/pages/weekmenu-print.blade.php` with the following content:

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('weekmenu.menu_label') }} — {{ $weekLabel }}</title>
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: var(--color-brand-bg);
            font-family: var(--font-body);
            color: var(--color-brand-dark);
        }

        /* Fixed top bar — hidden when printing */
        .print-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: var(--color-brand-bg);
            border-bottom: 3px solid var(--color-brand-orange);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .print-bar-label {
            font-family: var(--font-sans);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-brand-muted);
            letter-spacing: 0.03em;
        }

        .print-bar-button {
            background: var(--color-brand-orange);
            color: white;
            border: none;
            padding: 0.5rem 1.25rem;
            font-family: var(--font-sans);
            font-size: 0.875rem;
            font-weight: 800;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
            letter-spacing: 0.01em;
        }

        .print-bar-button:hover {
            opacity: 0.9;
        }

        /* A4 content area */
        .print-content {
            max-width: 640px;
            margin: 0 auto;
            padding: 6rem 3rem 3rem; /* top padding clears fixed bar */
            background: white;
            min-height: 100vh;
        }

        /* Document header */
        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--color-brand-orange);
        }

        .doc-eyebrow {
            font-family: var(--font-sans);
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--color-brand-orange);
            margin: 0 0 0.25rem;
        }

        .doc-heading {
            font-family: var(--font-sans);
            font-size: 1.75rem;
            font-weight: 900;
            color: var(--color-brand-dark);
            margin: 0;
            line-height: 1.1;
        }

        .doc-logo {
            width: 72px;
            flex-shrink: 0;
        }

        /* Day rows */
        .days-list {
            display: flex;
            flex-direction: column;
            gap: 0.875rem;
        }

        .day-row {
            display: flex;
            gap: 0;
            align-items: flex-start;
        }

        .day-row.closed {
            opacity: 0.45;
        }

        .day-date {
            width: 52px;
            flex-shrink: 0;
            text-align: right;
            padding-right: 0.875rem;
            margin-right: 0.875rem;
            border-right: 2px solid #e8e0d8;
        }

        .day-date-num {
            font-family: var(--font-sans);
            font-size: 1.5rem;
            font-weight: 900;
            line-height: 1;
            display: block;
            color: var(--color-brand-dark);
        }

        .day-date-month {
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            display: block;
            color: var(--color-brand-muted);
        }

        .day-body {
            flex: 1;
            min-width: 0;
        }

        .day-label {
            font-family: var(--font-sans);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--color-brand-muted);
            margin: 0 0 0.05rem;
        }

        .day-soup {
            font-size: 0.875rem;
            color: var(--color-brand-muted);
            margin: 0.1rem 0;
        }

        .day-main-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 1rem;
        }

        .day-main {
            font-family: var(--font-body);
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--color-brand-dark);
            margin: 0;
            line-height: 1.3;
        }

        .day-price {
            font-family: var(--font-sans);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--color-brand-muted);
            flex-shrink: 0;
            font-variant-numeric: tabular-nums;
            margin: 0;
        }

        .day-closed-label {
            font-family: var(--font-body);
            font-size: 1rem;
            font-style: italic;
            color: var(--color-brand-muted);
            margin: 0;
        }

        /* Special event */
        .day-row.special {
            margin-left: -1px;
            padding-left: 1rem;
            border-left: 3px solid var(--color-brand-orange);
        }

        .day-row.special .day-date {
            border-right-color: var(--color-brand-orange);
        }

        .day-special-badge {
            display: inline-block;
            background: var(--color-brand-orange);
            color: white;
            font-family: var(--font-sans);
            font-size: 0.6rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            padding: 1px 7px;
            border-radius: 999px;
            margin-bottom: 0.2rem;
        }

        .day-event-label {
            font-family: var(--font-body);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--color-brand-dark);
            margin: 0;
            line-height: 1.3;
        }

        .day-courses {
            list-style: none;
            padding: 0;
            margin: 0.4rem 0 0;
            border-top: 1px solid #e8e0d8;
            padding-top: 0.3rem;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .day-courses li {
            font-size: 1rem;
            color: var(--color-brand-dark);
            padding-left: 0.75rem;
            position: relative;
        }

        .day-courses li::before {
            content: '·';
            position: absolute;
            left: 0;
            color: var(--color-brand-orange);
            font-weight: 700;
        }

        /* Allergen note */
        .allergen-note {
            margin-top: 1.5rem;
            padding-top: 0.875rem;
            border-top: 1px solid #e8e0d8;
        }

        .allergen-note p {
            font-size: 0.875rem;
            color: var(--color-brand-muted);
            margin: 0;
            line-height: 1.5;
        }

        /* Print styles */
        @media print {
            .print-bar { display: none !important; }

            body { background: white; }

            .print-content {
                padding-top: 2rem;
                min-height: unset;
                box-shadow: none;
            }

            @page {
                size: A4;
                margin: 1.5cm;
            }
        }

        @media (max-width: 640px) {
            .print-content { padding: 5rem 1.25rem 2rem; }
        }
    </style>
</head>
<body>

{{-- Fixed top bar --}}
<div class="print-bar">
    <span class="print-bar-label">{{ __('weekmenu.menu_label') }} — {{ $weekLabel }}</span>
    <button class="print-bar-button" onclick="window.print()">
        🖨 {{ __('weekmenu.print_link') }}
    </button>
</div>

{{-- A4 content --}}
<div class="print-content">

    {{-- Document header --}}
    <div class="doc-header">
        <div>
            <p class="doc-eyebrow">{{ __('weekmenu.menu_label') }}</p>
            <h1 class="doc-heading">{{ $weekLabel }}</h1>
        </div>
        <img src="{{ asset('images/logo.png') }}" alt="De Harmonie" class="doc-logo">
    </div>

    {{-- Day rows --}}
    <div class="days-list">
        @php $carbon_locale = $locale; @endphp

        @forelse ($days as $day)
            @php
                $carbon = \Carbon\Carbon::parse($day['date'])->locale($carbon_locale);
                $dateNum = $carbon->day;
                $monthAbbr = rtrim($carbon->isoFormat('MMM'), '.');
                $weekdayLabel = $carbon->isoFormat('dddd');
            @endphp

            @if ($day['closed'])
                <div class="day-row closed">
                    <div class="day-date">
                        <span class="day-date-num">{{ $dateNum }}</span>
                        <span class="day-date-month">{{ $monthAbbr }}</span>
                    </div>
                    <div class="day-body">
                        <p class="day-label">{{ $weekdayLabel }}</p>
                        <p class="day-closed-label">{{ $day['closed_label_' . $locale] ?? __('weekmenu.closed') }}</p>
                    </div>
                </div>

            @elseif ($day['special_event'])
                <div class="day-row special">
                    <div class="day-date">
                        <span class="day-date-num">{{ $dateNum }}</span>
                        <span class="day-date-month">{{ $monthAbbr }}</span>
                    </div>
                    <div class="day-body">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                            <div>
                                <span class="day-special-badge">{{ __('weekmenu.special_badge') }}</span>
                                <p class="day-event-label">{{ $day[$locale]['event_label'] }}</p>
                            </div>
                            <p class="day-price">€ {{ $day['price'] }}</p>
                        </div>
                        <ul class="day-courses">
                            @foreach ($day[$locale]['courses'] as $course)
                                <li>{{ $course }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            @else
                <div class="day-row">
                    <div class="day-date">
                        <span class="day-date-num">{{ $dateNum }}</span>
                        <span class="day-date-month">{{ $monthAbbr }}</span>
                    </div>
                    <div class="day-body">
                        <p class="day-label">{{ $weekdayLabel }}</p>
                        @if ($day[$locale]['soup'])
                            <p class="day-soup">{{ $day[$locale]['soup'] }}</p>
                        @endif
                        <div class="day-main-row">
                            <p class="day-main">{{ $day[$locale]['main'] }}</p>
                            <p class="day-price">€&thinsp;{{ $day['price'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

        @empty
            <p style="color: var(--color-brand-muted); font-size: 0.9rem;">{{ __('weekmenu.no_days') }}</p>
        @endforelse
    </div>

    {{-- Allergen note --}}
    <div class="allergen-note">
        <p>{{ __('weekmenu.allergen_note') }}</p>
    </div>

</div>

</body>
</html>
```

- [ ] **Step 2: Run all print tests — they should now pass**

```bash
php artisan test --compact tests/Feature/WeekMenuPrintTest.php
```

Expected: all 8 tests PASS.

- [ ] **Step 3: If the Vite manifest error appears, build assets**

```bash
npm run build
```

Then re-run the tests.

- [ ] **Step 4: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/WeekMenuPrintTest.php lang/nl/weekmenu.php lang/fr/weekmenu.php routes/web.php app/Http/Controllers/PageController.php resources/views/pages/weekmenu-print.blade.php
git commit -m "feat: add print/PDF view for week menu"
```

---

## Task 6: Add the print link to the Livewire component

**Files:**
- Modify: `resources/views/livewire/week-menu.blade.php`

- [ ] **Step 1: Add the print link below the allergen note**

In `resources/views/livewire/week-menu.blade.php`, find the allergen note `<div>` at the bottom of the menu body:

```html
    <div style="border-top: 1px solid #e8e0d8; padding-top: 0.875rem; margin-top: 1.5rem;">
        <p style="font-size: 1rem; color: var(--color-brand-muted); margin: 0;">{{ __('weekmenu.allergen_note') }}</p>
    </div>
```

Replace it with:

```html
    <div style="border-top: 1px solid #e8e0d8; padding-top: 0.875rem; margin-top: 1.5rem;">
        <p style="font-size: 1rem; color: var(--color-brand-muted); margin: 0;">{{ __('weekmenu.allergen_note') }}</p>
        <div style="margin-top: 0.75rem; text-align: right;">
            <a href="{{ route(app()->getLocale() . '.weekmenu.print', ['week' => $this->weekOffset]) }}"
               target="_blank"
               style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-muted); text-decoration: underline; text-underline-offset: 3px; text-decoration-thickness: 1px;">
                🖨 {{ __('weekmenu.print_link') }}
            </a>
        </div>
    </div>
```

- [ ] **Step 2: Run the existing weekmenu tests to confirm nothing broke**

```bash
php artisan test --compact tests/Feature/WeekMenuTest.php tests/Feature/WeekMenuPrintTest.php
```

Expected: all tests PASS.

- [ ] **Step 3: Commit**

```bash
git add resources/views/livewire/week-menu.blade.php
git commit -m "feat: add print link to week menu card"
```

---

## Task 7: Manual smoke test

- [ ] **Step 1: Open the week menu page in the browser**

Visit `https://harmonie.test/restaurant-menu` and confirm the print link appears below the allergen note.

- [ ] **Step 2: Click the print link**

Confirm it opens a new tab at `/restaurant-menu/print?week=0` (or whatever the current week offset is).

- [ ] **Step 3: Verify the fixed bar**

The on-brand top bar with "🖨 Afdrukken / PDF" button should be visible at the top.

- [ ] **Step 4: Test the print dialog**

Click the print button. Confirm the top bar disappears in the print preview and the A4 content is clean.

- [ ] **Step 5: Test the FR version**

Visit `https://harmonie.test/fr/restaurant-menu` and repeat — link should say "Imprimer / PDF" and open `/fr/restaurant-menu/print?week=0`.

- [ ] **Step 6: Navigate to next week, check the link updates**

On the week menu page, navigate to the next week. The print link's `?week=` param should update to `?week=1`.
