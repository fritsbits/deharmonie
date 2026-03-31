# UX Structure — Activiteiten (page-level)
_Status: Draft_

## Page hierarchy

```
/activiteiten                    ← Invitation page (this section)
  └── /activiteiten/{slug}       ← Activity detail + registration

/activiteiten/agenda             ← Full calendar (separate, utility page)
  (or /activiteiten/kalender)
```

## Overview page — information architecture

Content flows from **emotional → conceptual → practical**:

1. **Hero** — establishes tone and context (warm, active)
2. **Photo strip** — visceral proof before any words: real people, real energy
3. **Reeksen** — the recurring fabric of the place; gives sense of community rhythm
4. **Special events** — the exciting layer; upcoming one-off activities worth joining
5. **Full agenda CTA** — exits to the calendar for people ready to plan

The page does NOT contain a full calendar. That is a deliberate structural decision: the calendar is for people already convinced. The invitation page is for everyone else.

## Detail page — information architecture

Content flows from **identity → context → action**:

1. **Back navigation** — grounding, wayfinding
2. **Activity photo** — immediate warmth
3. **Title + Reeks context** — what is this, is it part of a series?
4. **Cancellation notice** (conditional) — honest, prominent
5. **Description** — context and invitation
6. **Registration form** — primary action
7. **Sidebar** (sticky) — practical facts + anchor CTA to form + print

## Interaction design

### Overview page
- Reeksen section: static (server-rendered from `activiteit_templates`)
- Special events: server-rendered, filtered to upcoming `activiteiten` where `template_id IS NULL`, limit ~3
- "Full agenda" link navigates to separate calendar page (no in-page toggle)

### Detail page
- Sidebar "Schrijf je in" button: smooth-scrolls to registration form anchor
- Registration form: Livewire, inline validation, success state replaces form
- Cancellation: form hidden, notice prominent, sidebar CTA removed
- Print: separate print-optimized route

## Key flows

**Curious outsider discovering De Harmonie:**
Homepage → Activiteiten overview → [sees Reeksen + special events] → Activity detail → Registration form → Confirmation email

**Regular checking their class:**
Homepage → Activiteiten overview → Full agenda → [finds their reeks] → (no registration needed)

**Family member evaluating for their parent:**
Homepage → Activiteiten overview → [sees photo strip + variety of Reeksen] → Activity detail → Contact
