# Activity Overview Redesign — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the static activity list on `/activiteiten` with a Livewire component that shows one month at a time, using the weekmenu-style date column (large day number + month abbreviation + vertical divider) and a larger activity title.

**Architecture:** A new `ActivityOverzicht` Livewire component handles data fetching and month navigation via a `monthOffset` integer (relative to the current calendar month). The existing `overzicht.blade.php` becomes a thin wrapper. `ActivityController::index()` no longer needs to query activities.

**Tech Stack:** Laravel 13, Livewire 3, Blade, PHPUnit 12, Carbon

---

## File Map

| Action | Path |
|--------|------|
| Create | `app/Livewire/ActivityOverzicht.php` |
| Create | `resources/views/livewire/activity-overzicht.blade.php` |
| Modify | `resources/views/activiteiten/overzicht.blade.php` |
| Modify | `app/Http/Controllers/ActivityController.php` |
| Create | `tests/Feature/ActivityOverzichtTest.php` |
| Modify | `tests/Feature/ActivityControllerTest.php` |

---

## Task 1: Scaffold the Livewire component

**Files:**
- Create: `app/Livewire/ActivityOverzicht.php`
- Create: `resources/views/livewire/activity-overzicht.blade.php`

- [ ] **Step 1: Generate the component**

```bash
php artisan make:livewire ActivityOverzicht --no-interaction
```

Expected output:
```
COMPONENT CREATED 🎉

CLASS: app/Livewire/ActivityOverzicht.php
VIEW:  resources/views/livewire/activity-overzicht.blade.php
```

- [ ] **Step 2: Commit the scaffolding**

```bash
git add app/Livewire/ActivityOverzicht.php resources/views/livewire/activity-overzicht.blade.php
git commit -m "feat: scaffold ActivityOverzicht Livewire component"
```

---

## Task 2: Write failing tests for `ActivityOverzicht`

**Files:**
- Create: `tests/Feature/ActivityOverzichtTest.php`

- [ ] **Step 1: Generate the test file**

```bash
php artisan make:test ActivityOverzichtTest --phpunit --no-interaction
```

- [ ] **Step 2: Replace the generated file with the full test suite**

Replace the contents of `tests/Feature/ActivityOverzichtTest.php` with:

```php
<?php

namespace Tests\Feature;

use App\Livewire\ActivityOverzicht;
use App\Models\Activiteit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityOverzichtTest extends TestCase
{
    use RefreshDatabase;

    public function test_shows_activities_in_current_month(): void
    {
        $thisMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $nextMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($thisMonth->titel_nl)
            ->assertDontSee($nextMonth->titel_nl);
    }

    public function test_does_not_show_past_activities(): void
    {
        $past = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->subDay()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertDontSee($past->titel_nl);
    }

    public function test_has_prev_false_at_initial_offset(): void
    {
        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertFalse($component->get('hasPrev'));
    }

    public function test_has_prev_true_after_navigating_forward(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        $component = Livewire::test(ActivityOverzicht::class)->call('nextMonth');

        $this->assertTrue($component->get('hasPrev'));
    }

    public function test_has_next_false_when_no_future_activities_exist(): void
    {
        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertFalse($component->get('hasNext'));
    }

    public function test_has_next_true_when_future_month_has_activities(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertTrue($component->get('hasNext'));
    }

    public function test_next_month_increments_offset(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('monthOffset', 0)
            ->call('nextMonth')
            ->assertSet('monthOffset', 1);
    }

    public function test_prev_month_decrements_offset(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->call('nextMonth')
            ->assertSet('monthOffset', 1)
            ->call('prevMonth')
            ->assertSet('monthOffset', 0);
    }

    public function test_prev_month_does_nothing_at_zero(): void
    {
        Livewire::test(ActivityOverzicht::class)
            ->assertSet('monthOffset', 0)
            ->call('prevMonth')
            ->assertSet('monthOffset', 0);
    }

    public function test_month_heading_is_localised_for_nl(): void
    {
        app()->setLocale('nl');
        $component = Livewire::test(ActivityOverzicht::class);
        $expected = ucfirst(Carbon::now()->startOfMonth()->locale('nl')->translatedFormat('F Y'));

        $this->assertSame($expected, $component->get('monthHeading'));
    }

    public function test_month_heading_is_localised_for_fr(): void
    {
        app()->setLocale('fr');
        $component = Livewire::test(ActivityOverzicht::class);
        $expected = ucfirst(Carbon::now()->startOfMonth()->locale('fr')->translatedFormat('F Y'));

        $this->assertSame($expected, $component->get('monthHeading'));
    }

    public function test_cancelled_activities_appear_in_list(): void
    {
        $cancelled = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($cancelled->titel_nl);
    }

    public function test_mount_jumps_to_first_month_with_activities(): void
    {
        $nextMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('monthOffset', 1)
            ->assertSee($nextMonth->titel_nl);
    }

    public function test_activities_ordered_by_date_then_time(): void
    {
        $later = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
            'startuur' => '15:00:00',
            'titel_nl' => 'Kaartspelen',
        ]);
        $earlier = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
            'startuur' => '09:00:00',
            'titel_nl' => 'Aquafit',
        ]);

        $component = Livewire::test(ActivityOverzicht::class);
        $activiteiten = $component->get('activiteiten');

        $this->assertSame($earlier->id, $activiteiten->first()->id);
        $this->assertSame($later->id, $activiteiten->last()->id);
    }
}
```

- [ ] **Step 3: Run the tests — they should all fail**

```bash
php artisan test --compact tests/Feature/ActivityOverzichtTest.php
```

Expected: all 13 tests FAIL (component has no logic yet).

- [ ] **Step 4: Commit the test file**

```bash
git add tests/Feature/ActivityOverzichtTest.php
git commit -m "test: add failing tests for ActivityOverzicht component"
```

---

## Task 3: Implement `ActivityOverzicht` component logic

**Files:**
- Modify: `app/Livewire/ActivityOverzicht.php`

- [ ] **Step 1: Replace the component class with the full implementation**

Replace the entire contents of `app/Livewire/ActivityOverzicht.php` with:

```php
<?php

namespace App\Livewire;

use App\Models\Activiteit;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ActivityOverzicht extends Component
{
    public int $monthOffset = 0;

    public function mount(): void
    {
        $first = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', now()->startOfDay())
            ->orderBy('datum')
            ->first();

        if ($first) {
            $this->monthOffset = (int) now()->startOfMonth()->diffInMonths(
                $first->datum->copy()->startOfMonth()
            );
        }
    }

    #[Computed]
    public function activeMonth(): Carbon
    {
        return Carbon::now()->startOfMonth()->addMonths($this->monthOffset);
    }

    #[Computed]
    public function activiteiten(): Collection
    {
        $query = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->whereYear('datum', $this->activeMonth->year)
            ->whereMonth('datum', $this->activeMonth->month)
            ->orderBy('datum')
            ->orderBy('startuur');

        if ($this->monthOffset === 0) {
            $query->where('datum', '>=', now()->startOfDay());
        }

        return $query->get();
    }

    #[Computed]
    public function monthHeading(): string
    {
        return ucfirst(
            $this->activeMonth->locale(app()->getLocale())->translatedFormat('F Y')
        );
    }

    #[Computed]
    public function hasPrev(): bool
    {
        return $this->monthOffset > 0;
    }

    #[Computed]
    public function hasNext(): bool
    {
        $nextMonthStart = $this->activeMonth->copy()->addMonth();

        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', $nextMonthStart)
            ->exists();
    }

    public function prevMonth(): void
    {
        if ($this->hasPrev) {
            $this->monthOffset--;
        }
    }

    public function nextMonth(): void
    {
        if ($this->hasNext) {
            $this->monthOffset++;
        }
    }

    public function render(): View
    {
        return view('livewire.activity-overzicht');
    }
}
```

- [ ] **Step 2: Run the tests — they should all pass**

```bash
php artisan test --compact tests/Feature/ActivityOverzichtTest.php
```

Expected: all 13 tests PASS.

- [ ] **Step 3: Run Pint**

```bash
vendor/bin/pint app/Livewire/ActivityOverzicht.php --format agent
```

- [ ] **Step 4: Commit**

```bash
git add app/Livewire/ActivityOverzicht.php
git commit -m "feat: implement ActivityOverzicht Livewire component with month navigation"
```

---

## Task 4: Create the Livewire view

**Files:**
- Modify: `resources/views/livewire/activity-overzicht.blade.php`

- [ ] **Step 1: Replace the generated view with the full template**

Replace the entire contents of `resources/views/livewire/activity-overzicht.blade.php` with:

```blade
<div>
    {{-- Month navigation bar --}}
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; background: #d4e8df; border-bottom: 1px solid #bcd6ca;">
        @if ($this->hasPrev)
            <button wire:click="prevMonth"
                    style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-green); background: none; border: none; cursor: pointer; padding: 0;">
                ← {{ __('activities.previous_month') }}
            </button>
        @else
            <span></span>
        @endif

        <span style="font-family: var(--font-sans); font-size: 1.125rem; font-weight: 900; color: var(--color-brand-dark);">
            {{ $this->monthHeading }}
        </span>

        @if ($this->hasNext)
            <button wire:click="nextMonth"
                    style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-green); background: none; border: none; cursor: pointer; padding: 0;">
                {{ __('activities.next_month') }} →
            </button>
        @else
            <span></span>
        @endif
    </div>

    {{-- Activity list --}}
    <div style="padding: 0.5rem 1.5rem 1.5rem;">
        @php $locale = app()->getLocale(); @endphp

        @forelse ($this->activiteiten as $activiteit)
            @php
                $cancelled  = $activiteit->status->value === 'geannuleerd';
                $dateColor  = $cancelled ? '#9e9690' : 'var(--color-brand-dark)';
                $monthColor = $cancelled ? '#b8b0ac' : 'var(--color-brand-muted)';
                $divColor   = $cancelled ? '#d8d0cc' : '#bcd6ca';
                $titleColor = $cancelled ? '#9e9690' : 'var(--color-brand-dark)';
                $metaColor  = $cancelled ? '#b8b0ac' : 'var(--color-brand-muted)';
                $monthAbbr  = rtrim($activiteit->datum->locale($locale)->isoFormat('MMM'), '.');
            @endphp

            @if (! $loop->first)
                <div style="height: 1px; background: rgba(160,195,180,0.35); margin-left: calc(52px + 0.875rem + 2px + 0.875rem);"></div>
            @endif

            <a href="{{ route($locale . '.activiteiten.show', $activiteit->slug) }}"
               class="activity-overzicht-row"
               style="display: flex; align-items: flex-start; gap: 0; padding: 0.75rem 0.5rem 0.75rem 0; text-decoration: none; border-radius: 6px; border-left: 3px solid transparent;">

                {{-- Date column --}}
                <div style="width: 52px; flex-shrink: 0; text-align: right; padding-right: 0.875rem; border-right: 2px solid {{ $divColor }}; margin-right: 0.875rem;">
                    <span style="font-family: var(--font-sans); font-size: 1.875rem; font-weight: 900; line-height: 1; color: {{ $dateColor }}; display: block;">{{ $activiteit->datum->format('j') }}</span>
                    <span style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; display: block; color: {{ $monthColor }}; margin-top: 1px;">{{ $monthAbbr }}</span>
                </div>

                {{-- Content --}}
                <div style="flex: 1; min-width: 0; padding-top: 0.2rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                        <p style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 700; color: {{ $titleColor }}; line-height: 1.25; margin: 0;">
                            {{ $activiteit->titel }}
                        </p>
                        @if ($cancelled)
                            <x-badge type="geannuleerd" />
                        @endif
                    </div>
                    <p style="font-size: 0.9rem; color: {{ $metaColor }}; margin: 0.15rem 0 0; line-height: 1.4;">
                        {{ ucfirst($activiteit->datum->locale($locale)->isoFormat('dddd')) }}
                        · {{ substr($activiteit->startuur, 0, 5) }}
                        @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
                        · {{ $activiteit->locatie }}
                    </p>
                </div>
            </a>

        @empty
            <p style="padding: 2rem 0; color: var(--color-brand-muted); font-size: 1.0625rem;">
                {{ __('activities.no_upcoming') }}
            </p>
        @endforelse
    </div>
</div>

<style>
.activity-overzicht-row:hover {
    background: rgba(255, 255, 255, 0.7) !important;
    border-left-color: var(--color-brand-green) !important;
}
</style>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/livewire/activity-overzicht.blade.php
git commit -m "feat: add activity-overzicht Livewire view with weekmenu-style date column"
```

---

## Task 5: Update `overzicht.blade.php` and `ActivityController`

**Files:**
- Modify: `resources/views/activiteiten/overzicht.blade.php`
- Modify: `app/Http/Controllers/ActivityController.php`

- [ ] **Step 1: Replace `overzicht.blade.php` with the thin wrapper**

Replace the entire contents of `resources/views/activiteiten/overzicht.blade.php` with:

```blade
@extends('layouts.app')
@section('title', 'Agenda — ' . __('activities.all'))

@section('content')

<x-page-hero
    :eyebrow="__('nav.activities')"
    eyebrow-color="green"
    :heading="__('activities.overview_heading')"
    :lead="__('activities.overview_tagline')"
    image="images/photo-visitors-1.webp"
    :image-alt="__('pages.home_photo_groep_actief_alt')"
    bg="white"
/>

<div style="background: #eef5f1;">
    <div style="max-width: 72rem; margin: 0 auto; padding: 0 1.5rem 4rem;">
        <livewire:activity-overzicht />
    </div>
</div>

@endsection
```

- [ ] **Step 2: Simplify `ActivityController::index()`**

In `app/Http/Controllers/ActivityController.php`, replace the `index()` method:

```php
public function index()
{
    return view('activiteiten.overzicht');
}
```

- [ ] **Step 3: Run Pint on the controller**

```bash
vendor/bin/pint app/Http/Controllers/ActivityController.php --format agent
```

- [ ] **Step 4: Clear the view cache and verify the page loads**

```bash
php artisan view:clear
```

Then visit `https://harmonie.test/activiteiten` in your browser to confirm the page renders without errors.

- [ ] **Step 5: Commit**

```bash
git add resources/views/activiteiten/overzicht.blade.php app/Http/Controllers/ActivityController.php
git commit -m "feat: replace static overzicht with ActivityOverzicht Livewire component"
```

---

## Task 6: Update `ActivityControllerTest` and run the full suite

**Files:**
- Modify: `tests/Feature/ActivityControllerTest.php`

The test `test_upcoming_activities_shown_including_next_month` asserts that next month's activities appear on the overzicht page. With the new one-month-at-a-time design, next month's activities are only shown after navigation — so the assertion must change.

- [ ] **Step 1: Update the test**

In `tests/Feature/ActivityControllerTest.php`, replace the method `test_upcoming_activities_shown_including_next_month` with:

```php
public function test_overview_shows_only_current_month_by_default(): void
{
    $today = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'datum' => now()->format('Y-m-d'),
    ]);
    $nextMonth = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'datum' => now()->addMonthNoOverflow()->startOfMonth()->format('Y-m-d'),
    ]);
    $past = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'datum' => now()->subDay()->format('Y-m-d'),
    ]);

    $response = $this->get('/activiteiten');
    $response->assertSee($today->titel_nl);
    $response->assertDontSee($nextMonth->titel_nl);
    $response->assertDontSee($past->titel_nl);
}
```

- [ ] **Step 2: Run the updated test file to confirm it passes**

```bash
php artisan test --compact tests/Feature/ActivityControllerTest.php
```

Expected: all tests PASS.

- [ ] **Step 3: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests PASS. If anything fails, fix before continuing.

- [ ] **Step 4: Run Pint on all modified PHP files**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/ActivityControllerTest.php
git commit -m "test: update ActivityControllerTest to reflect one-month-at-a-time overview"
```
