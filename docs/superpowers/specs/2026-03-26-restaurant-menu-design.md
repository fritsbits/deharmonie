# Restaurant & Menu Page — Design Spec
_Date: 2026-03-26_

## Overview

Replace the current Google Doc iframe on `/restaurant-menu` with a structured, mobile-first weekly menu page. Data comes from a JSON flat file for Phase 1; the structure is designed to migrate cleanly to Filament + database in Phase 2.

---

## Architecture

**Approach:** JSON flat file + controller logic

- Menu data lives in `resources/data/weekmenu.json`
- `PageController::weekmenu()` reads the file, resolves the highlighted day, passes data to the view
- View loops over days to render cards
- No new service classes, no new Blade components — reuse `<x-eyebrow>` and inline card HTML

---

## Data Structure

`resources/data/weekmenu.json`:

```json
{
  "week": {
    "nl": "23 – 28 maart 2026",
    "fr": "23 – 28 mars 2026"
  },
  "days": [
    {
      "date": "2026-03-23",
      "closed": false,
      "closed_label_nl": null,
      "closed_label_fr": null,
      "special_event": false,
      "price": 9.00,
      "nl": { "soup": "Soep van de dag", "main": "Stoofvlees met Sla en Kroketjes", "courses": [] },
      "fr": { "soup": "Potage du jour", "main": "Carbonnades, Frites et Salade", "courses": [] }
    }
  ]
}
```

Special event day:
```json
{
  "date": "2026-04-02",
  "closed": false,
  "special_event": true,
  "price": 20.00,
  "nl": { "event_label": "Paasmenu", "courses": ["Kir Royal", "Scampi met look", "Eendenborst", "Gestoofd Witloof", "Duo van IJs op Lente wijze"] },
  "fr": { "event_label": "Menu de Pâques", "courses": ["Kir Royal", "Scampi à l'Ail", "Magret de Canard", "Chicons Braisés et pdt Rissolées", "Duo de Glace Printanière"] }
}
```

Closed day with named label:
```json
{
  "date": "2026-04-07",
  "closed": true,
  "closed_label_nl": "Paasmaandag: Gesloten",
  "closed_label_fr": "Lundi de Pâques: Fermé",
  "special_event": false,
  "price": null,
  "nl": { "soup": null, "main": null, "courses": [] },
  "fr": { "soup": null, "main": null, "courses": [] }
}
```

---

## Controller Logic

```php
public function weekmenu()
{
    $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);

    $now = now();
    $candidate = $now->hour >= 14 ? $now->copy()->addDay() : $now->copy();

    // Find the first non-closed day from candidate forward
    $highlightedDate = $candidate->toDateString();
    foreach ($data['days'] as $day) {
        if ($day['date'] >= $candidate->toDateString() && !$day['closed']) {
            $highlightedDate = $day['date'];
            break;
        }
    }

    return view('pages.weekmenu', [
        'week'            => $data['week'],
        'days'            => $data['days'],
        'highlightedDate' => $highlightedDate,
    ]);
}
```

---

## Page Sections

1. **Hero** — full-width restaurant photo (`photo-restaurant-vol.webp`) with dark gradient overlay. Title "Restaurant & Menu" + tagline over the image.
2. **Practical info** — 2×2 grid: Openingsuren / Prijs / Reservatie / Adres & contact. No abbreviations. Hours placeholder: 11u15–13u15 (confirm with team).
3. **Weekmenu** — section header with week label (right-aligned). Vertical stack of day cards.
4. **Sfeer** — 3-photo strip (photo-chef-taart, photo-groep-tafel, photo-feest-2) + one warm sentence. No CTA.
5. **Footer** — standard site footer.

---

## Card Types

### Standard day
- Day label (uppercase, muted) + date
- "Soep van de dag" (small, muted)
- Main dish (1.25rem, bold, dark) — the primary content
- Price (orange, right-aligned)

### Today / next meal highlighted
- Warm off-white background (`#fff8f5`)
- Orange left border (4px)
- Small "Vandaag" badge (orange pill) above the day label
- After 14:00: tomorrow's card gets the highlight instead
- If that day is closed: advance to next open day

### Special event
- Warm cream background (`#fff8f0`)
- Orange border (2px, full)
- "Speciaal" badge + event name (e.g. "Paasmenu")
- Full course list as bullet points
- Price top-right

### Closed day
- Light grey background, reduced opacity
- Day label + "Gesloten" (or named label for public holidays)
- No soup/main shown

---

## Bilingual

- All labels use translation keys (`__('weekmenu.today')`, etc.)
- Day/main/soup content comes from the JSON `nl` or `fr` key based on `app()->getLocale()`
- Week label string comes from `$week['nl']` or `$week['fr']`
- Day names rendered via Carbon: `Carbon::parse($day['date'])->locale(app()->getLocale())->isoFormat('dddd D/MM')`

### New translation keys needed
```
weekmenu.today          → Vandaag / Aujourd'hui
weekmenu.soup           → Soep van de dag / Potage du jour  (fallback if JSON null)
weekmenu.closed         → Gesloten / Fermé  (fallback if no label)
weekmenu.allergen_note  → Allergenen? Vraag aan onze kok. / Allergènes ? Demandez à notre cuisinier.
weekmenu.hours_label    → Openingsuren / Heures d'ouverture
weekmenu.price_label    → Prijs / Prix
weekmenu.price_value    → Vanaf € 9 / À partir de € 9
weekmenu.price_sub      → soep + hoofdgerecht / potage + plat principal
weekmenu.walkin_label   → Reservatie / Réservation
weekmenu.walkin_value   → Gewoon binnenlopen / Entrez librement
weekmenu.address_label  → Adres & contact / Adresse & contact
weekmenu.sfeer_label    → Bij ons aan tafel / À notre table
weekmenu.sfeer_caption  → Soms is er reden voor iets extra. Onze kok zorgt voor de rest. / Parfois, il y a une raison de faire quelque chose de spécial. Notre cuisinier s'occupe du reste.
weekmenu.special_badge  → Speciaal / Spécial
```

---

## Phase 2 Migration Path

When Filament CRUD is ready:
- Controller swaps `file_get_contents(resource_path(...))` for a `Weekmenu::currentWeek()->with('days')->first()` query
- View and card logic stay identical — data shape is the same
- JSON file can be retired

---

## Open Items

- **Lunch hours** — using 11u15–13u15 as placeholder. Confirm with Cynthia/Nancy before go-live.
- **Delivery/takeaway** — omitted in Phase 1. Add once details confirmed with team.
- **PDF generation** — future goal (Nancy prints the weekly menu). Out of scope for this implementation.
