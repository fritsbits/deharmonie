# UX Scope — Activiteiten (page-level)
_Status: Draft_

## Overview page (`/activiteiten`)

### What we're building

An invitation-first page that shows the richness and warmth of De Harmonie's program — not a calendar. The calendar exists as a separate, linked page for people already convinced.

### Content sections

| Section | Source | Purpose |
|---------|--------|---------|
| **Hero** | Existing page-hero component | Context — eyebrow, heading, warm tagline |
| **Photo strip** | Existing photos (already mapped) | Emotional proof — real people, real energy, full bleed |
| **Reeksen** | `activiteit_templates` table | "We have a rich weekly life here" — recurring activity types with Lucide icon + title + 1-line description |
| **Special events** | `activiteiten` where `template_id IS NULL`, upcoming only | "Exciting things happen here" — 3 upcoming one-off activities as inviting cards |
| **Full agenda CTA** | Link to separate calendar page | Utility path for planners and regulars |

### What's NOT on this page

- Full chronological calendar (separate page)
- Filtering / search (future scope if needed)
- Registration (happens on detail page)

### Data model insight

- **Reeksen** = `activiteit_templates` — recurring series with `dag_van_de_week`, time, location, NL/FR title/description
- **Special events** = `activiteiten` where `template_id IS NULL`
- This separation already exists in the database — no new data structures needed

### Content needs

- Lucide icon per Reeks — to be assigned manually (no DB field needed, map in code)
- Photo strip photos — already mapped and available in `public/images/`
- Warm hero copy (NL + FR) — needs writing

---

## Detail page (`/activiteiten/{slug}`)

### What we're building

A warm, trust-building activity page that gives someone enough context to decide to register — and then makes registration easy.

### Content sections

| Section | Source | Purpose |
|---------|--------|---------|
| **Back link** | Route to overview | Navigation |
| **Activity photo hero** | Spatie Media Library `afbeelding` | Immediate visual warmth; fallback to brand-color band |
| **Title + Reeks context** | `activiteiten.titel`, `template_id` join | Identity — what is this, is it part of a series? |
| **Cancellation notice** | `status = geannuleerd` + `notice` | Honest communication, prominent but not alarming |
| **Description** | `beschrijving` | Context — what happens, who's it for |
| **Registration form** | Livewire `RegistrationForm` | Primary action |
| **Sidebar: practical info** | `datum`, `startuur`, `einduur`, `prijs`, `locatie` | Decision-making facts |
| **Sidebar: contact** | Static (De Harmonie phone + email) | Reassurance / fallback |
| **Sidebar: register CTA** | Anchor to form | Shortcut for people who skip the description |
| **Print link** | Print route | Physical reminder, important for 65+ audience |
