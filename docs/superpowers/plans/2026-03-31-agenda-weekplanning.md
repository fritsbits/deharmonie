# Agenda Weekplanning Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the month-based `ActivityOverzicht` Livewire component with a week-based view that groups activities by day and is printable.

**Architecture:** Modify `ActivityOverzicht` in place (week navigation replaces month navigation); redesign its Blade view to render per-day sections with compact rows; add `@media print` to `app.css`.

**Tech Stack:** Laravel 13, Livewire 3, Blade, Tailwind v4 (inline styles), PHPUnit 12, Carbon

---

## Files

| File | Action |
|---|---|
| `lang/nl/activities.php` | Add `previous_week`, `next_week`, `no_activities_this_day` |
| `lang/fr/activities.php` | Add `previous_week`, `next_week`, `no_activities_this_day` |
| `tests/Feature/ActivityOverzichtTest.php` | Rewrite all tests for week-based logic |
| `app/Livewire/ActivityOverzicht.php` | Replace month logic with week logic |
| `resources/views/livewire/activity-overzicht.blade.php` | Redesign: day sections + print header |
| `resources/css/app.css` | Add `@media print` rules |

---

## Task 1: Add translation keys

**Files:**
- Modify: `lang/nl/activities.php`
- Modify: `lang/fr/activities.php`

- [ ] **Step 1: Add keys to NL lang file**

In `lang/nl/activities.php`, add after the `'next_month'` line:

```php
'previous_week' => 'Vorige week',
'next_week' => 'Volgende week',
'no_activities_this_day' => 'Geen activiteiten deze dag.',
```

- [ ] **Step 2: Add keys to FR lang file**

In `lang/fr/activities.php`, add after the `'next_month'` line:

```php
'previous_week' => 'Semaine précédente',
'next_week' => 'Semaine suivante',
'no_activities_this_day' => 'Pas d\'activités ce jour.',
```

- [ ] **Step 3: Commit**

```bash
git add lang/nl/activities.php lang/fr/activities.php
git commit -m "feat: add week navigation translation keys"
```

---

## Task 2: Rewrite ActivityOverzichtTest.php

**Files:**
- Modify: `tests/Feature/ActivityOverzichtTest.php`

- [ ] **Step 1: Replace the test file with week-based tests**

Replace the entire contents of `tests/Feature/ActivityOverzichtTest.php` with:

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

    public function test_shows_activities_in_current_week(): void
    {
        $thisWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);
        $nextWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($thisWeek->titel_nl)
            ->assertDontSee($nextWeek->titel_nl);
    }

    public function test_does_not_show_previous_week_activities(): void
    {
        $lastWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->subDay()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertDontSee($lastWeek->titel_nl);
    }

    public function test_shows_full_week_including_past_days(): void
    {
        $monday = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($monday->titel_nl);
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
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $component = Livewire::test(ActivityOverzicht::class)->call('nextWeek');

        $this->assertTrue($component->get('hasPrev'));
    }

    public function test_has_next_false_when_no_future_activities_exist(): void
    {
        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertFalse($component->get('hasNext'));
    }

    public function test_has_next_true_when_future_week_has_activities(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertTrue($component->get('hasNext'));
    }

    public function test_next_week_increments_offset(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('weekOffset', 0)
            ->call('nextWeek')
            ->assertSet('weekOffset', 1);
    }

    public function test_prev_week_decrements_offset(): void
    {
        Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->call('nextWeek')
            ->assertSet('weekOffset', 1)
            ->call('prevWeek')
            ->assertSet('weekOffset', 0);
    }

    public function test_prev_week_does_nothing_at_zero(): void
    {
        Livewire::test(ActivityOverzicht::class)
            ->assertSet('weekOffset', 0)
            ->call('prevWeek')
            ->assertSet('weekOffset', 0);
    }

    public function test_week_heading_is_localised_for_nl(): void
    {
        app()->setLocale('nl');
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $expected = $start->month === $end->month
            ? "{$start->day}–{$end->day} " . $start->locale('nl')->isoFormat('MMMM YYYY')
            : $start->locale('nl')->isoFormat('D MMMM') . ' – ' . $end->locale('nl')->isoFormat('D MMMM YYYY');

        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertSame($expected, $component->get('weekHeading'));
    }

    public function test_week_heading_is_localised_for_fr(): void
    {
        app()->setLocale('fr');
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        $expected = $start->month === $end->month
            ? "{$start->day}–{$end->day} " . $start->locale('fr')->isoFormat('MMMM YYYY')
            : $start->locale('fr')->isoFormat('D MMMM') . ' – ' . $end->locale('fr')->isoFormat('D MMMM YYYY');

        $component = Livewire::test(ActivityOverzicht::class);

        $this->assertSame($expected, $component->get('weekHeading'));
    }

    public function test_cancelled_activities_appear_in_list(): void
    {
        $cancelled = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSee($cancelled->titel_nl);
    }

    public function test_mount_jumps_to_first_week_with_activities(): void
    {
        $nextWeek = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addWeek()->format('Y-m-d'),
        ]);

        Livewire::test(ActivityOverzicht::class)
            ->assertSet('weekOffset', 1)
            ->assertSee($nextWeek->titel_nl);
    }

    public function test_activities_grouped_by_date(): void
    {
        $monday = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
            'titel_nl' => 'Maandag activiteit',
        ]);
        $tuesday = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->addDay()->format('Y-m-d'),
            'titel_nl' => 'Dinsdag activiteit',
        ]);

        $component = Livewire::test(ActivityOverzicht::class);
        $grouped = $component->get('activiteiten');

        $this->assertCount(2, $grouped);
        $this->assertTrue($grouped->has($monday->datum->toDateString()));
        $this->assertTrue($grouped->has($tuesday->datum->toDateString()));
    }

    public function test_activities_ordered_by_date_then_time(): void
    {
        $later = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
            'startuur' => '15:00:00',
            'titel_nl' => 'Kaartspelen',
        ]);
        $earlier = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->startOfWeek()->format('Y-m-d'),
            'startuur' => '09:00:00',
            'titel_nl' => 'Aquafit',
        ]);

        $component = Livewire::test(ActivityOverzicht::class);
        $grouped = $component->get('activiteiten');
        $dayActivities = $grouped->first();

        $this->assertSame($earlier->id, $dayActivities->first()->id);
        $this->assertSame($later->id, $dayActivities->last()->id);
    }
}
```

- [ ] **Step 2: Run tests to confirm they fail**

```bash
php artisan test --compact tests/Feature/ActivityOverzichtTest.php
```

Expected: multiple failures — `weekOffset` property not found, `nextWeek`/`prevWeek` methods not found, `weekHeading` computed not found.

---

## Task 3: Update ActivityOverzicht.php

**Files:**
- Modify: `app/Livewire/ActivityOverzicht.php`

- [ ] **Step 1: Replace the component with week-based logic**

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
    public int $weekOffset = 0;

    public function mount(): void
    {
        $first = Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>=', now()->startOfDay())
            ->orderBy('datum')
            ->first();

        if ($first) {
            $this->weekOffset = (int) now()->startOfWeek()->diffInWeeks(
                $first->datum->copy()->startOfWeek()
            );
        }
    }

    #[Computed]
    public function activeWeekStart(): Carbon
    {
        return Carbon::now()->startOfWeek()->addWeeks($this->weekOffset);
    }

    #[Computed]
    public function activeWeekEnd(): Carbon
    {
        return $this->activeWeekStart->copy()->endOfWeek();
    }

    #[Computed]
    public function activiteiten(): Collection
    {
        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->whereBetween('datum', [$this->activeWeekStart, $this->activeWeekEnd])
            ->orderBy('datum')
            ->orderBy('startuur')
            ->get()
            ->groupBy(fn(Activiteit $a) => $a->datum->toDateString());
    }

    #[Computed]
    public function weekHeading(): string
    {
        $start = $this->activeWeekStart;
        $end = $this->activeWeekEnd;
        $locale = app()->getLocale();

        if ($start->month === $end->month) {
            return "{$start->day}–{$end->day} " . $start->locale($locale)->isoFormat('MMMM YYYY');
        }

        return $start->locale($locale)->isoFormat('D MMMM') . ' – ' . $end->locale($locale)->isoFormat('D MMMM YYYY');
    }

    #[Computed]
    public function hasPrev(): bool
    {
        return $this->weekOffset > 0;
    }

    #[Computed]
    public function hasNext(): bool
    {
        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->where('datum', '>', $this->activeWeekEnd)
            ->exists();
    }

    public function prevWeek(): void
    {
        if ($this->hasPrev) {
            $this->weekOffset--;
        }
    }

    public function nextWeek(): void
    {
        if ($this->hasNext) {
            $this->weekOffset++;
        }
    }

    public function render(): View
    {
        return view('livewire.activity-overzicht');
    }
}
```

- [ ] **Step 2: Run tests to confirm they pass**

```bash
php artisan test --compact tests/Feature/ActivityOverzichtTest.php
```

Expected: all tests pass (13 tests, 0 failures).

- [ ] **Step 3: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 4: Commit**

```bash
git add app/Livewire/ActivityOverzicht.php tests/Feature/ActivityOverzichtTest.php
git commit -m "feat: replace month navigation with week navigation in ActivityOverzicht"
```

---

## Task 4: Redesign activity-overzicht.blade.php

**Files:**
- Modify: `resources/views/livewire/activity-overzicht.blade.php`

- [ ] **Step 1: Replace the view with the week-based day-section layout**

Replace the entire contents of `resources/views/livewire/activity-overzicht.blade.php` with:

```blade
<div>
    @php $locale = app()->getLocale(); @endphp

    {{-- Print header (hidden on screen, visible on print) --}}
    <div class="agenda-print-header" style="display: none; margin-bottom: 1.5rem; font-family: var(--font-sans);">
        <strong style="font-size: 1.125rem; font-weight: 900; color: var(--color-brand-dark);">
            De Harmonie — Weekplanning {{ $this->weekHeading }}
        </strong><br>
        <span style="font-size: 0.875rem; color: var(--color-brand-muted);">Antwerpsesteenweg 24 · 02 203 28 48</span>
    </div>

    {{-- Week navigation bar --}}
    <div class="agenda-week-nav" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; background: #d4e8df; border-bottom: 1px solid #bcd6ca;">
        @if ($this->hasPrev)
            <button wire:click="prevWeek"
                    aria-label="{{ __('activities.previous_week') }}"
                    style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-green); background: none; border: none; cursor: pointer; padding: 0;">
                ← {{ __('activities.previous_week') }}
            </button>
        @else
            <span></span>
        @endif

        <span style="font-family: var(--font-sans); font-size: 1.125rem; font-weight: 900; color: var(--color-brand-dark);">
            {{ $this->weekHeading }}
        </span>

        <div style="display: flex; align-items: center; gap: 1.25rem;">
            @if ($this->hasNext)
                <button wire:click="nextWeek"
                        aria-label="{{ __('activities.next_week') }}"
                        style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: var(--color-brand-green); background: none; border: none; cursor: pointer; padding: 0;">
                    {{ __('activities.next_week') }} →
                </button>
            @else
                <span></span>
            @endif

            <button class="agenda-print-btn"
                    onclick="window.print()"
                    aria-label="{{ __('activities.print') }}"
                    style="font-family: var(--font-sans); font-size: 0.8125rem; font-weight: 700; color: var(--color-brand-muted); background: none; border: 1px solid var(--color-brand-gray-dark); border-radius: 4px; cursor: pointer; padding: 0.25rem 0.625rem;">
                🖨 {{ __('activities.print') }}
            </button>
        </div>
    </div>

    {{-- Day sections --}}
    <div style="padding: 0 1.5rem 2rem;">
        @for ($i = 0; $i < 7; $i++)
            @php
                $day = $this->activeWeekStart->copy()->addDays($i);
                $dateKey = $day->toDateString();
                $dayActivities = $this->activiteiten->get($dateKey, collect());
                $isSatOrSun = in_array($day->dayOfWeek, [\Carbon\Carbon::SATURDAY, \Carbon\Carbon::SUNDAY]);
                $isToday = $day->isToday();
                $isPast = $day->isPast() && ! $isToday;

                $headingColor = $isToday
                    ? 'var(--color-brand-green)'
                    : ($isPast ? 'var(--color-brand-muted)' : 'var(--color-brand-dark)');
            @endphp

            @if ($isSatOrSun && $dayActivities->isEmpty())
                @continue
            @endif

            {{-- Day heading --}}
            <div style="margin-top: 1.75rem;">
                <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 900; letter-spacing: 0.08em; text-transform: uppercase; color: {{ $headingColor }};">
                        {{ mb_strtoupper($day->locale($locale)->isoFormat('dddd')) }}
                    </span>
                    <span style="font-family: var(--font-sans); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: var(--color-brand-muted);">
                        {{ mb_strtoupper($day->locale($locale)->isoFormat('D MMMM')) }}
                    </span>
                    @if ($isToday)
                        <span style="font-family: var(--font-sans); font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; background: var(--color-brand-green); color: white; padding: 0.1rem 0.4rem; border-radius: 3px;">
                            {{ __('activities.date_today') }}
                        </span>
                    @endif
                </div>
                <div style="height: 1px; background: #bcd6ca;"></div>
            </div>

            {{-- Activities for this day --}}
            @if ($dayActivities->isEmpty())
                <p style="padding: 0.5rem 0 0; font-size: 0.875rem; color: var(--color-brand-muted); font-family: var(--font-body);">
                    {{ __('activities.no_activities_this_day') }}
                </p>
            @else
                @foreach ($dayActivities as $activiteit)
                    @php
                        $cancelled = $activiteit->status->value === 'geannuleerd';
                        $timeColor = $cancelled ? '#b8b0ac' : 'var(--color-brand-muted)';
                        $titleColor = $cancelled ? '#9e9690' : 'var(--color-brand-dark)';
                        $metaColor = $cancelled ? '#c8c0bc' : 'var(--color-brand-muted)';
                    @endphp

                    @if (! $loop->first)
                        <div style="height: 1px; background: rgba(160,195,180,0.3); margin-left: 88px;"></div>
                    @endif

                    <div style="display: flex; align-items: baseline; gap: 0; padding: 0.55rem 0.25rem;">
                        {{-- Time column --}}
                        <div style="width: 88px; flex-shrink: 0; text-align: right; padding-right: 1rem;">
                            <span style="font-family: var(--font-sans); font-size: 0.875rem; font-weight: 700; color: {{ $timeColor }}; white-space: nowrap;">
                                {{ substr($activiteit->startuur, 0, 5) }}@if($activiteit->einduur)–{{ substr($activiteit->einduur, 0, 5) }}@endif
                            </span>
                        </div>

                        {{-- Title + meta --}}
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                <span style="font-family: var(--font-sans); font-size: 1.0625rem; font-weight: 700; color: {{ $titleColor }}; line-height: 1.3;">
                                    {{ $activiteit->titel }}
                                </span>
                                @if ($cancelled)
                                    <x-badge type="geannuleerd" />
                                @endif
                            </div>
                            <p style="font-size: 0.8125rem; color: {{ $metaColor }}; margin: 0.1rem 0 0; font-family: var(--font-body);">
                                {{ $activiteit->locatie }}@if($activiteit->prijs !== null) · {{ $activiteit->getPrijsLabel($locale) }}@endif
                            </p>
                        </div>
                    </div>
                @endforeach
            @endif
        @endfor
    </div>
</div>
```

- [ ] **Step 2: Verify visually in the browser**

Open `https://deharmonie.test/activiteiten/agenda` and check:
- Week heading shows correct date range
- Previous/next week navigation works
- Activities grouped by day with correct time/title/location/price
- Cancelled activity shows the badge
- Lege weekdagen (ma–vr) show "Geen activiteiten deze dag."
- Print button is visible

- [ ] **Step 3: Commit**

```bash
git add resources/views/livewire/activity-overzicht.blade.php
git commit -m "feat: redesign agenda view with week-based day sections"
```

---

## Task 5: Add print CSS to app.css

**Files:**
- Modify: `resources/css/app.css`

- [ ] **Step 1: Append print media query to app.css**

Add the following at the end of `resources/css/app.css`:

```css
/* =====================
   Print styles — agenda
   ===================== */
@media print {
    nav,
    footer,
    .agenda-week-nav,
    .agenda-print-btn {
        display: none !important;
    }

    .agenda-print-header {
        display: block !important;
    }

    body {
        background: white !important;
    }
}
```

- [ ] **Step 2: Build assets**

```bash
npm run build
```

Expected: build completes without errors.

- [ ] **Step 3: Verify print layout**

Open `https://deharmonie.test/activiteiten/agenda`, open browser print preview (Cmd+P), and verify:
- Nav and footer are hidden
- Week navigation bar is hidden
- Print button is hidden
- Print header "De Harmonie — Weekplanning [dates]" is visible at the top
- Day sections and activity rows are clean and readable

- [ ] **Step 4: Run the full test file to confirm nothing regressed**

```bash
php artisan test --compact tests/Feature/ActivityOverzichtTest.php
```

Expected: all 13 tests pass.

- [ ] **Step 5: Commit**

```bash
git add resources/css/app.css
git commit -m "feat: add print styles for agenda weekplanning page"
```
