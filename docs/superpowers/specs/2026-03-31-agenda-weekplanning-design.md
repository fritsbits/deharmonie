# Design Spec — Agenda Weekplanning (`/activiteiten/agenda`)

_Status: Draft — 2026-03-31_

## Context

The `/activiteiten/agenda` page is a **utility page** for people already convinced about De Harmonie. Its role is practical: check what's happening this week, print it for the bulletin board, or plan a visit. It is explicitly NOT an invitation page — that job belongs to `/activiteiten`.

The current implementation uses `ActivityOverzicht` (month-based Livewire navigation). This spec replaces month navigation with week navigation and restructures the view to group activities by day.

---

## Approach

**Modify `ActivityOverzicht` in place** — the component is only used in `agenda.blade.php`, so modifying it avoids new files while keeping the Livewire pattern consistent with the rest of the codebase.

---

## Livewire Component: `ActivityOverzicht`

### State

| Property | Type | Default | Description |
|---|---|---|---|
| `weekOffset` | `int` | `0` | Weeks relative to the current week (0 = current week) |

Replace `monthOffset` with `weekOffset`.

### Computed Properties

**`activeWeekStart(): Carbon`**
Returns the Monday of the target week:
```php
Carbon::now()->startOfWeek()->addWeeks($this->weekOffset)
```

**`activeWeekEnd(): Carbon`**
Returns the Sunday of the target week:
```php
$this->activeWeekStart->copy()->endOfWeek()
```

**`activiteiten(): Collection`**
Returns all `gepubliceerd` + `geannuleerd` activities for the week, grouped by date string:
```php
Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
    ->whereBetween('datum', [$this->activeWeekStart, $this->activeWeekEnd])
    ->orderBy('datum')->orderBy('startuur')
    ->get()
    ->groupBy(fn($a) => $a->datum->toDateString())
```
No offset-0 filtering: show the full week even if some days are in the past (the week view is always the complete week).

**`weekHeading(): string`**
Format: `"6–12 april 2026"` (locale-aware month name, lowercase).
- If start and end are in the same month: `"6–12 april 2026"`
- If they span months: `"28 april – 4 mei 2026"`

**`hasPrev(): bool`**
`weekOffset > 0` — never allow navigating into past weeks.

**`hasNext(): bool`**
Check if any published/cancelled activity exists after `activeWeekEnd`.

### Methods

- `prevWeek()` — decrement `weekOffset` if `hasPrev`
- `nextWeek()` — increment `weekOffset` if `hasNext`

### `mount()`

On mount, set `weekOffset` to the first week that contains upcoming activities:
```php
$first = Activiteit::whereIn('status', [...])
    ->where('datum', '>=', now()->startOfDay())
    ->orderBy('datum')->first();

if ($first) {
    $this->weekOffset = (int) now()->startOfWeek()
        ->diffInWeeks($first->datum->copy()->startOfWeek());
}
```

---

## View: `livewire/activity-overzicht.blade.php`

### Week navigation bar

Same visual style as current month nav: green background (`#d4e8df`), border-bottom.

```
← Vorige week    6–12 april 2026    Volgende week →
```

Add a print button on the right side of the nav bar:
```
← Vorige week    6–12 april 2026    Volgende week →    [🖨 Afdrukken]
```

Print button: ghost style, small, `onclick="window.print()"`. Hidden via `@media print`.

### Day sections

Loop over Mon–Sun. For each day:

**If the day is Saturday or Sunday and has no activities → skip it** (hide to keep the view lean).
**If the day is Mon–Fri and has no activities → show with "Geen activiteiten".**

Day heading:
- `font-family: var(--font-sans)`, weight 900, ~1rem, uppercase, letter-spacing
- Color: `var(--color-brand-muted)` for past days, `var(--color-brand-dark)` for today/future
- Today: add a subtle "Vandaag" badge or color accent
- Format: `MAANDAG 6 APRIL`
- Divider line below heading

### Activity rows (no links — pure info)

Each row is a `<div>` (not `<a>`) with the same visual rhythm as the current list:

```
[tijd]   [titel + badge]              [locatie · prijs]
```

- **Tijd**: `startuur–einduur` (or just `startuur` if no end time). `font-family: var(--font-sans)`, weight 700, ~1rem, fixed width ~80px, right-aligned. Color: `var(--color-brand-muted)` for cancelled.
- **Titel**: `font-family: var(--font-sans)`, weight 700, ~1.25rem. Cancelled: muted color.
- **Badge**: `<x-badge type="geannuleerd" />` inline after title if cancelled.
- **Meta**: locatie · prijslabel. `font-size: 0.875rem`, `var(--color-brand-muted)`. Cancelled: lighter.

No hover state (no interactivity — pure info).

Separator between rows: thin line `rgba(160,195,180,0.35)`.

### Empty day state

```
Geen activiteiten deze dag.
```
`color: var(--color-brand-muted)`, `font-size: 0.9rem`, padding `0.5rem 0`.

---

## Print Styles

Add to `resources/css/app.css` under `@media print`:

```css
@media print {
    /* Hide chrome */
    nav, footer, .agenda-week-nav, .agenda-print-btn { display: none !important; }

    /* Print header */
    .agenda-print-header { display: block !important; }

    /* Clean layout */
    body { background: white; }
}
```

Add a `.agenda-print-header` div (hidden by default, shown on print) inside the Livewire component above the week nav:

```html
<div class="agenda-print-header" style="display: none; margin-bottom: 1rem;">
    <strong>De Harmonie — Weekplanning {{ $this->weekHeading }}</strong><br>
    <small>Antwerpsesteenweg 24 · 02 203 28 48</small>
</div>
```

---

## UX Docs

Save skeleton to `docs/ux/activiteiten/agenda/_skeleton.md` (new subfolder).

---

## What Changes

| File | Action |
|---|---|
| `app/Livewire/ActivityOverzicht.php` | Replace month logic with week logic |
| `resources/views/livewire/activity-overzicht.blade.php` | Redesign: day sections, time-first rows, print header, print button |
| `resources/css/app.css` | Add `@media print` rules for agenda page |
| `resources/views/activiteiten/agenda.blade.php` | No changes needed |

---

## What Does NOT Change

- `agenda.blade.php` — already correct (x-page-hero + Livewire component)
- Route structure — no changes
- `ActivityOverzicht` class name — kept as-is (used only in one place)
- Activity detail links — removed (rows are not clickable, per UX decision)
