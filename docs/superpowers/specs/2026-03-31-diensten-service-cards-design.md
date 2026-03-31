---
title: Diensten — Service Cluster Cards
date: 2026-03-31
status: approved
---

# Diensten — Service Cluster Cards

## What we're building

Replace the three plain checklist columns beneath the hero on the Diensten page with proper cards. Each card uses a coloured header with a large decorative icon, matching the established "special activity card" pattern used on the activities overview page.

## Design

### Card structure

Each of the three service clusters becomes a white card with:

- **Border-radius:** `12px` — matches the special activity cards
- **Box shadow:** `0 2px 8px rgba(44,40,38,.09), 0 8px 28px rgba(44,40,38,.10)` — matches the special activity cards

### Coloured header

- Height: determined by content (no fixed height), min ~90px
- Background: brand colour per cluster (see below)
- Top line: cluster label first word(s) in small uppercase muted white (`rgba(255,255,255,.75)`, `0.7rem`, `font-weight: 900`, `letter-spacing: .12em`)
- Bottom line: remaining label word(s) in large bold white (`1.125rem`, `font-weight: 900`)
- Decorative icon: large SVG (110×110px), semi-transparent white (`opacity: 0.18`), positioned bottom-right, rotated `12deg`, `pointer-events: none`

### Card body

- Padding: `1.25rem 1.5rem 1.5rem`
- List items separated by `1px solid rgba(44,40,38,.07)` dividers
- Each item: coloured `✓` (matching the card's accent colour) + body text in `#2c2826`, `0.9375rem`, `line-height: 1.45`
- No bottom border on the last item

### Colours and icons per cluster

| Cluster | Background | Checkmark | Icon |
|---|---|---|---|
| Eten & Activiteiten | `#eb6643` (orange) | `#eb6643` | Fork/plate circle (`heroicons` solid) |
| Begeleiding & Ondersteuning | `#81b59c` (green) | `#81b59c` | Group of people (`heroicons` solid) |
| Thuis & in de buurt | `#4679bc` (blue) | `#4679bc` | Home (`heroicons` solid) |

### Layout

- Three cards in a flex row, `gap: 1.5rem`, each `flex: 1`
- Section background stays `#eef2f8` — cards pop off this naturally
- Section padding: `0 1.5rem 5rem` (unchanged from current)

### Responsive

- Mobile (≤639px): stack to single column, `flex-direction: column`
- Tablet (640–1023px): 2+1 or single column — keep flex-wrap

### Bilingual

Both NL and FR label text fits the two-line header split. The `$clusters` PHP array already contains both locales; the label split (first word(s) + rest) is handled in the template logic.

## What stays the same

- The `#eef2f8` section background
- The CTA row below (phone + email)
- The photo strip, Grote Kuis section, and all other page content
- The `$clusters` PHP data array — only the markup changes

## Files to change

- `resources/views/pages/diensten.blade.php` — replace the cluster markup inside the flex container
