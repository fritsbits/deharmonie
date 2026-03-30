# UX Structure — Restaurant & Menu (page-level)
_Status: Draft_

## Page sections (top to bottom)

```
Restaurant & Menu
├── Restaurant intro       — photo + warm tagline
├── Practical info         — hours, price, walk-in, address, takeaway/delivery
├── Weekly menu            — current week, day-by-day stack
│   ├── Standard day       — day + date, soup, main, price
│   ├── Special event day  — named menu, full course list, price
│   └── Closed day         — greyed, "Gesloten" or named label
├── Sfeer                  — 3-photo strip, atmosphere, no CTA
└── Footer
```

## IA decisions

- **One page, no sub-pages.** Restaurant overview and menu live together. No tab switching, no separate "menu" link.
- **Current week only** in Phase 1. No week navigation. Keeps implementation simple and avoids stale content confusion.
- **Practical info before the menu.** Answers "can I come today / how much?" for first-timers before they hit the menu. Returning visitors scroll past it instantly.
- **Special event menus stay in the weekly stack** — not a separate section. They appear in context as part of that week, visually distinct.

## Key user flows

**Returning visitor (direct from Facebook link):**
→ Page loads → scrolls past intro + practical info → finds today's highlighted card immediately

**Family member (first visit):**
→ Page loads → reads intro → scans practical info (hours + price + walk-in + delivery option) → browses the week → feels confident

**Today logic:**
- Before 14:00 → highlight today's card
- After 14:00 → highlight tomorrow's card (next meal opportunity)
- If today is closed → highlight next open day

## Inspiration reference

Chambéry wijkrestaurant (chambery.be) — hours and pricing above a full weekly menu, allergen note below. Their horizontal 6-column table works on desktop; we use a vertical stack optimized for mobile.
