---
# UX Skeleton — Over ons (page-level)
_Status: Draft_

```
┌──────────────────────────────────────────────────────────┐
│ NAV                                                      │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  HERO — bg: white                                        │
│  eyebrow: "Over De Harmonie"  (blue)                     │
│  h1: "Vijftig jaar hart voor de Noordwijk"               │
│  lead: warm 1-sentence positioning line                  │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  MISSION STORY — bg: off-white (#fbfaf9)                 │
│  eyebrow: "Ons verhaal"  (green)                         │
│  heading: warm, active — "Een thuis in de Noordwijk"     │
│                                                          │
│  ┌─────────────────────────┐  ┌───────────────────┐      │
│  │ 2–3 paragraphs          │  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  │      │
│  │ neighborhood story      │  │ ▓ community  ▓▓▓  │      │
│  │ 50 years, diverse team, │  │ ▓▓▓  photo  ▓▓▓▓  │      │
│  │ warm daily rhythm,      │  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  │      │
│  │ anyone welcome          │  │ portrait crop,    │      │
│  │                         │  │ warm moment       │      │
│  │ (18px+, leading-relaxed)│  └───────────────────┘      │
│  └─────────────────────────┘                             │
│  layout: 60/40 split, image right                        │
│                                                          │
│  [Stage 2: impact numbers row inserts here]              │
│  [3 large numbers — meals/week, visitors, activities]    │
│                                                          │
├──────────────────────────────────────────────────────────┤
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ PHOTO STRIP ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
│  [ photo-groep-tafel ] [ photo-samen ] [ photo-buiten ]  │
│  full bleed, ~280px tall, no gaps                        │
│  people at De Harmonie — eating, laughing, outside       │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  VISITOR VOICES — bg: blue-tint (#eef2f8)                │
│  eyebrow: "Wat bezoekers zeggen"  (blue)                 │
│                                                          │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────┐ │
│  │ ★★★★★        │  │ ★★★★★        │  │ ★★★★★        │ │
│  │               │  │               │  │               │ │
│  │ "Hier wordt   │  │ "Un accueil   │  │ "Comme        │ │
│  │  met veel     │  │  hors du      │  │  d'habitude   │ │
│  │  moed en      │  │  commun. Ils  │  │  accueil      │ │
│  │  inzet elke   │  │  sont des     │  │  super        │ │
│  │  dag gewerkt" │  │  piliers du   │  │  chaleureux.  │ │
│  │               │  │  quartier."   │  │  On s'y sent  │ │
│  │ — Josiane C.  │  │               │  │  bien."       │ │
│  │               │  │ — Marc P.     │  │               │ │
│  │               │  │               │  │ — Hélène-     │ │
│  │               │  │               │  │   Christine A.│ │
│  └───────────────┘  └───────────────┘  └───────────────┘ │
│  3 columns desktop / 1 column mobile                     │
│  quote in large italic, name in small muted below        │
│  stars in brand-orange                                   │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  TEAM REFERENCE — bg: off-white (#fbfaf9)                │
│  eyebrow: "Het team"  (green)                            │
│  heading: "De mensen achter De Harmonie"                 │
│  1 warm sentence about team diversity and daily presence │
│                                                          │
│  [Ontmoet het team →]                                    │
│  text link or soft ghost button → /wie-is-wie            │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  CTA BAND — bg: brand-blue (#4679bc)                     │
│  heading: "Benieuwd hoe het eruitziet?"                  │
│  subtext: "Kom gerust langs. We zijn er elke weekdag."   │
│                                                          │
│  [Neem contact op →]                                     │
│  white text, orange button → /contact                    │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ FOOTER                                                   │
└──────────────────────────────────────────────────────────┘
```

## Key decisions

- **60/40 text/image split on mission story** — gives the narrative room to breathe without becoming a wall of text. Image anchors the warmth without dominating.
- **Photo strip before quotes** — photos prime the emotional response; quotes then confirm it with real voices. Swapping them weakens both.
- **Stars before quote text** — star rating establishes credibility before the reader even reads the words. Psychological trust primer.
- **Team section is a single sentence + link** — keeps Over ons clean, gives Wie is wie a reason to exist. Don't duplicate the full list here.
- **CTA points to Contact, not Activiteiten** — Over ons readers are in a "considering" state. Their next step is reaching out, not registering. Activiteiten is the right destination for people who are already convinced.
- **Stage 2 insertion point** — impact numbers slot in between mission story and photo strip without changing the layout. One row of 3 numbers (large type, brand-dark) with labels.

## Mobile adjustments

- Mission story: image moves below text (stacked, full width)
- Photo strip: 2 photos instead of 3
- Visitor voices: single column, stacked cards
- Team reference: centered text, full-width button
- CTA band: stacked, centered

## Photo candidates (from public/images/)

| Section | Candidate files |
|---------|----------------|
| Mission story (right col) | `photo-groep-tafel.webp`, `photo-samen.webp` |
| Photo strip [1] | `photo-groep-tafel.webp` |
| Photo strip [2] | `photo-samen.webp` or `photo-buiten-event.webp` |
| Photo strip [3] | `photo-buiten-activiteit.webp` or `photo-party.webp` |

Avoid reusing photos already prominent on other pages (restaurant photos on weekmenu, activity photos on activiteiten).
