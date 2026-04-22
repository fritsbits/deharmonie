# Clickable Agenda Rows Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make every activity row on `/activiteiten/agenda` a link to the activity detail page, with a clear visual affordance that survives into the print layout unchanged.

**Architecture:** Swap the activity wrapper `<div>` for an `<a>` in `resources/views/activiteiten/agenda.blade.php`. Add a dedicated `.agenda-activity` CSS block to the existing `<style>` tag in that same file for hover, focus-visible, `::after` arrow, and print resets. Active-state title color shifts from `--color-brand-dark` to `--color-brand-blue`; past and cancelled title colors are unchanged.

**Tech Stack:** Laravel 13 Blade, Tailwind v4 CSS tokens (existing `--color-brand-*`), PHPUnit feature test.

**Spec:** `docs/superpowers/specs/2026-04-22-agenda-clickable-rows-design.md`

---

## File Structure

**Modify:**
- `resources/views/activiteiten/agenda.blade.php` — swap the activity `<div>` for an `<a>`, update the active-state `$titleColor`, and append a `.agenda-activity` CSS block (hover / focus-visible / ::after arrow / print reset) inside the existing `<style>` tag.

**Create:**
- `tests/Feature/AgendaClickableRowsTest.php` — new feature test that asserts each agenda row links to its activity detail route. Both a published and a cancelled activity are covered.

No other files change.

---

### Task 1: Write the failing feature test

**Files:**
- Create: `tests/Feature/AgendaClickableRowsTest.php`

- [ ] **Step 1: Create the test file**

Create `tests/Feature/AgendaClickableRowsTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaClickableRowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_agenda_row_links_to_activity_detail(): void
    {
        $published = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);

        $response = $this->get('/activiteiten/agenda?week=0');

        $response->assertOk();
        $response->assertSee(
            'href="' . route('nl.activiteiten.show', $published->slug) . '"',
            false
        );
    }

    public function test_cancelled_agenda_row_still_links_to_activity_detail(): void
    {
        $cancelled = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->addDay()->format('Y-m-d'),
        ]);

        $response = $this->get('/activiteiten/agenda?week=0');

        $response->assertOk();
        $response->assertSee(
            'href="' . route('nl.activiteiten.show', $cancelled->slug) . '"',
            false
        );
    }
}
```

- [ ] **Step 2: Run the test and confirm both methods fail**

Run: `php artisan test --compact --filter=AgendaClickableRowsTest`

Expected: both tests FAIL because the agenda template currently renders `<div>`, not `<a href="...">`, so the `href="..."` substrings are absent from the response.

- [ ] **Step 3: Commit the failing test**

```bash
git add tests/Feature/AgendaClickableRowsTest.php
git commit -m "test: assert agenda rows link to activity detail pages"
```

---

### Task 2: Wrap each agenda row in a link

**Files:**
- Modify: `resources/views/activiteiten/agenda.blade.php` (activity loop at lines ~90-118, `$titleColor` at line ~93)

- [ ] **Step 1: Change the active-state title color to brand blue**

In `resources/views/activiteiten/agenda.blade.php`, find:

```php
$titleColor = $isPast || $cancelled ? 'var(--color-brand-muted)' : 'var(--color-brand-dark)';
```

Replace with:

```php
$titleColor = $isPast || $cancelled ? 'var(--color-brand-muted)' : 'var(--color-brand-blue)';
```

Past and cancelled titles remain muted (unchanged).

- [ ] **Step 2: Swap the activity `<div>` for an `<a>`**

Find this block (roughly lines 110-118 in the current file):

```blade
<div class="agenda-activity" style="{{ $loop->first ? '' : 'margin-top: 1.25rem;' }}">
    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span class="agenda-activity-title" style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 700; color: {{ $titleColor }}; line-height: 1.3;">{{ $activiteit->titel }}</span>
        @if ($cancelled)
            <x-badge type="geannuleerd" />
        @endif
    </div>
    <p class="agenda-activity-meta tabular-nums" style="font-size: 1.0625rem; color: {{ $metaColor }}; margin: 0.25rem 0 0; font-family: var(--font-body);">{{ $metaStr }}</p>
</div>
```

Replace with:

```blade
<a class="agenda-activity{{ $cancelled ? ' agenda-activity--cancelled' : '' }}"
   href="{{ route($locale . '.activiteiten.show', $activiteit->slug) }}"
   style="{{ $loop->first ? '' : 'margin-top: 0.625rem;' }}">
    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
        <span class="agenda-activity-title" style="font-family: var(--font-sans); font-size: 1.375rem; font-weight: 700; color: {{ $titleColor }}; line-height: 1.3;">{{ $activiteit->titel }}</span>
        @if ($cancelled)
            <x-badge type="geannuleerd" />
        @endif
    </div>
    <p class="agenda-activity-meta tabular-nums" style="font-size: 1.0625rem; color: {{ $metaColor }}; margin: 0.25rem 0 0; font-family: var(--font-body);">{{ $metaStr }}</p>
</a>
```

Changes vs the original:
- Opening/closing tag `<div>` → `<a>`
- Added `href` using the existing `$locale` variable (declared earlier in the template at line 19: `$locale = app()->getLocale();`)
- Added `agenda-activity--cancelled` modifier class when the activity is cancelled (replaces later inline opacity styling, which Task 3 will move to CSS)
- `margin-top` inline value shrunk from `1.25rem` to `0.625rem` to preserve vertical rhythm once Task 3 adds `0.625rem` of vertical padding to the link

- [ ] **Step 3: Run the feature test and confirm it now passes**

Run: `php artisan test --compact --filter=AgendaClickableRowsTest`

Expected: both tests PASS — the rendered HTML now contains `href="..."` pointing to the activity show route for each activity.

- [ ] **Step 4: Run the full agenda test suite to confirm no regressions**

Run: `php artisan test --compact tests/Feature/ActivityOverzichtTest.php tests/Feature/ActiviteitenOverviewTest.php tests/Feature/ActivityControllerTest.php`

Expected: all tests PASS.

- [ ] **Step 5: Run Pint on the changed file**

Run: `vendor/bin/pint --dirty --format agent`

Expected: no errors. The `.blade.php` file is not formatted by Pint, but this picks up any incidental PHP changes.

- [ ] **Step 6: Commit**

```bash
git add resources/views/activiteiten/agenda.blade.php
git commit -m "feat(agenda): link each agenda row to its activity detail page"
```

---

### Task 3: Add hover, focus, arrow, and print CSS

**Files:**
- Modify: `resources/views/activiteiten/agenda.blade.php` (inside the existing `<style>` block, currently at lines ~135-208)

- [ ] **Step 1: Append the `.agenda-activity` CSS rules**

In the `<style>` block of `resources/views/activiteiten/agenda.blade.php`, add these rules **immediately before** the existing `@@media print { ... }` block (so the base rules come first, then the existing print block still has the final word on print overrides). Then add the print-specific overrides for `.agenda-activity` **inside** the existing `@@media print { ... }` block.

First, add before the `@@media print` block:

```css
.agenda-activity {
    display: block;
    position: relative;
    text-decoration: none;
    padding: 0.625rem 2.25rem 0.625rem 0.75rem;
    margin-left: -0.75rem;
    margin-right: -0.75rem;
    border-radius: 6px;
    transition: background-color 160ms ease;
}
.agenda-activity::after {
    content: '→';
    position: absolute;
    top: 50%;
    right: 0.75rem;
    transform: translateY(-50%) translateX(-4px);
    opacity: 0;
    color: var(--color-brand-blue);
    font-family: var(--font-sans);
    font-size: 1.25rem;
    font-weight: 700;
    transition: opacity 160ms ease, transform 160ms ease;
    pointer-events: none;
}
.agenda-activity:hover,
.agenda-activity:focus-visible {
    background-color: rgba(129, 181, 156, 0.10);
}
.agenda-activity:hover::after,
.agenda-activity:focus-visible::after {
    opacity: 1;
    transform: translateY(-50%) translateX(0);
}
.agenda-activity:focus-visible {
    outline: 2px solid var(--color-brand-green-dark);
    outline-offset: 2px;
}
.agenda-activity--cancelled {
    opacity: 0.6;
}
```

Then, inside the existing `@@media print { ... }` block (which currently ends around line 207), add these rules alongside the existing print overrides:

```css
    .agenda-activity {
        padding: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        background: none !important;
        border-radius: 0 !important;
        opacity: 1 !important;
    }
    .agenda-activity::after {
        display: none !important;
    }
    .agenda-activity-title {
        color: var(--color-brand-dark) !important;
    }
```

Blade note: the `@@media` / `@@page` double-at syntax is intentional and already used by the existing print block — it escapes the single `@` so Blade outputs `@media` / `@page` literally.

- [ ] **Step 2: Rebuild frontend assets**

Tailwind v4 scans Blade files at build time. The new CSS is raw, not Tailwind utilities, but run the build to be safe:

Run: `npm run build`

Expected: build completes without errors. (If `npm run dev` is already running in a separate shell, Vite hot-reloads and this step can be skipped.)

- [ ] **Step 3: Re-run the feature test**

Run: `php artisan test --compact --filter=AgendaClickableRowsTest`

Expected: both tests still PASS (CSS changes don't affect HTML output).

- [ ] **Step 4: Manual browser verification**

Because hover, focus-visible, arrow animation, and `@media print` are not covered by automated tests, verify them manually in the browser at `https://deharmonie.test/activiteiten/agenda`:

1. **Hover:** hover any activity row. A soft green tint fills the row; a `→` arrow fades in on the right and slides from slightly-left into position.
2. **Click:** clicking the row navigates to the activity detail page.
3. **Keyboard focus:** `Tab` onto a row. A green outline appears with a `2px` offset, and the same background tint and arrow appear.
4. **Cancelled activity:** a cancelled row is visibly dimmed (60% opacity) but still shows the hover background and arrow, and clicking it still opens the detail page.
5. **Past week browsing:** navigate to a past week via the "Previous week" button. Past activities are muted as before, no brand-blue titles, still clickable.
6. **Vertical rhythm:** the gap between stacked activities inside a single day is visually close to what it was before the change.
7. **Print preview:** `Cmd+P` (macOS). The printed output is identical to before — no blue titles, no arrow, no hover background, no outline, no dimmed cancelled rows.

If any of the above fails, stop and diagnose before committing.

- [ ] **Step 5: Commit**

```bash
git add resources/views/activiteiten/agenda.blade.php
git commit -m "feat(agenda): add hover, focus, and arrow affordance for agenda row links"
```

---

### Task 4: Full test suite run

**Files:** none modified

- [ ] **Step 1: Run the whole test suite**

Run: `php artisan test --compact`

Expected: all tests PASS.

- [ ] **Step 2: If anything failed, diagnose and fix**

If a test unrelated to this change fails, it may be a pre-existing issue — check `git stash && php artisan test --compact --filter=<failing test>` to confirm it's not caused by this branch. If it was caused by this branch, fix forward (do not skip, disable, or delete tests).

---

## Definition of Done

- `AgendaClickableRowsTest` passes.
- Full test suite passes.
- Manual browser checks from Task 3 Step 4 all succeed, including print preview.
- Pint shows no dirty PHP files (`vendor/bin/pint --dirty --format agent` is clean).
