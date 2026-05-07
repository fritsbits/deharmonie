# Volunteer Discovery — Design Spec

**Date:** 2026-05-07
**Status:** Approved

## Problem

The Vrijwilligers page exists but is hard to discover. It was added to the main nav, but the nav is already at capacity (5 items for a 60+ audience) and volunteering is a secondary goal — most visitors come for activities or the menu. The page needs to be findable without occupying a top-level nav slot.

## Solution Overview

Three coordinated changes:

1. **Section nav on all About pages** — a dark blue sub-row at the bottom of the site header, linking between Over ons / Wie is wie / Vrijwilligers
2. **Volunteer section on the Over Ons page** — a dedicated section above the existing "Wie is wie: Het Team" section
3. **Slim volunteer strip on the homepage** — a narrow band near the bottom of the homepage

---

## 1. Section Nav (Header Sub-row)

### Placement
A second row appended to the bottom of the existing blue header (`resources/views/components/nav.blade.php`). Sits inside the `<header>` element, below the main nav row. Appears **only** on the three About-section pages:
- Over ons
- Wie is wie
- Vrijwilligers

### Visual design
- Background: slightly darker blue than the header (`#3a68a8`, i.e. about 10% darker than `--color-brand-blue`)
- A thin separator line (`rgba(255,255,255,0.12)`) above the row
- Three tab links: `Over ons · Wie is wie · Vrijwilligers`
- Inactive tabs: `color: rgba(255,255,255,0.6)`, no underline
- Active tab: `color: white`, `border-bottom: 2px solid white`
- Font: `--font-sans`, `0.875rem`, `font-weight: 700`
- Padding: `0.35rem 0` per tab, `0.6rem` horizontal padding per tab

### Routing
| Tab label (NL) | Route name | Tab label (FR) |
|---|---|---|
| Over ons | `{locale}.over-ons` | À propos |
| Wie is wie | `{locale}.wie-is-wie` | Qui est qui |
| Vrijwilligers | `{locale}.vrijwilligers` | Bénévoles |

Active state is determined by matching the current route name.

### Main nav change
Remove the standalone `Vrijwilligers` link from the main nav (both desktop and mobile). It is now exclusively reached via the section nav. The main nav shrinks from 5 items to 4: Activiteiten / Weekmenu / Over ons / Contact.

### Mobile
On mobile, the section nav sub-row appears below the hamburger menu toggle row (always visible, not inside the collapsible menu). Same three tabs, same dark blue background, scrollable horizontally if needed.

### Implementation
The sub-row is conditional. The nav component checks the current route name: if it starts with `{locale}.over-ons`, `{locale}.wie-is-wie`, or `{locale}.vrijwilligers`, the sub-row renders. No changes needed to individual blade files.

---

## 2. Volunteer Section on the Over Ons Page

### Placement
New section inserted directly **above** the existing "Wie is wie: Het Team" section (`resources/views/pages/over-ons.blade.php`), which currently starts at the `{{-- TEAM REFERENCE --}}` comment.

### Visual design
Mirrors the visual rhythm of the Team section: same `var(--color-brand-bg)` background, same `border-top: 1px solid #e8e5e2`, same `3.5rem 0` padding.

Layout: two-column flex row (image left, text right), gap `3rem`, collapses to single column on mobile.

**Left column — image**
- A real photo from the existing image library (e.g. `photo-gemeenschap.webp` or `photo-handwerk.webp`)
- `border-radius: 12px`, `overflow: hidden`, fixed height ~260px, `object-fit: cover`
- Same `img-outline` treatment as other sections

**Right column — text**
- Eyebrow (orange): `__('pages.over_ons_vrijwilligers_eyebrow')`
- Heading (h2): `__('pages.over_ons_vrijwilligers_heading')`
- Lead paragraph: `__('pages.over_ons_vrijwilligers_lead')`
- CTA button (outline style, orange border + text): `__('pages.over_ons_vrijwilligers_cta')` → links to `{locale}.vrijwilligers`

### Copy keys to add (NL / FR)

| Key | NL | FR |
|---|---|---|
| `over_ons_vrijwilligers_eyebrow` | Doe mee | Participez |
| `over_ons_vrijwilligers_heading` | Word vrijwilliger bij De Harmonie | Devenez bénévole à De Harmonie |
| `over_ons_vrijwilligers_lead` | Heb je een paar uur per maand en wil je iets betekenen voor de buurt? We zijn altijd op zoek naar enthousiaste vrijwilligers die mee activiteiten begeleiden. | Vous avez quelques heures par mois et souhaitez contribuer au quartier ? Nous recherchons des bénévoles enthousiastes pour co-animer nos activités. |
| `over_ons_vrijwilligers_cta` | Meer over vrijwilligerswerk | En savoir plus |

---

## 3. Slim Volunteer Strip on the Homepage

### Placement
New section inserted between the **social proof photo strip** (`{{-- SOCIAL PROOF PHOTO STRIP --}}`) and the **practical info section** (`{{-- PRACTICAL INFO --}}`) in `resources/views/activiteiten/index.blade.php`.

### Visual design
A single narrow band — not a full section with cards. Comparable height to a call-to-action bar.

- Background: `var(--color-brand-green)` (warm, distinct from the orange already used on the homepage CTAs)
- Padding: `2rem 1.5rem`
- Content: centered, single row on desktop (stacks on mobile)
  - Short headline (left): `__('pages.home_vrijwilligers_heading')` — e.g. "Wil je meehelpen bij De Harmonie?"
  - Button (right): `__('pages.home_vrijwilligers_cta')` — white pill button → `{locale}.vrijwilligers`

### Copy keys to add

| Key | NL | FR |
|---|---|---|
| `home_vrijwilligers_heading` | Wil je meehelpen bij De Harmonie? | Vous souhaitez aider à De Harmonie ? |
| `home_vrijwilligers_cta` | Word vrijwilliger | Devenir bénévole |

---

## Affected Files

| File | Change |
|---|---|
| `resources/views/components/nav.blade.php` | Add dark blue sub-row; remove standalone Vrijwilligers link |
| `resources/views/pages/over-ons.blade.php` | Add volunteer section above Team section |
| `resources/views/activiteiten/index.blade.php` | Add slim green volunteer strip |
| `lang/nl/pages.php` | Add new copy keys (Over ons volunteer section + homepage strip) |
| `lang/fr/pages.php` | Same |
| `lang/nl/nav.php` | No change needed (vrijwilligers key stays, used in footer + section nav) |
| `lang/fr/nav.php` | No change needed |

---

## Out of Scope

- No changes to the Vrijwilligers page itself
- No changes to the Wie is wie page layout
- No changes to the footer (already links to Vrijwilligers)
- No form or application flow — the volunteer CTA links to the existing page, which has a mailto CTA
