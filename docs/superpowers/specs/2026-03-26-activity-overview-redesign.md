# Spec: Activity Overview Redesign

**Date:** 2026-03-26
**Scope:** `resources/views/activiteiten/overzicht.blade.php` + new Livewire component

---

## Problem

The current activity overview loads all upcoming activities grouped by month as a static Blade view. The date column shows only a day number, titles are small, and there is no way to navigate between months without scrolling through a long list.

## Solution

Apply the weekmenu date-column pattern to the activity overview. Show one month at a time with Livewire-powered prev/next navigation. Each activity row gets the same visual shape as a weekmenu day row: large day number + month abbreviation + vertical divider + larger title.

---

## Visual Design

### Month navigation bar (top)
- Full-width bar above the activity list, background `#d4e8df` (the green tint used elsewhere on the overzicht page), bottom border `1px solid #bcd6ca`
- Layout: `← Vorige maand` | `April 2026` | `Volgende maand →`
- Month name: Nunito Sans, 1.125rem, weight 900, color `var(--color-brand-dark)`
- Nav buttons: Nunito Sans, 0.875rem, weight 700, color `var(--color-brand-green)`; hidden (not rendered) when there is no prev/next month with activities
- Padding: `1rem 1.5rem`

### Activity row
Each row is a `<a>` link with the following structure:

```
[ date-col ] | [ content ]
```

**Date column** (52px wide, right-aligned)
- Day number: Nunito Sans, 1.875rem, weight 900, `var(--color-brand-dark)`
- Month abbreviation: Nunito Sans, 0.65rem, weight 800, uppercase, `var(--color-brand-muted)`
- Right border: `2px solid #bcd6ca` (green divider), `0.875rem` margin to content

**Content**
- Title: Nunito Sans, 1.375rem, weight 700, `var(--color-brand-dark)`, line-height 1.25
- Meta line: Source Sans 3, 0.9rem, `var(--color-brand-muted)` — weekday · startuur–einduur · locatie
- Padding: `0.75rem 0.5rem 0.75rem 0`
- Hover: `rgba(255,255,255,0.7)` background, green left border `3px solid var(--color-brand-green)`, day number and divider turn green

**Cancelled activities**
- Full row uses grey tones: day (`#9e9690`), month abbr (`#b8b0ac`), title (`#9e9690`), meta (`#b8b0ac`), divider (`#d8d0cc`)
- Cancelled badge (grey pill) inline after the title
- Row is still a link (detail page still accessible)

**Row dividers**
- `1px` line, `rgba(160,195,180,0.35)`, indented to start at the content column (skips the date column)

**Empty state**
- When a month has no activities, show the existing translated string `activities.no_upcoming` in muted text

---

## Component: `ActivityOverzicht` (Livewire)

### File
`app/Livewire/ActivityOverzicht.php`

### Properties
```php
public int $monthOffset = 0;
```
`0` = the first month that has at least one upcoming activity (determined in `mount()`). Negative offsets are not allowed — the overview is forward-looking only.

### Mount logic
`$monthOffset` is always relative to the **current calendar month** (`Carbon::now()`). On mount, find the earliest month >= current month that has at least one activity. Set `$monthOffset` to the number of whole months between now and that month (0 if the current month has activities, 1 if next month is the first with activities, etc.).

### Computed: `activiteiten()`
Filter `Activiteit` records where `datum` falls within the active month (year + month derived from `Carbon::now()->addMonths($this->monthOffset)`). Order by `datum ASC`, then `startuur ASC`. Return all statuses (upcoming + cancelled).

### Computed: `activeMonth()`
Returns a `Carbon` instance for the first day of the currently displayed month: `Carbon::now()->startOfMonth()->addMonths($this->monthOffset)`.

### Computed: `monthHeading()`
Returns a localised string: `ucfirst($this->activeMonth->translatedFormat('F Y'))` — e.g., "April 2026" / "Avril 2026".

### Computed: `hasPrev()`
Returns `true` if `monthOffset > 0`. The current calendar month is the earliest you can navigate to — no showing past months.

### Computed: `hasNext()`
Returns `true` if there is at least one activity with `datum` in the month after the current active month, or any month beyond that. In practice: check if any activity exists with `datum >= first day of next month`.

### Actions
```php
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
```

### View
`resources/views/livewire/activity-overzicht.blade.php`

---

## Blade changes

### `overzicht.blade.php`
Remove the existing `@php` grouping logic, `@forelse` loop, and `<style>` block. Replace the content section with:

```blade
<livewire:activity-overzicht />
```

The page hero and outer container remain unchanged.

---

## Bilingual support

- Month heading uses `translatedFormat` with `app()->getLocale()` — Carbon handles NL/FR automatically
- Weekday in meta line uses `$activiteit->datum->locale(app()->getLocale())->isoFormat('dddd')`
- No new translation keys needed

---

## Testing

Update `tests/Feature/ActivityControllerTest.php`:
- Existing route assertions still pass (named routes `nl.activiteiten.overzicht`, `fr.activiteiten.overzicht`)
- Route resolves to a view that contains the Livewire component

Add `tests/Feature/ActivityOverzichtTest.php` using Livewire testing utilities (`Livewire::test(ActivityOverzicht::class)`):
- `hasPrev` is false when `monthOffset === 0`
- `hasPrev` is true when `monthOffset > 0`
- `hasNext` is false when no activities exist in any future month
- `nextMonth()` increments offset; `prevMonth()` decrements it
- `prevMonth()` does nothing when `monthOffset === 0`
- `activiteiten()` returns only activities in the active month, ordered by datum then startuur
- `monthHeading()` returns the correct localised string for NL and FR
- Cancelled activities appear in the list alongside upcoming ones

---

## Out of scope

- The homepage `activity-filter` widget (unchanged)
- The `activiteiten/show.blade.php` detail page (unchanged)
- Contextual "Vandaag / Morgen" highlighting (not part of this redesign)
- Filtering by category or status
