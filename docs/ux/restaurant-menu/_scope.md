# UX Scope — Restaurant & Menu (page-level)
_Status: Draft_

## Phase 1 content (build now)

| Block | Content | Notes |
|-------|---------|-------|
| Restaurant intro | Photo + short warm tagline | Photos available in public/images/ |
| Practical info | Hours, price, walk-in, address | Hours TBD — confirm with team (placeholder: 11u15–13u15) |
| Weekly menu | Mon–Sat, soup + main, price per day | Loaded from JSON flat file for now; Filament later |
| Special event menus | Multi-course, named (Paasmenu etc.), €20 | Visually distinct within the same menu stack |
| Closed days | Single greyed row ("Gesloten") | Public holiday: named label (e.g. "Paasmaandag: Gesloten") |
| Allergen note | "Allergenen? Vraag aan de kok." | Small, below the menu |
| Sfeer section | 3-photo strip, warm sentence | No CTA — atmosphere only |

## Phase 2 (after team meeting)

- Delivery/takeaway — exists but details unknown; add once confirmed
- Solidarity pricing table — currently just "v.a. €9" hint

## Menu data structure (per day)

```json
{
  "date": "2026-03-23",
  "nl": {
    "soup": "Soep van de dag",
    "main": "Stoofvlees met Sla en Kroketjes"
  },
  "fr": {
    "soup": "Potage du jour",
    "main": "Carbonnades, Frites et Salade"
  },
  "price": 9.00,
  "closed": false,
  "closed_label_nl": null,
  "closed_label_fr": null,
  "special_event": false,
  "courses": []
}
```

Special event day example (Easter):
```json
{
  "date": "2026-04-02",
  "nl": { "label": "Paasmenu" },
  "fr": { "label": "Menu de Pâques" },
  "price": 20.00,
  "closed": false,
  "special_event": true,
  "courses": [
    { "nl": "Kir Royal", "fr": "Kir Royal" },
    { "nl": "Scampi met look", "fr": "Scampi à l'Ail" },
    { "nl": "Eendenborst", "fr": "Magret de Canard" },
    { "nl": "Gestoofd Witloof", "fr": "Chicons Braisés et pdt Rissolées" },
    { "nl": "Duo van Ijs op Lente wijze", "fr": "Duo de Glace Printanière" }
  ]
}
```

## Future: Filament + PDF

Goal: Nancy enters the weekly menu in Filament admin → site displays it → PDF auto-generated for printing. Flat file is a prototype step only.
