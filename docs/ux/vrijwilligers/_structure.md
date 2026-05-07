# UX Structure — Vrijwilligerspagina
_Status: Draft_

## Page placement in sitemap

```
Homepage
├── Activiteiten
│   └── [Activity detail]
├── Restaurant & Menu
│   └── Weekmenu
├── Diensten
│   ├── In het centrum
│   └── Bij u thuis
├── Over ons
│   └── Wie is wie
├── Contact
└── [Footer]
    └── Vrijwilligers ← NEW (footer link, not primary nav)
```

**Decision: footer link, not primary nav item.**
The site strategy calls for a flat 5-item primary nav for clarity with a 65+ audience. Volunteer recruitment is secondary to the core visitor/family flows. Footer placement keeps it findable without disrupting the primary navigation hierarchy.

Cross-link opportunities:
- **Over ons** page → add a volunteer mention with link at the bottom
- **Homepage** → optional small "Word vrijwilliger" teaser card (Phase 2)
- **Footer** → "Vrijwilligers" link in the secondary footer links

## Routes

| Locale | Route | Named route |
|--------|-------|-------------|
| NL | `/vrijwilligers` | `nl.vrijwilligers` |
| FR | `/fr/benevoles` | `fr.benevoles` |

## Page sections (ordered)

1. **Page hero** — standard `<x-page-hero>` component, orange bg
2. **Photo strip** — 3-panel horizontal, same pattern as contact page
3. **Why volunteer** — 3-card grid on brand-bg
4. **What you can do** — 2-column: text left, role list right
5. **Contact / CTA** — form or email link on blue-tint bg

## Information architecture decisions

- **No sub-pages** — everything on one compact page. Volunteer information is simple enough to fit.
- **Contact at the bottom** — not forced above the fold. User should see context (why, what) before being asked to act.
- **No "process" section** — avoid making it feel like an application. If there's a 3-step process (contact → coffee → start), keep it light and informal, not a numbered funnel.
