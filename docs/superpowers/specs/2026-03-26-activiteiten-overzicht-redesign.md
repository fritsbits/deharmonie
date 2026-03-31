# Activiteiten Overzicht — Redesign Spec

## Goal

Replace the current activiteiten overview page with a warmer, photo-forward layout that clearly communicates two types of activities: recurring weekly sessions (grouped by theme) and special upcoming events (sign-up required).

---

## Page Structure

Three sections, in order:

1. **Hero** — updated lead copy
2. **Thematic cards** — recurring activities, paper aesthetic, + agenda link
3. **Bijzondere momenten** — photo grid + upcoming events list + Facebook link

No photo strip (removed). No week view on this page (reserved for the agenda/calendar page).

---

## Section 1: Hero

Existing `<x-page-hero>` component, existing eyebrow/heading translation keys.

**Lead text update** — the `:lead` copy must explain both activity types in one breath:

> "Elke week staan er vaste activiteiten op het programma — van bewegen tot knutselen en gesprekken. Daarnaast organiseren we bijzondere momenten waarvoor je je beter op voorhand inschrijft."

Translation keys to add:
- `activities.overview_tagline` (NL): above copy
- `activities.overview_tagline` (FR): equivalent

No structural changes to the hero component.

---

## Section 2: Thematic Cards ("Elke week bij ons")

Replaces the current 18-item icon-card grid entirely.

### Data model

Query `ActiviteitTemplate` (recurring activities), grouped into 4 hardcoded themes:

| Theme (NL) | Theme (FR) | Color token | Activities |
|---|---|---|---|
| **Beweeg mee** | **Bougez avec nous** | `--color-brand-orange` (`#eb6643`) | Zumba, Pilates & Fitness, Stoel-gym, Country Line Dance |
| **Maak iets** | **Créez ensemble** | `--color-brand-green` (`#81b59c`) | Diamond Painting, Naaiworkshop, Creativiteit |
| **Praat & leer** | **Parlez & apprenez** | `--color-brand-blue` (`#4679bc`) | Conv. NL/Spaans/Engels/Italiaans, Geheugenatelier, Digitale workshop |
| **Vier mee** | **Fêtez avec nous** | `#d4956a` | Bingo, Verjaardagsfeest, Culturele uitstap, Boodschappendienst |

Theme groupings are hardcoded in the view — not database-driven. Each theme has a short italic tagline (hardcoded NL/FR strings).

### Card design — paper aesthetic

Four cards in a flex row, each rotated slightly and staggered vertically (matching the menu page paper element):

```
rotate: -2deg / +1.8deg / -1deg / +2.2deg
margin-top stagger: 1.5rem / 0 / 2.5rem / 0.5rem
```

Each card:
- Thin 4px color band at top (theme color)
- **140px photo** — one real photo per theme (using existing `public/images/` photos):
  - Bewegen → `photo-groep-actief.webp`
  - Creatief → `photo-visitors-2.webp`
  - Praten & Leren → `photo-samen.webp`
  - Samen → `photo-party.webp`
- Card body: theme name (900 weight), italic tagline, dashed activity list with day+time on the right
- Paper curl shadow via `::before`/`::after` pseudo-elements (same technique as menu page)
- Semi-transparent "tape" decoration at the top of 3 cards (orange, green, blue tints)

### CTA below cards

Ghost button (dark border, dark text) centered below the card row:

> "Bekijk de volledige agenda →" → links to `{locale}.activiteiten.agenda`

Translation key: `activities.agenda_link` (already exists).

---

## Section 3: Bijzondere Momenten

### Layout

3-column CSS grid, 2 rows (`240px / 220px`):

```
[big photo — spans 2 rows] | [small photo top]    | [upcoming events card — spans 2 rows]
                           | [small photo bottom] |
```

Grid: `grid-template-columns: 2fr 1.5fr 1.5fr`

### Photo grid

Photos from `public/images/`:
- Big (span): `photo-feest-2.webp` — "Feest van 51 jaar De Harmonie"
- Middle top: `photo-buiten-event.webp` — "Culturele uitstap"
- Middle bottom: `photo-cake.jpg` — "Verjaardagsfeest"

Each photo has a gradient caption overlay:
- Gradient: `linear-gradient(to top, rgba(20,16,14,0.85) 0%, rgba(20,16,14,0.5) 50%, transparent 100%)`
- Caption: Nunito Sans, 800 weight, 1rem, white, with `text-shadow`

### Upcoming events card (sidebar)

A white card (`border-radius: 10px`, green-tinted border) spanning both grid rows. Contains:

- Small header: "Aankomende activiteiten" + "Inschrijven aanbevolen" subline
- List of upcoming `Activiteit` records (special/non-recurring), ordered by `datum` ascending, limit ~5
- Per row: date (large day + abbreviated month), activity name, theme tag badge, time, "Inschrijven →" link to the activity detail page

**Data query**: `Activiteit::whereNull('template_id')->where('datum', '>=', today())->where('status', 'gepubliceerd')->orderBy('datum')->limit(5)->get()`

`template_id IS NULL` means one-off special event. `template_id IS NOT NULL` means generated from a recurring `ActiviteitTemplate`. No `is_recurring` column exists — use the foreign key nullability.

Theme tag colors match section 2 (orange/green/blue/warm).

### Intro copy + Facebook block

Above the grid — intro paragraph (2 sentences):
1. Explains special activities exist
2. "Schrijf je in via de site of via het secretariaat."

Translation keys to add:
- `activities.special_moments_intro` (NL + FR)

Below the grid — Facebook follow block (white card, green-tinted border):
- Facebook "F" icon (blue circle)
- Copy: "Volg De Harmonie op Facebook" + subtitle about photos and announcements
- Link to Facebook page (use existing URL from footer or contact page)

Translation keys to add:
- `activities.facebook_follow_heading` (NL + FR)
- `activities.facebook_follow_body` (NL + FR)

---

## Translation keys summary

New keys needed in `lang/nl/activities.php` and `lang/fr/activities.php`:

| Key | NL | FR |
|---|---|---|
| `overview_tagline` | "Elke week staan er vaste activiteiten…" | "Chaque semaine, des activités sont au programme…" |
| `special_moments_intro` | "Regelmatig organiseren we iets extra's… Schrijf je in via de site of via het secretariaat." | FR equivalent |
| `facebook_follow_heading` | "Volg De Harmonie op Facebook" | "Suivez De Harmonie sur Facebook" |
| `facebook_follow_body` | "Foto's, nieuwtjes en aankondigingen van bijzondere activiteiten" | FR equivalent |

Existing keys to keep:
- `reeksen_eyebrow`, `reeksen_heading` (`'Elke week bij ons'`)
- `special_moments_eyebrow`, `special_moments_heading`
- `agenda_link`

---

## Week view (not on this page)

The week-view paper element (Mon–Fri columns, color-coded by theme, navigation arrows) is designed and ready. It belongs on the **agenda/calendar page** (`activiteiten.agenda`), not here. Spec that page separately.

---

## What stays the same

- `<x-page-hero>` component and its props
- Routing, controller, Livewire components — no changes
- All existing translation keys not listed above
- Mobile breakpoint handling (single column, hide right column of moments grid on small screens)

---

## Testing

- `ActiviteitenOverviewTest` — existing 4 tests must still pass
- Add: assert theme section renders 4 theme names (Bewegen, Creatief, Praten & Leren, Samen)
- Add: assert upcoming events card renders activities with future `datum` and `template_id IS NULL`
- Add: assert agenda CTA link resolves to correct named route for both locales
