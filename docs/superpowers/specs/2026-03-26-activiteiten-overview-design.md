# Design Spec — Activiteiten Overview Page
_Date: 2026-03-26_

## Goal

Replace the current activities overview page (`/activiteiten`) — a bare chronological list — with an invitation-first page that makes a curious outsider feel "something's alive here, I'd fit in."

The page is **not a calendar**. It shows what kind of place De Harmonie is. The calendar is a separate linked page.

---

## Page sections (top to bottom)

### 1. Hero
- Existing `<x-page-hero>` component — keep as-is
- Eyebrow: "Activiteiten" (green)
- Heading + tagline: warm, action-oriented copy (NL + FR)
- Background: white

### 2. Photo strip
- Full-bleed horizontal strip, ~280px tall, no gaps
- 4 photos side-by-side (existing activity photos already in `public/images/`)
- People doing things — yoga, games, outings, eating together
- No captions, no interaction — pure atmosphere

### 3. Reeksen — "Elke week bij ons"
- **Dynamic** — rendered from `activiteit_templates` table (all 18 templates)
- Layout: 2-column card grid (desktop), 1 column (mobile)
- Each card: Lucide icon (colored square background, brand palette) + title + "Elke [dag] om [tijd]" + 1-line description from `beschrijving_nl`/`beschrijving_fr`
- Icon mapping: hardcoded array in the view, keyed by `template_id`
- Section heading: "Elke week bij ons" (NL) / "Chaque semaine chez nous" (FR)
- Background: off-white (`#fbfaf9`)

### 4. Special moments — "Bijzondere momenten"
- **Static** — hardcoded in the Blade template
- 3–4 curated photos from past special events (Valentine's dinner, anniversary party, iftar, theatre outing etc.)
- Layout: asymmetric grid — 1 large photo left, 2 smaller stacked right (or 3 equal, TBD during implementation)
- Optional: short caption per photo (event name, no date needed)
- No links, no CTA — purely atmospheric
- Background: green-tint (`#eef5f1`)
- Photos stored in `public/images/`, chosen and placed by developer at build time

### 5. Full agenda link
- Simple centered line: "Bekijk de volledige agenda →"
- Links to existing `/activiteiten/overzicht` (the Livewire calendar page)
- Ghost button or plain text link — not a primary CTA
- Background: white

---

## What this page does NOT contain
- A calendar or date-filtered list (that's `/activiteiten/overzicht`)
- Admin-managed event photos (copyright risk, maintenance burden)
- Registration (that's the detail page)
- Filtering or search

---

## Routing
- This page replaces the current `activiteiten/overzicht.blade.php` view
- Route stays the same: `nl.activiteiten.index` / `fr.activiteiten.index`
- The existing Livewire `ActivityOverzicht` component (full calendar) moves to a separate URL: `/activiteiten/agenda` — new route needed

---

## Reeksen icon mapping
Hardcoded in the view as a PHP array `$iconMap = [template_id => 'lucide-icon-name']`. Icons from lucide.dev. Color backgrounds use the existing brand palette color rotation already used in activity thumbnails.

Example mapping (to be finalized during implementation):
```php
$iconMap = [
    1  => 'message-circle',      // Conversatietafel Spaans
    2  => 'message-circle',      // Conversatietafel Engels
    3  => 'message-circle',      // Conversatietafel Italiaans
    4  => 'message-circle',      // Nederlandse conversatietafel
    5  => 'music-4',             // Country Line Dance
    6  => 'brain',               // Geheugenatelier
    7  => 'armchair',            // Stoel-gym met Nicole
    8  => 'monitor',             // Digitale workshop
    9  => 'dices',               // Bingo
    10 => 'palette',             // Creativiteit workshop
    11 => 'zap',                 // Zumba
    12 => 'gem',                 // Diamond Painting
    13 => 'scissors',            // Naaiworkshop
    14 => 'shopping-bag',        // Boodschappendienst
    15 => 'activity',            // Pilates & Fitness
    16 => 'info',                // Sociale Infopunt
    17 => 'cake',                // Verjaardagsfeest
    18 => 'landmark',            // Culturele uitstap
];
```

---

## Homepage activities section — same shift needed
The homepage currently shows activity cards in calendar style (date-first, list-like). It should be updated in the same pass:
- Remove the current `@forelse ($activiteiten as $activiteit)` card list
- Replace with 2–3 special events shown as warm invitation cards (photo placeholder + title + date + "Meer info →")
- Or: a simpler teaser linking to the new overview page
- This is a separate task but closely related

---

## Out of scope
- Detail page redesign (separate spec)
- Admin UI for managing Reeks icons or photos
- Unsplash / stock photo integration
- Activity type filtering
