# Clickable agenda activity rows

## Problem

On the weekly agenda page (`/activiteiten/agenda`), each activity is rendered as a plain `<div>`. Visitors cannot click through to the activity detail page from here, and there is no visual affordance to suggest they should be able to. The homepage list (`resources/views/livewire/activity-filter.blade.php`) already wraps each row in an `<a>` — the agenda page is the one outlier.

## Goal

Make every activity in the weekly agenda clickable, taking the visitor to the activity's detail page. The affordance must be obvious enough for a senior audience (60+) while respecting the paper-like aesthetic of the agenda page and leaving the printed output unchanged.

## Scope

- **In scope:** `resources/views/activiteiten/agenda.blade.php`
- **Out of scope:** the homepage list (already correct), the activity detail page itself, the print layout behavior (must remain identical)

## Design

### Structural change

Replace the activity wrapper:

```blade
<div class="agenda-activity" style="...">
```

with a link:

```blade
<a class="agenda-activity" href="{{ route($locale . '.activiteiten.show', $activiteit->slug) }}" style="...">
```

The `<a>` wraps everything that currently lives inside: the title row (title + optional cancelled badge) and the meta line (time · location · price). Screen readers will announce the entire row as a single link whose text is the activity title plus its context — no separate `aria-label` needed.

### Base visual state

- `text-decoration: none`
- `display: block`, `position: relative`, `border-radius: 6px`
- Title color: currently `var(--color-brand-dark)` for active activities. Change the active-state color to `var(--color-brand-blue)`. Past and cancelled activities keep their existing muted colors (the `$isPast || $cancelled` branch is unchanged).
- Padding `0.625rem 2.25rem 0.625rem 0.75rem` — extra right padding reserves room for the hover arrow.
- Compensating negative horizontal margin (`margin-left: -0.75rem; margin-right: -0.75rem`) so text alignment matches today's layout while the hover fill breathes past the text baseline.
- Vertical rhythm: the existing `margin-top: 1.25rem` between stacked activities is reduced so that `margin-top + padding-top + padding-bottom` ≈ the current visual gap. Target `margin-top: 0.625rem` between items.

### Hover and focus state

- Background fades to `rgba(129, 181, 156, 0.10)` — a soft tint derived from `--color-brand-green`.
- A `→` arrow appears on the right via an `::after` pseudo-element. It fades from `opacity: 0` to `1` and slides from `translateX(-4px)` to `0`.
- Transitions: `background-color 160ms ease`, plus `opacity` and `transform` `160ms ease` on the arrow.
- Keyboard focus shows the same background plus a `2px` solid `var(--color-brand-green-dark)` outline with `2px` offset — a visibly stronger cue than mouse hover, so keyboard users know where focus is.

### Cancelled activities

- `opacity: 0.6` on the whole link (matches the de-emphasis already applied today).
- Still clickable, still shows the hover background and arrow on hover — users can still open a cancelled activity's detail page to see context.

### Print

Under `@media print`, reset everything the link adds:

- Title color forced back to `var(--color-brand-dark)`.
- No underline.
- No background fill (hover state can't trigger in print, but also reset base link styling).
- Arrow `::after` hidden.

The printed weekly agenda must look identical to today.

## Testing

One feature test in `tests/Feature/`:

- GET the agenda page for a week that contains seeded activities (seed via factory).
- Assert the response contains an `<a>` with `href` equal to the expected activity show route for each activity rendered for that week.
- Cover both a non-cancelled and a cancelled activity to confirm cancelled rows are still linked.

Visual states (hover, focus, arrow animation, print) are CSS-only and not automated — verify manually in the browser before marking the work complete.

## Non-goals

- No change to the homepage list.
- No change to the activity detail page.
- No change to the NL/FR routing logic, the locale middleware, or the route names.
- No introduction of new design tokens; all colors come from existing `--color-brand-*` variables.
