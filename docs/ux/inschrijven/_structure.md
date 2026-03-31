# UX Structure — Inschrijvingsflow

## Flow map

```
Activity detail page
├── [spots available]  → Registration form
│                         ↓ submit
│                         Inline success state (form replaced, no redirect)
│                         ↓ simultaneously
│                         ├── Visitor confirmation email (bilingual)
│                         └── Staff notification email (reply-to: registrant)
│                                         ↓
│                               Staff inbox — reply if needed
│
└── [fully booked]     → "Fully booked" notice (entire section hidden, replaced)

Admin /deelnameverzoeken
└── Read-only list
    └── Row click → Side panel with full registration details
```

## Interaction design notes

- **No page redirect** on successful submission — form swaps to success state inline
- **Emails are synchronous** — sent immediately on submit (low volume, no queue needed)
- **Fully booked** replaces the entire registration section (CTA heading + form card), not just the form
- **Admin** is reference-only — no actions, no state changes
- **Side panel** opens on row click — no separate detail page needed
