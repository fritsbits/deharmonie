# UX Skeleton — Activiteiten (page-level)
_Status: Draft_

---

## Overview page — `/activiteiten`

```
┌──────────────────────────────────────────────────────────┐
│ NAV                                                      │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  HERO                                                    │
│  eyebrow: "Activiteiten"  (green)                        │
│  h1: warm, action-oriented headline                      │
│  tagline: 1 sentence — community, not schedule           │
│  bg: white                                               │
│                                                          │
├──────────────────────────────────────────────────────────┤
│▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ PHOTO STRIP ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
│  [  photo  ] [  photo  ] [  photo  ] [  photo  ]         │
│  full bleed, ~280px tall, no gaps                        │
│  people doing things — yoga, games, outings, eating      │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  REEKSEN — bg: off-white (#fbfaf9)                       │
│  eyebrow: "Elke week" (blue)                             │
│  heading: "Vaste activiteiten"                           │
│  subtext: 1 sentence — "Kom gerust elke week langs"      │
│                                                          │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐            │
│  │ [icon]     │ │ [icon]     │ │ [icon]     │            │
│  │ Titel      │ │ Titel      │ │ Titel      │            │
│  │ dag · uur  │ │ dag · uur  │ │ dag · uur  │            │
│  │ 1-line     │ │ 1-line     │ │ 1-line     │            │
│  │ beschr.    │ │ beschr.    │ │ beschr.    │            │
│  └────────────┘ └────────────┘ └────────────┘            │
│  grid: 3 columns desktop, 2 tablet, 1 mobile             │
│  icon: Lucide icon per reeks (assigned manually)         │
│  card: light border, no heavy shadow                     │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  SPECIAL EVENTS — bg: green-tint (#eef5f1)               │
│  eyebrow: "Binnenkort" (green)                           │
│  heading: "Bijzondere activiteiten"                      │
│                                                          │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐      │
│  │▓▓▓▓ photo ▓▓│ │▓▓▓▓ photo ▓▓│ │▓▓▓▓ photo ▓▓│      │
│  │              │ │              │ │              │      │
│  │ datum        │ │ datum        │ │ datum        │      │
│  │ Titel        │ │ Titel        │ │ Titel        │      │
│  │ tijd · loc.  │ │ tijd · loc.  │ │ tijd · loc.  │      │
│  │ [Meer info →]│ │ [Meer info →]│ │ [Meer info →]│      │
│  └──────────────┘ └──────────────┘ └──────────────┘      │
│  3 upcoming activities where template_id IS NULL         │
│  photo top, date prominent (green), title large           │
│  fallback: colored placeholder if no photo               │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  FULL AGENDA LINK                                        │
│  centered, subtle                                        │
│  "Bekijk de volledige agenda →"                          │
│  text link or soft ghost button — not a primary CTA      │
│                                                          │
├──────────────────────────────────────────────────────────┤
│ FOOTER                                                   │
└──────────────────────────────────────────────────────────┘
```

**Mobile adjustments:**
- Photo strip: 3 photos instead of 4, or 2 tall
- Reeksen: 1 column
- Special events: 1 column, stacked cards

---

## Detail page — `/activiteiten/{slug}`

```
┌──────────────────────────────────────────────────────────┐
│ NAV                                                      │
├──────────────────────────────────────────────────────────┤
│  ← Alle activiteiten                                     │
│  (back link, brand-blue, top of content area)            │
├──────────────────────────────────────────────────────────┤
│▓▓▓▓▓▓▓▓▓▓▓ ACTIVITY PHOTO — full width ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
│  ~360px tall if image exists                             │
│  fallback: brand-green color band (no broken image)      │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  TITLE AREA — bg: white                                  │
│  eyebrow: "Activiteit" OR reeks name if template_id set  │
│  h1: activity title (large, uppercase, brand-dark)       │
│  alt-language title in muted below (if exists)           │
│                                                          │
│  [!] CANCELLATION NOTICE — if geannuleerd               │
│  orange bg band, prominent, full width of content area  │
│                                                          │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  MAIN (2/3)              │  SIDEBAR (1/3, sticky top)    │
│  bg: green-tint          │  bg: white, border            │
│                          │                               │
│  Description             │  📅 Datum                     │
│  (18px+, readable,       │     woensdag 15 april         │
│   leading-relaxed)       │     14:00 – 16:00             │
│                          │                               │
│  ──────────────────       │  💰 Prijs                     │
│                          │     € 5  /  Gratis [badge]    │
│  REGISTRATION FORM       │                               │
│  white card, anchor: #   │  📍 Locatie                   │
│  inschrijven             │     Grote zaal                │
│                          │                               │
│  Naam *                  │  ──────────────────           │
│  [________________]      │                               │
│                          │  [Schrijf je in →]            │
│  Telefoon                │  anchor → #inschrijven        │
│  [________________]      │  shown only if gepubliceerd   │
│                          │                               │
│  E-mail                  │  ──────────────────           │
│  [________________]      │                               │
│                          │  📞 De Harmonie               │
│  Bericht (optioneel)     │     02 203 28 48              │
│  [________________]      │     info@deharmonie.be        │
│                          │                               │
│  [Inschrijven]           │  ──────────────────           │
│  primary orange button   │                               │
│                          │  [🖨 Afdrukken]               │
│                          │  ghost button, small          │
│                          │                               │
└──────────────────────────┴───────────────────────────────┘
│ FOOTER                                                   │
└──────────────────────────────────────────────────────────┘
```

**Key decisions:**
- **Sidebar "Schrijf je in" CTA** — smooth-scrolls to form anchor. Solves the problem of the form being far down the page for 65+ users who may not scroll that far.
- **Sidebar CTA hidden when cancelled** — no false affordance.
- **Reeks context in eyebrow** — if activity has a `template_id`, show the reeks name instead of generic "Activiteit". Signals recurring community.
- **Photo as hero** — if image exists, it runs full-width above the title. More inviting than the current white header band.
- **Photo fallback** — brand-green color band so the page never looks broken without an image.

**Mobile:**
- Sidebar moves below the description
- "Schrijf je in" button appears at top of page (below title) as a sticky anchor CTA
- Form remains at bottom but is immediately reachable
```
