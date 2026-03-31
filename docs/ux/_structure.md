# UX Structure — De Harmonie (site-level)
_Status: Draft_

## Sitemap (Phase 1)

```
Homepage
├── Activiteiten
│   └── [Activity detail page]
├── Restaurant & Menu
│   └── Weekmenu (current week — always up to date)
├── Diensten
│   ├── In het centrum
│   └── Bij u thuis (incl. Grote Kuis as highlighted service)
├── Over ons
│   └── Wie is wie (team)
└── Contact
```

## Navigation

Primary nav (5 items): Activiteiten / Restaurant & Menu / Diensten / Over ons / Contact

Keep it flat. This is a 65+ audience and their families — no dropdowns, no mega menus.
Language switcher always visible (NL / FR).

## Key user flows

**Family member searching for their parent:**
Homepage → Diensten → Contact (or future: Eerste bezoek)

**Visitor checking the menu:**
Homepage → Restaurant & Menu → Weekmenu
(or: direct link from Facebook post → Weekmenu)

**Social worker referring a client:**
Homepage → Diensten → Contact (or future: Voor doorverwijzers)

**New visitor curious about activities:**
Homepage → Activiteiten → [Activity detail] → Registration form

## IA decisions

- "Restaurant & Menu" is a primary nav item, not buried under Diensten. The social restaurant is the heart of the center and the most-searched content.
- "Over ons" contains Wie is wie as a sub-page — team belongs inside the "about" narrative, not standalone in the nav.
- Grote Kuis = prominent section within Diensten > Bij u thuis. Not a separate page (Phase 1).
- Contact = standalone page with opening hours, address (canonical: Antwerpsesteenweg 24), phone, map.
- Saturday hours must be clearly communicated: usually closed, occasionally open for special events.

## Bilingual routing

- NL: `/` (no prefix, default)
- FR: `/fr/`
- Every public page exists in both languages
- Language switcher on every page
