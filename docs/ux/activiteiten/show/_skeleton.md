# UX Skeleton — Activity Detail Page
_Status: Draft_

---

## Desktop (≥ 768px)

```
┌──────────────────────────────────────────────────────────┐
│ NAV                                                      │
├──────────────────────────────────────────────────────────┤
│  bg: white                                               │
│                                                          │
│  ← Alle activiteiten  (brand-blue, small)                │
│                                                          │
│  eyebrow: "Conversatietafel" OR "Activiteit" (green)     │
│  H1: ACTIVITY TITLE  (uppercase, bold, brand-dark)       │
│  alt-lang title  (muted, 1rem, below)                    │
│                                                          │
│  [!] CANCELLATION NOTICE  ← only if geannuleerd          │
│  orange bg, full-width of content area                   │
│                                                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│  │ DATUM        │  │ TIJD         │  │ PRIJS        │    │
│  │ wo 26 mrt    │  │ 10:00–12:00  │  │ Gratis       │    │
│  └──────────────┘  └──────────────┘  └──────────────┘    │
│  Locatie: Grote zaal  (inline, below strip, muted)       │
│                                                          │
├──────────────────────────────────────────────────────────┤
│  bg: green-tint (#eef5f1)                                │
│                                                          │
│  DESCRIPTION (2/3)          │  SIDEBAR (1/3, sticky)     │
│  18px+, leading-relaxed     │  bg: white, border         │
│  brand-dark                 │  rounded                   │
│                             │                            │
│  Lorem ipsum description    │  [  Schrijf je in →  ]    │
│  text flows here...         │  orange, full-width        │
│                             │  anchor → #inschrijven     │
│  ─────────────────          │  hidden if geannuleerd     │
│                             │                            │
│  REGISTRATION FORM          │  ──────────────────        │
│  id="inschrijven"           │                            │
│  white card, rounded        │  De Harmonie               │
│                             │  02 203 28 48 (linked)     │
│  Naam *                     │  info@deharmonie.be        │
│  [____________________]     │                            │
│                             │  ──────────────────        │
│  E-mailadres                │                            │
│  [____________________]     │  [🖨 Afdrukken]            │
│                             │  ghost button, small       │
│  Telefoon (optioneel)       │                            │
│  [____________________]     │                            │
│                             │                            │
│  Bericht (optioneel)        │                            │
│  [____________________]     │                            │
│                             │                            │
│  [     Inschrijven     ]    │                            │
│  orange, full-width         │                            │
│                             │                            │
│  ── SUCCESS STATE ──        │                            │
│  ✓  Bedankt! Je bent        │                            │
│     ingeschreven.           │                            │
│     (replaces form)         │                            │
│                             │                            │
└─────────────────────────────┴────────────────────────────┘
│ FOOTER                                                   │
└──────────────────────────────────────────────────────────┘
```

---

## Mobile (< 768px)

```
┌──────────────────────────────┐
│ NAV                          │
├──────────────────────────────┤
│  bg: white                   │
│                              │
│  ← Alle activiteiten         │
│                              │
│  eyebrow (green)             │
│  H1: TITLE                   │
│  alt-lang (muted)            │
│                              │
│  [!] cancellation notice     │
│                              │
│  DATUM       TIJD            │
│  wo 26 mrt   10:00–12:00     │
│  PRIJS       LOCATIE         │
│  Gratis      Grote zaal      │
│                              │
├──────────────────────────────┤
│  bg: green-tint              │
│                              │
│  Description text (18px+)    │
│                              │
│  ─────────────────           │
│                              │
│  SIDEBAR                     │
│  (moves above form,          │
│   below description)         │
│                              │
│  De Harmonie                 │
│  02 203 28 48                │
│  info@deharmonie.be          │
│  [🖨 Afdrukken]              │
│                              │
│  ─────────────────           │
│                              │
│  REGISTRATION FORM           │
│  id="inschrijven"            │
│  white card                  │
│                              │
│  Naam *                      │
│  [____________________]      │
│                              │
│  E-mailadres                 │
│  [____________________]      │
│                              │
│  Telefoon (optioneel)        │
│  [____________________]      │
│                              │
│  Bericht (optioneel)         │
│  [____________________]      │
│                              │
│  [     Inschrijven     ]     │
│  orange, full-width          │
│                              │
│  ── SUCCESS STATE ──         │
│  ✓ Bedankt! Ingeschreven.    │
│                              │
├──────────────────────────────┤
│  STICKY BOTTOM BAR           │
│  [  Schrijf je in →  ]       │
│  shown while form off-screen │
│  hidden once form visible    │
│  hidden if geannuleerd       │
└──────────────────────────────┘
│ FOOTER                       │
└──────────────────────────────┘
```

---

## Key decisions

- **Logistics in header, not sidebar** — date/time/price/location live in the white header band below the title. Seniors see all critical info before scrolling. Sidebar repeats nothing — it's just CTA + contact + print.
- **Sidebar CTA anchors to form** — smooth-scroll to `#inschrijven`. Solves the problem of the form being far down the page.
- **Sidebar CTA is orange** — primary action, matches the submit button. Consistent signal.
- **Success state replaces form** — no redirect, no separate page. Visually clear: green check, direct message.
- **Mobile sticky CTA** — appears at bottom of screen while the form is above the fold; disappears when user reaches the form. Prevents losing the CTA on long description pages.
- **No photo, no placeholder** — white header is clean and intentional, not broken.
