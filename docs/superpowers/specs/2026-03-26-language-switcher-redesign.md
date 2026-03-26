# Language Switcher Redesign

**Date:** 2026-03-26

## Problem

The current language switcher ("Français" / "Nederlands") blends into the nav links with no visual distinction. On a blue background with white text, it reads as just another nav item — making it easy to overlook, especially for seniors.

## Design

### Desktop

Replace the current language link with a three-part control:

1. **Vertical divider** — `1px` wide, `1.25rem` tall, `rgba(255,255,255,0.35)` — separates nav links from language control
2. **Globe icon** — Heroicons outline `GlobeAltIcon`, 16×16px, `opacity: 0.75`, inline with text
3. **NL / FR toggle** — two-letter codes only, no translation strings needed:
   - Current locale: full opacity (`1.0`), not a link
   - Separator `/`: `opacity: 0.4`, `font-size: 0.75rem`
   - Other locale: `opacity: 0.6`, underlined, links to `set-locale` route

Route logic (determining target URL from current route name) is unchanged from today.

### Mobile

In the Alpine.js dropdown:
- Language entry stays at the bottom, after Contact
- Replace full word with the same globe icon + `NL / FR` inline pattern
- Retain the existing `border-top` visual separator already present between Contact and language

### What changes

- `resources/views/components/nav.blade.php` — desktop and mobile language switcher markup
- Remove dependency on `nav.language_switch` translation key (codes are language-neutral)

### What stays the same

- `set-locale` route and redirect logic
- All other nav links and layout
- Mobile hamburger behaviour
