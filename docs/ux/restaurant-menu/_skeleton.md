# UX Skeleton — Restaurant & Menu (page-level)
_Status: Draft_

## Full-page ASCII mockup (mobile-first)

```
┌──────────────────────────────────────┐
│ NAV                                  │
├──────────────────────────────────────┤
│                                      │
│  [RESTAURANT PHOTO — full width]     │
│   photo-restaurant-vol or            │
│   photo-restaurant-bediening         │
│                                      │
│  Restaurant & Menu          (overlay)│
│  "Elke dag een warm middagmaal"      │
│                                      │
├──────────────────────────────────────┤
│  PRACTICAL INFO                      │
│  ┌─────────────┬────────────────┐    │
│  │ Openingsuren│ Prijs          │    │
│  │ ma–vr       │ v.a. €9        │    │
│  │ 11u15–13u15 │ soep + hoofd   │    │
│  ├─────────────┼────────────────┤    │
│  │ Zonder      │ Antwerpsesteen-│    │
│  │ reservatie  │ weg 24         │    │
│  ├─────────────┴────────────────┤    │
│  │ Afhaal & Levering            │    │
│  │ Bel 's ochtends of stuur een │    │
│  │ e-mail → dezelfde dag klaar  │    │
│  └──────────────────────────────┘    │
│                                      │
├──────────────────────────────────────┤
│  WEEKMENU  23 – 28 maart             │
│                                      │
│  ╔══════════════════════════════╗    │
│  ║ ★ VANDAAG  ma 23/03    €9  ║    │
│  ║ Soep van de dag              ║    │
│  ║ Stoofvlees met kroketjes     ║    │
│  ╚══════════════════════════════╝    │
│  (brand-orange background, larger)   │
│                                      │
│  ┌──────────────────────────────┐    │
│  │ di 24/03                €9  │    │
│  │ Soep van de dag              │    │
│  │ Chicon Gratin met Puree      │    │
│  └──────────────────────────────┘    │
│                                      │
│  ┌──────────────────────────────┐    │
│  │ wo 25/03               €10  │    │
│  │ Soep van de dag              │    │
│  │ Rog in Botersaus             │    │
│  └──────────────────────────────┘    │
│                                      │
│  ┌──────────────────────────────┐    │
│  │ do 26/03                €9  │    │
│  │ Soep van de dag              │    │
│  │ Keuze van Vlees — Stoemp     │    │
│  └──────────────────────────────┘    │
│                                      │
│  ┌──────────────────────────────┐    │
│  │ vr 27/03                €9  │    │
│  │ Soep van de dag              │    │
│  │ Spaghetti Forestière         │    │
│  └──────────────────────────────┘    │
│                                      │
│  ┌──────────────────────────────┐    │
│  │ za 28/03  Gesloten           │    │
│  │ (greyed out)                 │    │
│  └──────────────────────────────┘    │
│                                      │
│  * Allergenen? Vraag aan de kok.     │
│                                      │
├──────────────────────────────────────┤
│  SPECIAL EVENT variant (when active) │
│  ╔══════════════════════════════╗    │
│  ║ do 02/04 — Paasmenu   €20  ║    │
│  ║ Kir Royal                    ║    │
│  ║ Scampi met look              ║    │
│  ║ Eendenborst                  ║    │
│  ║ Duo van IJs                  ║    │
│  ╚══════════════════════════════╝    │
│  (distinct treatment, same stack)    │
│                                      │
├──────────────────────────────────────┤
│  SFEER — Bij ons aan tafel           │
│  [photo-chef-taart]                  │
│  [photo-groep-tafel]                 │
│  [photo-feest-2]                     │
│  (3-up grid or horizontal scroll)    │
│  "Soms is er reden voor iets extra"  │
│  (no CTA)                            │
│                                      │
├──────────────────────────────────────┤
│ FOOTER                               │
└──────────────────────────────────────┘
```

## Element annotations

### Today highlight
- Brand-orange background (`--color-brand-orange`)
- Day label bold + larger
- Visible immediately on load without scrolling
- **Logic:** before 14:00 → today; after 14:00 → tomorrow; if closed → next open day

### Practical info grid
- 2×2 grid on mobile, single row on desktop
- Label above, value below (not icon-dependent — accessible without icons)
- Hours placeholder: 11u15–13u15 — **confirm with team**

### Menu cards
- Standard day: day + date left, price right; soup line (muted); main dish bold
- Special event: full course list, named label (Paasmenu etc.), €20, visually elevated
- Closed day: single greyed row, no soup/main

### Sfeer photos
- Suggested: photo-chef-taart, photo-groep-tafel, photo-feest-2
- Captures: kitchen warmth, community at table, special occasions
- Mobile: vertical stack or horizontal scroll strip

## Open items (to confirm with team)
- Exact lunch hours (using 11u15–13u15 as placeholder from Chambéry reference)
- ~~Email address for takeaway/delivery orders~~ — confirmed: info@deharmonie.be (mailto pre-filled with order template)
