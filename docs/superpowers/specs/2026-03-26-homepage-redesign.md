# Homepage Redesign — Design Spec
**Date:** 2026-03-26
**Status:** Approved for implementation

---

## Overview

Redesign the homepage (`resources/views/activiteiten/index.blade.php`) to match the UX plan defined in `docs/ux/_strategy.md`, `_scope.md`, and `_structure.md`. The goal is a homepage that serves all five audiences in one scroll, leads with community warmth rather than institutional framing, and surfaces the two highest-value pieces of content — the weekly menu and upcoming activities — above the fold.

---

## Section-by-Section Design

### 1. Nav (unchanged)
Keep the existing nav component (`resources/views/components/nav.blade.php`). No changes.

### 2. Hero (change)

**Current:** Large heading "Dienstencentrum / Restaurant Social" + "Quartier Noordwijk" with illustration on the right.

**New:**
- Eyebrow: "Noordwijk · Brussel · Al 50 jaar"
- H1: "Eet mee. Doe mee. Kom langs." (three short lines, large and bold)
- Subheading: warm sentence about the center being the neighborhood heart, daily meal, activities for everyone
- Two CTAs side by side:
  - Primary (orange button): "Bekijk activiteiten" → links to activiteiten index
  - Secondary (blue outline button): "Weekmenu →" → links to weekmenu page
- Right column: real photo instead of illustration — use `photo-restaurant-vol.webp` (photo-groep-tafel.webp is already used in the photo strip below)
- Keep the responsive behavior: hide photo on mobile, stack copy full-width

### 3. Photo strip (keep)
Keep the 3-photo community strip as-is. It works well.

### 4. Menu preview (new section)

**Purpose:** Surface today's and tomorrow's menu with price — the most-searched piece of content on the site.

**Layout:**
- Orange top border (3px, `--color-brand-orange`)
- Warm off-white background (`#fff8f5`)
- Eyebrow: "Restaurant & Menu"
- Heading: "Vandaag & morgen aan tafel"
- Two cards side by side (today + tomorrow):
  - "Vandaag" badge on today's card (orange pill)
  - Day name + date (e.g. "Maandag 23/03")
  - Dish name (bold)
  - "Soep van de dag inbegrepen" (always true — soup is always included)
  - Price (large, orange, e.g. "€ 9")
- Link below cards: "Volledig weekmenu bekijken →"
- On mobile: stack cards vertically

**Data source (Phase 1 — static):** Use hardcoded placeholder content for now. The goal is to validate the design with the client before building the data model. The section will show realistic dummy data (two days, dish name, price). A `TODO` comment marks where dynamic data will be wired in later.

**Phase 2 (future, out of scope here):** Build `Weekmenu` model + migration + Filament resource for Nancy to manage menu entries weekly.

**No edge cases needed for static version.**

### 5. Upcoming activities (change: from list to cards)

**Current:** Activities embedded inside the "Activiteiten" section as a text list.

**New:**
- Section header: eyebrow "Activiteiten" + heading "Komende activiteiten" + "Alle activiteiten →" link right-aligned
- Three cards in a row (use existing `$activiteiten` variable, limit 3)
- Each card:
  - Date (e.g. "Di 25/03") — orange, small caps
  - Activity title — bold
  - Time + location — muted
  - "Inschrijven →" link to activity detail page
  - If cancelled: show cancelled badge, grey out card
- On mobile: stack vertically
- Background: white

**Data:** `$activiteiten` is already available. Keep limit at 3 for homepage (currently shows more).

### 6. Wat we doen — service cards (change: replaces 3 alternating sections)

**Current:** Three full alternating sections (restaurant, activities, services) each with photo + text + CTA.

**New:** Three compact cards in a row on a light blue background (`#f2f6fb`):
- Card 1 — **Samen aan tafel** (orange accent): "Elke dag een warme maaltijd in ons sociaal restaurant. Takeaway en thuisbezorging mogelijk." + price "Vanaf € 9" + link
- Card 2 — **Activiteiten & workshops** (green accent): brief description + link to `nl.activiteiten.index`
- Card 3 — **Bij u thuis** (blue accent): brief description of home services + link to `nl.diensten`
- Each card has a bottom border in its accent color
- On mobile: stack vertically

**Note:** The three large alternating photo sections are removed. This is a significant reduction in page length but increases scannability for the primary audiences (families on mobile).

### 7. Practical info (simplify)

**Current:** Full section with building photo + opening hours + address + contact.

**New:** Compact 3-column bar on `--color-brand-bg`:
- Column 1: Adres — Antwerpsesteenweg 24, 1000 Brussel
- Column 2: Openingsuren — Ma–vr 10:00–16:30 · Za 10:00–14:00
- Column 3: Contact — phone (linked) + email (linked)
- No photo in this bar (the photo strip at top already shows the space)
- On mobile: stack as 3 rows

### 8. Footer (unchanged)
Keep existing footer component.

---

## Bilingual

All new text strings go through the translation system (`__('pages.*')`). Add keys to `lang/nl/pages.php` and `lang/fr/pages.php`.

New keys needed:
- `pages.home_hero_eyebrow`
- `pages.home_hero_heading` (three-line version)
- `pages.home_hero_subheading`
- `pages.home_hero_cta_activities`
- `pages.home_hero_cta_menu`
- `pages.home_menu_preview_heading`
- `pages.home_menu_soup_included`
- `pages.home_menu_link`
- `pages.home_activities_heading`
- `pages.home_services_heading`
- `pages.home_services_intro`
- `pages.home_practical_address_label`
- `pages.home_practical_hours_label`
- `pages.home_practical_contact_label`
- `pages.home_service_restaurant_body` — "Elke dag een warme maaltijd in ons sociaal restaurant. Takeaway en thuisbezorging mogelijk."
- `pages.home_service_restaurant_price` — "Vanaf € 9"
- `pages.home_service_activities_body` — "Van Italiaans leren tot country line dance. Elke week iets om bij te leren of gewoon te genieten."
- `pages.home_service_home_body` — "Poetsen, boodschappen, vervoer, klusjes en maaltijden aan huis."

---

## Controller changes

None required for this phase. The menu preview uses static blade content. The `$activiteiten` limit of 3 is already in place.

---

## Files to change

| File | Change |
|------|--------|
| `resources/views/activiteiten/index.blade.php` | Full rework of sections 2, 4, 5, 6, 7 |
| `app/Http/Controllers/ActivityController.php` | No changes needed |
| `lang/nl/pages.php` | Add new translation keys |
| `lang/fr/pages.php` | Add FR translations |

---

## Out of scope

- Nav changes
- Footer changes
- Weekmenu page itself
- Activity detail page
- Any Phase 2 pages (eerste bezoek, voor familie, etc.)
