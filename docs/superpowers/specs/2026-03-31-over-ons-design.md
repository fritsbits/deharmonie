# Design Spec — Over ons page
_Date: 2026-03-31_

## Overview

Build the Over ons page from its current placeholder state into a finished, warm page. Static Blade view — no new models, controllers, or routes needed. All copy lives in lang files (NL/FR). Quotes are hardcoded in Blade (original language only, no translation per locale).

## Architecture

- **File:** `resources/views/pages/over-ons.blade.php` (already exists, replace placeholder content)
- **Lang keys:** `lang/nl/pages.php` and `lang/fr/pages.php` — add new keys for all new sections
- **Route:** already registered, no changes needed
- **Photos:** all from `public/images/` — no uploads needed
- **No new Livewire components, models, or migrations**

## Sections (top to bottom)

### 1. Hero
Already rendered via `<x-page-hero>`. One change: update the lead text in both lang files to remove overlap with the mission story section.

| Key | NL (current → new) | FR (current → new) |
|-----|-------------------|-------------------|
| `over_ons_lead` | `Al 50 jaar zijn wij er voor de senioren van de Noordwijk in Brussel.` → `Een buurtplek in Brussel waar mensen al 50 jaar samenkomen.` | `Depuis 50 ans, nous sommes là pour les seniors du Noordwijk à Bruxelles.` → `Un lieu de rencontre à Bruxelles où les gens se retrouvent depuis 50 ans.` |

All other hero keys (`over_ons_eyebrow`, `over_ons_heading`) stay unchanged.

---

### 2. Mission story
Layout: 60/40 flex split — text left, portrait-crop photo right. Background: `#fbfaf9`.

**New lang keys needed:**

| Key | NL | FR |
|-----|----|----|
| `over_ons_verhaal_eyebrow` | `Ons verhaal` | `Notre histoire` |
| `over_ons_verhaal_heading` | `Een thuis in de Noordwijk` | `Un chez-soi dans le Noordwijk` |
| `over_ons_verhaal_p1` | `Wat begon als een kleine ontmoetingsplek is uitgegroeid tot het kloppende hart van de Noordwijk. Elke dag komen mensen langs — voor een warme maaltijd, een activiteit, of gewoon om even bij te praten.` | `Ce qui a commencé comme un petit lieu de rencontre est devenu le cœur battant du Noordwijk. Chaque jour, des gens passent — pour un repas chaud, une activité, ou simplement pour échanger quelques mots.` |
| `over_ons_verhaal_p2` | `De deur staat altijd open. Voor iedereen. Of je nu elke week meekookt, iets nieuws wil proberen, of voor het eerst binnenstapt — je bent welkom.` | `La porte est toujours ouverte. Pour tout le monde. Que tu cuisines avec nous chaque semaine, que tu veuilles essayer quelque chose de nouveau, ou que tu entres pour la première fois — tu es le bienvenu.` |
| `over_ons_verhaal_p3` | `In de Noordwijk wonen mensen uit tientallen landen. Dat zie je bij ons terug: aan tafel, in de keuken, in het team. Diversiteit is geen slogan bij De Harmonie. Het is gewoon hoe het hier is.` | `Dans le Noordwijk vivent des gens venus de dizaines de pays. Cela se voit chez nous : à table, en cuisine, dans l'équipe. La diversité n'est pas un slogan à De Harmonie. C'est simplement la réalité ici.` |

**Photo:** `public/images/photo-groep-tafel.webp` — portrait crop (aspect-ratio: 3/4), object-fit: cover.

**Mobile:** image moves below text (flex-direction: column, image full width, aspect-ratio: 16/9).

---

### 3. Photo strip
Full-bleed horizontal strip, ~280px tall. Three photos side by side, no gaps.

| Slot | File |
|------|------|
| Left | `photo-samen.webp` |
| Centre | `photo-buiten-event.webp` |
| Right | `photo-groep-actief.webp` |

Mobile: show 2 photos (hide third with `display:none` via media query).

---

### 4. Visitor voices
Background: `#eef2f8`. Three quote cards in a row. Quotes are **hardcoded in Blade** — they show in original language regardless of locale. Only the section eyebrow is localised.

| Key | NL | FR |
|-----|----|----|
| `over_ons_quotes_eyebrow` | `Wat bezoekers zeggen` | `Ce que disent les visiteurs` |

**Hardcoded quotes (in Blade, not lang files):**

```
Quote 1 (NL): "Hier wordt met veel moed en inzet elke dag gewerkt. Ook met allerlei activiteiten kunnen mensen zich amuseren of iets bijleren." — Josiane C., ★★★★★
Quote 2 (FR): "Un accueil hors du commun. Ils sont des piliers du quartier." — Marc P., ★★★★★
Quote 3 (FR): "Comme d'habitude accueil super chaleureux. On s'y sent bien." — Hélène-Christine A., ★★★★★
```

Card layout: stars (brand-orange) → quote text (italic, 1rem) → attribution (small, muted, uppercase). White card, light blue-border, no heavy shadow. 3 columns desktop, 1 column mobile.

---

### 5. Team reference
Background: `#fbfaf9`. Border-top separator. Text block only — no name pills, no photos. Link to `/wie-is-wie`.

| Key | NL | FR |
|-----|----|----|
| `over_ons_team_eyebrow` | `Het team` | `L'équipe` |
| `over_ons_team_heading` | `De mensen achter De Harmonie` | `Les personnes derrière De Harmonie` |
| `over_ons_team_lead` | `Van de keuken tot het onthaal — ons team staat elke dag voor je klaar. Een divers gezelschap dat één ding gemeen heeft: echte betrokkenheid.` | `De la cuisine à l'accueil — notre équipe est là pour toi chaque jour. Un groupe diversifié qui a une chose en commun : un engagement sincère.` |
| `over_ons_team_cta` | `Ontmoet het team` | `Rencontrez l'équipe` |

Link: `route(app()->getLocale() . '.wie-is-wie')` → ghost button style (border: brand-blue, text: brand-blue, no fill).

---

### 6. CTA band
Background: `#4679bc` (brand-blue). Centered text, orange button → Contact page.

| Key | NL | FR |
|-----|----|----|
| `over_ons_cta_heading` | `Benieuwd hoe het eruitziet?` | `Curieux de voir à quoi ça ressemble ?` |
| `over_ons_cta_lead` | `Kom gerust langs. We zijn er elke weekdag.` | `Venez donc. Nous sommes là tous les jours de la semaine.` |
| `over_ons_cta_btn` | `Neem contact op` | `Contactez-nous` |

Button: `route(app()->getLocale() . '.contact')`, orange (`#eb6643`), white text.

---

## Stage 2 insertion point

After the mission story section, a numbers row (3 impact stats) can be added without layout changes. Section is simply absent in Stage 1 — no placeholder markup needed.

## Responsive breakpoint

At `max-width: 767px`:
- Mission story: flex-direction column, image moves below text, aspect-ratio 16/9
- Photo strip: 2 photos (third hidden)
- Quotes: 1 column stacked
- Team ref: full-width button

Follow the inline `<style>` media query pattern used in `diensten.blade.php`.

## Coding conventions

- Follow `diensten.blade.php` and `wie-is-wie.blade.php` for structure: inline styles, no Tailwind for multi-column layouts, `var(--color-*)` tokens for colours
- Use `__('pages.key')` for all localised strings
- Hardcode the three quotes directly in Blade (not via lang files)
- No new PHP classes or Livewire components
