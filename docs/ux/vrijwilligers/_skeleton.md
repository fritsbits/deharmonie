# UX Skeleton — Vrijwilligerspagina
_Status: Draft — updated with yearly report data_

## Full-page layout

```
┌─────────────────────────────────────────────────────────┐
│ NAV  [logo]   Activiteiten · Menu · Diensten · Over ons · Contact   NL|FR │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  HERO  [bg: orange]                                     │
│                                                         │
│  eyebrow: "Vrijwilligers"                               │
│  h1: "Word deel van ons team"                           │
│  lead: "Bij De Harmonie draait 80% van alle             │
│  activiteiten op vrijwilligers. Van bezoeker            │
│  naar organisator — jouw bijdrage maakt het             │
│  verschil."                                             │
│                                                         │
├─────────────────────────────────────────────────────────┤
│  PHOTO STRIP  [260px, 3 panels]                         │
│  ┌───────────────┬───────────────┬───────────────┐      │
│  │  community    │  activity in  │  festival /   │      │
│  │  gathering    │  action       │  Plazey       │      │
│  └───────────────┴───────────────┴───────────────┘      │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  80% STAT  [bg: brand-bg, centered]                     │
│                                                         │
│  "80% van onze activiteiten wordt geleid                │
│   door vrijwilligers."                    [large, bold] │
│                                                         │
│  "Dankzij hun inzet en betrokkenheid konden wij         │
│  onze werking in 2025 verderzetten en versterken."      │
│                                              [smaller]  │
│                                                         │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  WHY VOLUNTEER  [bg: white, 3 cards]                    │
│                                                         │
│  h2: "Waarom vrijwilliger worden?"                      │
│                                                         │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐     │
│  │  [icon]     │  │  [icon]     │  │  [icon]     │     │
│  │             │  │             │  │             │     │
│  │ Van bezoeker│  │ Doe wat je  │  │ Jij bepaalt │     │
│  │ naar orga-  │  │ graag doet  │  │ hoe vaak    │     │
│  │ nisator     │  │             │  │ je er bent  │     │
│  │             │  │ Dansen,     │  │             │     │
│  │ Ken jij de  │  │ conversa-   │  │ Wekelijks   │     │
│  │ buurt? Deel │  │ ties leiden,│  │ of één keer │     │
│  │ wat er leeft│  │ schilderen. │  │ per jaar.   │     │
│  └─────────────┘  └─────────────┘  └─────────────┘     │
│                                                         │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  WHAT YOU CAN DO  [bg: brand-bg, 2-col]                 │
│                                                         │
│  ┌───────────────────────┬───────────────────────┐      │
│  │  INTRO TEXT           │  ROLE GROUPS          │      │
│  │                       │                       │      │
│  │  "Ben je bezoeker     │  Activiteiten leiden  │      │
│  │  bij De Harmonie en   │  ────────────────────  │      │
│  │  wil je zelf iets     │  • Ciné-Club          │      │
│  │  organiseren? Of wil  │  • Conversatietafel   │      │
│  │  je een handje toe-   │  • Dans & bewegen     │      │
│  │  steken? We zijn blij │  • Creatief atelier   │      │
│  │  met elk initiatief.  │  • Iets nieuws?       │      │
│  │  Er is altijd een     │                       │      │
│  │  plek voor jou."      │  Meehelpen            │      │
│  │                       │  ────────────────────  │      │
│  │                       │  • Plazey festival    │      │
│  │                       │  • Activiteitsdagen   │      │
│  └───────────────────────┴───────────────────────┘      │
│                                                         │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  CONTACT  [bg: brand-blue-tint]                         │
│                                                         │
│  h2: "Geïnteresseerd? Kom eens kennismaken."            │
│  subtext: "Stuur ons een berichtje. Geen               │
│  verplichtingen — we plannen gewoon een                 │
│  koffiemoment."                                         │
│                                                         │
│  ┌─────────────────────────────────────────┐            │
│  │  [Naam                               ]  │            │
│  │  [E-mailadres                        ]  │            │
│  │  [Bericht (optioneel)                ]  │            │
│  │                                         │            │
│  │  [ Stuur je bericht → ]  (orange CTA)   │            │
│  └─────────────────────────────────────────┘            │
│                                                         │
│  — OR if no form —                                      │
│  📧  [email]  |  📞 02/203.28.48                        │
│                                                         │
├─────────────────────────────────────────────────────────┤
│ FOOTER  (includes "Vrijwilligers" link)                  │
└─────────────────────────────────────────────────────────┘
```

## Section notes

### Hero
- Orange background, same as homepage CTA sections
- Lead text now leads with the **80% stat** — this is the hook that makes the page feel urgent and meaningful
- No CTA button in hero — guide user through the page, CTA at the bottom

### Photo strip
- 3-panel, 260px, same pattern as contact page
- Panel 1: `photo-gemeenschap.webp` — lachende man aan tafel, community warmth
- Panel 2: `photo-handwerk.webp` — craft/handwork activity, shows what volunteers lead
- Panel 3: `photo-muzikanten.webp` — musicians/festival energy, Plazey vibe
- 3rd panel hidden on mobile

### 80% stat
- This deserves its own visual moment — not buried in a card. Centered, large quote-style treatment
- Source: jaarverslag 2025

### Why volunteer (3 cards)
- Card 1: "Van bezoeker naar organisator" — the distinctive angle. You don't need to be an expert, you can lead from your own experience as a visitor
- Card 2: "Doe wat je graag doet" — passion-led volunteering, not service
- Card 3: "Jij bepaalt hoe vaak je er bent" — dissolves over-commitment fear

### What you can do (2-column)
- Two groups of roles: **Activiteiten leiden** (run your own) + **Meehelpen** (support existing)
- Confirmed from yearly report: Ciné-Club, Conversatietafel (Engels/Spaans/Italiaans/NL), Crea atelier, Diamond Painting, Bingo, Zumba, Country Line Dance, Stoelengym, Verjaardagen
- "Iets nieuws?" bullet is important — signals openness to new ideas
- On mobile: stack (intro → Activiteiten leiden → Meehelpen)

### Contact
- Reassurance copy: "Geen verplichtingen — we plannen gewoon een koffiemoment" directly addresses the over-commitment fear
- Simple form: Naam + Email + Bericht (optional) — 3 fields max
- If form is skipped: prominent mailto link + phone number

## Mobile behavior

| Section | Mobile |
|---------|--------|
| Hero | Full width, text centered |
| Photo strip | 2 panels (3rd hidden) |
| 80% stat | Full width, large text |
| Why cards | Stack vertically |
| What you can do | Stack (intro → role groups) |
| Contact form | Single column, full width |

## Decisions (resolved)

- **HermoVis** — voice-to-text error, not a real concept. No named contact team.
- **Email** — `info@deharmonie.be` with pre-filled subject `?subject=Vrijwilliger+bij+De+Harmonie`
- **Contact** — mailto link, not a Livewire form. Styled prominently.
- **Photos** — use existing: `photo-gemeenschap.webp`, `photo-handwerk.webp`, `photo-muzikanten.webp`
- **Nav** — footer link only (keep 5-item primary nav)
