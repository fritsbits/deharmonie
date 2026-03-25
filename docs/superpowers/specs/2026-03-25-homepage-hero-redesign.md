# Homepage Hero Redesign

**Date:** 2026-03-25
**Status:** Approved for implementation

## Context

The current homepage hero uses two small overlapping photos beside the title. The activity list lives in a separate agenda section at the bottom. The design undersells De Harmonie's warm community identity and creates redundancy (weekmenu CTA duplicated, activity list disconnected from the activities intro).

## Approved Layout

```
NAV (blue)
──────────────────────────────────────────────
HERO: text only
  - Eyebrow: "Noordwijk · Brussel"
  - H1: "Dienstencentrum / Restaurant Social"
  - H2: "Quartier Noordwijk" (green)
  - No photo. No bullets. Clean.
──────────────────────────────────────────────
SECTION 1 — Restaurant (photo left, text right)
  Photo: photo-restaurant.jpg (crowd eating)
  Tag: "Sociaal restaurant · Restaurant social"
  H2: "Elke dag samen aan tafel"
  Body NL: Dagschotels aan verminderd tarief...
  Body FR: (italic, muted) Plat du jour...
  CTA: "Weekmenu de la Semaine →" (blue button)
──────────────────────────────────────────────
SECTION 2 — Activities (photo right, content left)
  Photo: activities carousel (party photo as default)
    - Carousel arrows + dots indicating swipeable
    - Photos: photo-party.jpg, photo-cake.jpg, photo-thumbsup.jpg
  Right side (full height, warm bg #f5f2ef):
    - Tag: "Activiteiten · Activités" (green)
    - H2: "Creatief, cultureel en sportief"
    - Body NL + Body FR
    - Live activity list (from Livewire ActivityFilter, limit 5)
      Each row: pastel thumb | name + date/time/location
    - CTA: "Alle activiteiten →" (green button)
──────────────────────────────────────────────
SECTION 3 — Services (photo left, text right)
  Photo: photo-samen.jpg (laughing couple)
  Tag: "Diensten · Services" (blue)
  H2: "Ook hulp waar u het nodig heeft"
  Body NL: Partner voor iedereen...
  Body FR: (italic, muted) Partenaire pour tout le monde...
  CTA: "Onze diensten →" (orange button)
──────────────────────────────────────────────
OPENING HOURS (existing — unchanged)
FOOTER (existing — unchanged)
```

## What Changes

- Remove: current hero (two overlapping photos + three bullet points)
- Remove: standalone AGENDA section (moved into section 2)
- Add: text-only hero header
- Add: three full-width alternating sections with photos
- Integrate: Livewire `activity-filter` into section 2 right column (limit 5)

## Photos Needed

Upload these files to `public/images/`:
- `photo-restaurant.jpg` — Facebook restaurant crowd photo
- `photo-party.jpg` — cultural party/dancing event photo
- `photo-cake.jpg` — cake celebration photo
- `photo-samen.jpg` — laughing couple with phone (already exists as `photo-visitors-2` equivalent)

Carousel in section 2 uses: photo-party.jpg, photo-cake.jpg, photo-thumbsup.jpg

## Design Tokens

All existing tokens apply. No new tokens needed.
- Section backgrounds alternate: white → `#f5f2ef` → `#f0efed`
- Section photos: `flex: 0 0 42%`, `object-fit: cover`
- Bilingual body copy: NL in `var(--color-brand-dark)`, FR italic in `var(--color-brand-muted)`

## Livewire

`ActivityFilter` component stays as-is. In the new layout it renders inside section 2's right column. Limit stays at 5 items (was 10 on old homepage). The "Alle activiteiten" + "Toutes les activités" buttons from the component's footer become the section CTA.

## What Does NOT Change

- Nav component
- Footer component
- Opening hours section (after the three sections)
- Activiteiten index page
- Activity detail pages
- All other static pages
