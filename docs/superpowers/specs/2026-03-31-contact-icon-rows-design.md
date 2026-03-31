# Contact Page — Icon Rows Redesign

**Date:** 2026-03-31
**Status:** Approved for implementation

## Summary

Replace the current eyebrow-label contact info blocks with icon-row fields matching the activity detail page pattern. All icon tiles use brand blue — no orange accent in this section.

## Design

### Layout

The contact info column (left side of the two-column layout) uses a vertical list of fields. Each field is an icon-row:

```
[48×48 blue tile]  LABEL (small uppercase blue)
                   Value text (body size, brand-dark)
```

Fields in order:
1. Adres
2. Openingsuren
3. Telefoon
4. Email

### Icon Tile

- **Size:** 48×48px
- **Background:** `rgba(70, 121, 188, 0.12)` (blue tint)
- **Border radius:** 10px
- **Icon:** 20×20 SVG, stroke color `#4679bc` (brand-blue), stroke-width 2

Icons per field:
- Adres → location pin (path + circle)
- Openingsuren → clock (circle + polyline hands)
- Telefoon → phone handset
- Email → envelope (rect + polyline)

### Labels

- `font-family: var(--font-sans)` (Nunito Sans)
- `font-size: 0.7rem`
- `font-weight: 800`
- `text-transform: uppercase`
- `letter-spacing: 0.08em`
- `color: var(--color-brand-blue)` (#4679bc)
- `margin-bottom: 0.25rem`

### Content

**Adres and Openingsuren:**
- `font-size: 1.125rem` (inherits Source Sans 3 from body)
- `line-height: 1.6`
- `color: var(--color-brand-dark)`

**Telefoon:**
- `font-family: var(--font-sans)` (Nunito Sans — display number)
- `font-size: 1.75rem`
- `font-weight: 900`
- `color: var(--color-brand-dark)`
- Wrapped in `<a href="tel:0220328048">`

**Email:**
- Three rows: Algemeen / Animatie & activiteiten / Diensten
- Purpose label: `font-size: 0.8125rem`, `color: var(--color-brand-muted)`, `margin-bottom: 0.05rem`
- Email address: `font-size: 1rem`, `color: var(--color-brand-blue)`, `<a href="mailto:…">`
- Gap between rows: `0.5rem`

### Dividers

Each field-row separated by `border-bottom: 1px solid var(--color-brand-gray)` (`#d8d3d2`). Last row has no border.

### Card wrapper

The four fields sit inside a white card:
- `background: white`
- `border-radius: 10px`
- `padding: 0 1.25rem`

## What Does Not Change

- Photo strip (three images) above the contact block — unchanged
- Map (right column) — unchanged
- Overall two-column layout — unchanged
- Page hero — unchanged
- Mobile responsive behavior — unchanged
- Lang keys for email purpose labels — already added

## Files to Touch

- `resources/views/pages/contact.blade.php` — rewrite left column only
