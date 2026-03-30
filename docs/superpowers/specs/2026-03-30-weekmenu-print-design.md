# Weekmenu Print / PDF View

**Date:** 2026-03-30
**Scope:** Add a print/PDF link to the week menu page that opens a clean, A4-optimised standalone page the user can print or save as PDF from the browser.

---

## Overview

A dedicated print view route renders the week menu for the currently-viewed week as a clean standalone HTML page — no nav, no footer, no Livewire. A fixed on-brand top bar contains a print button (`window.print()`). The bar and any screen chrome are hidden via `@media print`, leaving a clean A4 document.

The link on the week menu page is generated inside the Livewire `week-menu` component so it has access to the current `$weekOffset`.

---

## Decisions

- **Single-language:** The print view respects the current locale (NL from `/restaurant-menu/print`, FR from `/fr/restaurant-menu/print`).
- **All days shown:** Unlike the web view (which hides closed days), the print view shows every day in the week. Closed days appear dimmed with their `closed_label_nl` / `closed_label_fr` text.
- **Soup included:** The print view shows both soup and main course per day (matching the original Google Docs format). The web view only shows the main.
- **Week determined by query param:** `?week={offset}` (integer, default 0). The Livewire component appends the current `$weekOffset` when generating the link.
- **No new dependencies:** Pure HTML + CSS, no DomPDF or Browsershot.

---

## Routes

Add to both locale groups in `routes/web.php`:

```php
// NL group
Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('nl.weekmenu.print');

// FR group (inside prefix('fr'))
Route::get('/restaurant-menu/print', [PageController::class, 'weekmenuPrint'])->name('fr.weekmenu.print');
```

---

## Controller — `PageController::weekmenuPrint()`

```php
public function weekmenuPrint(Request $request): View
```

1. Read `$weekOffset = (int) $request->query('week', 0)`.
2. Read `resources/data/weekmenu.json`.
3. Compute `$weekStart` / `$weekEnd` using the same Carbon logic as the Livewire component.
4. Filter `$days` to all entries (open **and** closed) whose `date` falls in that range.
5. Compute `$weekLabel` (same format as the Livewire `weekLabel` computed property: `"D – D MMMM YYYY"` or `"D MMMM – D MMMM YYYY"`).
6. Pass `$days`, `$weekLabel`, `$locale = app()->getLocale()` to the view.

The week-filtering and label logic is intentionally duplicated from the Livewire component (YAGNI — no shared service needed yet).

---

## View — `resources/views/pages/weekmenu-print.blade.php`

Standalone HTML document (no `@extends`). Loads the project fonts (Nunito Sans, Source Sans 3) and the compiled `app.css` for CSS tokens.

### Fixed top bar (screen only, hidden when printing)

```
[ Weekmenu — {weekLabel}          [🖨 Afdrukken / PDF] ]
```

- Background: `--color-brand-bg` (`#fbfaf9`)
- Bottom border: `3px solid --color-brand-orange`
- Button: orange background, white text, calls `window.print()`
- Hidden via `@media print { display: none }`

### A4 content area

Max-width ~600px, centred, white background, adequate padding to clear the fixed bar.

**Header:**
- Eyebrow: "WEEKMENU" in orange uppercase
- Heading: `{weekLabel}` in large bold dark type
- De Harmonie logo (`public/images/logo.png`) floated right, ~80px wide

**Day rows** (one per JSON entry in the week):

*Open day:*
```
[date num]  [weekday label]
[month]     [soup name — muted]
            [main course name ————————————— € price]
```
Left column: date number (large, bold) + month abbreviation, separated by a vertical line.

*Closed day:*
Same date column layout, dimmed (`opacity: 0.45`), shows `closed_label_nl` / `closed_label_fr` in italic instead of course names.

*Special event:*
Same orange-left-border treatment as the web view, bullet list of courses.

**Footer:** Allergen note in muted small text, separated by a top border.

### Print styles (`@media print`)

- Hide the fixed top bar
- Remove box shadows, transforms
- Ensure `@page { size: A4; margin: 1.5cm; }`
- Force white background, dark text (no colour inversion)
- Font sizes bump up slightly for legibility on paper

---

## Link in the Livewire component

In `resources/views/livewire/week-menu.blade.php`, add a small print link below the allergen note:

```html
<div style="margin-top: 0.75rem; text-align: right;">
    <a href="{{ route(app()->getLocale() . '.weekmenu.print', ['week' => $this->weekOffset]) }}"
       target="_blank"
       style="font-size: 0.875rem; font-weight: 700; color: var(--color-brand-muted); text-decoration: underline; text-underline-offset: 3px;">
        🖨 {{ __('weekmenu.print_link') }}
    </a>
</div>
```

Translation keys needed:
- `weekmenu.print_link` — NL: `"Afdrukken / PDF"`, FR: `"Imprimer / PDF"`

---

## Tests

`tests/Feature/WeekMenuPrintTest.php`:

- `GET /restaurant-menu/print` returns 200
- `GET /fr/restaurant-menu/print` returns 200
- `GET /restaurant-menu/print?week=1` returns 200 (next week)
- Response contains the week label string
- Response contains a day's main course text
- Closed day label (`Gesloten` / `Fermé`) appears in the response
- Response does **not** contain nav or footer markup

---

## Out of scope

- Bilingual (NL+FR side by side) PDF — deferred
- Server-side PDF file generation — deferred
- Printing from any week beyond what is in the JSON — no error handling needed; empty week renders gracefully with the "no days" message
