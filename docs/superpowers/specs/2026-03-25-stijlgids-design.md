# Design Spec: De Harmonie Stijlgids

**Date:** 2026-03-25
**Status:** Approved

## Overview

A hidden style guide page at `/stijlgids` that serves as the single source of truth for all visual components used across the De Harmonie website. Dutch only. Not linked from the nav. Accessible by URL for internal/developer use.

The page is a hybrid: small atomic building blocks at the top, full section previews below. Each item is named and labeled so you can identify it, tweak it here, and copy the styles to other pages. Over time, individual items can be extracted into Blade components.

---

## Access & Routing

- **URL:** `/stijlgids`
- **Route:** Added to `routes/web.php` outside the NL/FR locale groups, wrapped with `middleware('locale:nl')` so `app()->getLocale()` returns `'nl'` throughout the page (required for the nav preview):
  ```php
  Route::middleware('locale:nl')->get('/stijlgids', fn() => view('stijlgids'))->name('stijlgids');
  ```
- **Visibility:** Not linked from nav or footer. `<meta name="robots" content="noindex">` to prevent indexing.
- **Auth:** None — harmless internal reference page
- **Language:** Dutch only (no bilingual content needed for a developer reference)

---

## Layout

- **Shell:** `resources/views/layouts/stijlgids.blade.php` — minimal layout, no site nav or footer. Uses `@vite(['resources/css/app.css'])` (no JS bundle needed — no Livewire or Alpine on this page; the nav section uses a static HTML copy, see below).
- **Structure:** Two-column grid — sticky sidebar (left, ~220px) + scrollable content area (right)
- **Sticky sidebar:** `<nav>` with `position: sticky; top: 1.5rem` — anchor links to each of the 12 sections. No JavaScript.
- **View file:** `resources/views/stijlgids.blade.php`
- **Controller:** None — static view, no Livewire

---

## Page sections

### Zone 1 — Building blocks (atomic, reusable)

| # | ID | Section name | Contents |
|---|---|---|---|
| 1 | `#kleurenpalet` | Kleurenpalet | All 9 color swatches (see token list below) |
| 2 | `#typografie` | Typografie | H1 hero, H2 sectie, H3 kaart, Eyebrow/label, Lead tekst, Body tekst, Klein/meta — each with label, font-size, font-weight |
| 3 | `#knoppen` | Knoppen & links | Primaire knop, Secundaire knop, Donkere knop, Tekstlink, Teruglink |
| 4 | `#formulieren` | Formulierelementen | Label, Tekstveld, Telefoonveld, Tekstvak, Verzendknop, Foutstatus, Successtatus |
| 5 | `#badges` | Badges & statussen | Gratis badge, Geannuleerd badge, Volzet melding, Annuleringsbanner |

### Zone 2 — Full section previews (in-context)

| # | ID | Section name | Contents |
|---|---|---|---|
| 6 | `#navigatie` | Navigatiebalk | Static HTML replica of the nav (see note below) |
| 7 | `#hero` | Hero sectie | Full hero with H1, H2, checklist items, CTA button |
| 8 | `#activiteitenlijst` | Activiteitenlijst item | One example agenda item: thumbnail, title (blue), meta (muted), cancelled variant |
| 9 | `#activiteit-detail` | Activiteit detail sidebar | Sidebar card with Datum, Prijs, Locatie, Contact, dividers |
| 10 | `#registratieformulier` | Registratieformulier | Full registration form with all fields (static HTML, no Livewire) |
| 11 | `#diensten` | Diensten sectie | Services list (bordered, orange bullet) + service card (Grote Kuis style) |
| 12 | `#voettekst` | Voettekst | Static HTML replica of the footer |

**Nav and footer note:** The nav (`resources/views/components/nav.blade.php`) and footer (`resources/views/components/footer.blade.php`) contain dynamic `route()` calls. Sections 6 and 12 use **static HTML copies** with hardcoded `href="#"` values — not `@include` or `<x-nav />`. This avoids locale/route resolution edge cases and makes the style guide self-contained.

---

## All 9 color tokens (complete list for section 1)

| Token | Hex | Name |
|---|---|---|
| `--color-brand-blue` | `#4679bc` | Brand blauw |
| `--color-brand-green` | `#81b59c` | Brand groen |
| `--color-brand-orange` | `#eb6643` | Brand oranje |
| `--color-brand-dark` | `#2c2826` | Brand donker |
| `--color-brand-muted` | `#706662` | Brand gedempd |
| `--color-brand-bg` | `#fbfaf9` | Brand achtergrond |
| `--color-brand-gray` | `#d8d3d2` | Brand grijs |
| `--color-brand-gray-dark` | `#c0bbb9` | Brand grijs donker |
| `--color-brand-medium` | `#4e4543` | Brand medium |

---

## Section anatomy

Each section follows this structure. All labels use **inline styles only** — no custom CSS classes (there are no `eyebrow` or `lead` classes in `app.css`):

```html
<section id="{anchor}" style="padding: 3rem 0; border-bottom: 1px solid var(--color-brand-gray);">

  <!-- Eyebrow label — inline styles -->
  <p style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em;
            color: var(--color-brand-green); margin-bottom: 0.25rem; font-family: var(--font-sans);">
    Stijlgids
  </p>

  <!-- Section heading -->
  <h2 style="font-family: var(--font-sans); font-size: 1.75rem; font-weight: 800;
             color: var(--color-brand-dark); margin-bottom: 1.5rem;">
    Kleurenpalet
  </h2>

  <!-- Optional description — inline styles -->
  <p style="font-size: 1.05rem; line-height: 1.7; color: var(--color-brand-muted); margin-bottom: 1.5rem;">
    ...
  </p>

  <!-- Component(s) rendered here with their exact live styles -->
  ...

</section>
```

Building block sections also include a small `<code>` annotation beneath each element showing which CSS token or style drives it (e.g. `background-color: var(--color-brand-blue)`).

---

## Style approach

All component styles are written **inline** (same as the rest of the site: Tailwind v4 utilities + `style=""` attributes). This ensures what you see in the style guide is byte-for-byte the same as what gets applied elsewhere. No abstraction layer. No custom CSS classes beyond what already exists in `app.css`.

---

## Migration path (hybrid approach)

1. **Now:** Build the style guide as a display-only reference. Use the same inline styles as the live pages.
2. **Later:** When a component is ready to be extracted, create a Blade component (`resources/views/components/`) and update the style guide to use `<x-component-name>` — so the style guide stays in sync automatically.
3. **Gradual migration:** Existing pages can adopt components one by one, without a big rewrite.

---

## Files to create

| File | Purpose |
|---|---|
| `resources/views/layouts/stijlgids.blade.php` | Minimal layout: `<html>`, `<head>` with `@vite(['resources/css/app.css'])`, `<body>` slot |
| `resources/views/stijlgids.blade.php` | The style guide page (all 12 sections) |
| Route in `routes/web.php` | `Route::middleware('locale:nl')->get('/stijlgids', ...)` |

No migrations, no models, no controllers, no Livewire components needed.

---

## Out of scope

- Authentication / access control
- Dark mode
- Bilingual / FR content
- Interactive token editor
- Automated visual regression tests
- Live `<x-nav />` / `<x-footer />` component includes (static copies used instead)
