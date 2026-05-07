# UX Scope — Vrijwilligerspagina
_Status: Draft_

## What we're building

A single compact page that:
1. Welcomes potential volunteers warmly
2. Gives them a quick sense of what volunteering looks like at De Harmonie
3. Offers a frictionless first contact step

## Content requirements

### Must have
- **Hero**: Inviting headline + warm lead sentence (1-2 lines max)
- **Photo strip**: 2-3 photos showing community moments — volunteers or visitors in action, real and human
- **Why volunteer**: 3 short, human reasons (community, meaning, flexibility)
- **What you can do**: 3-5 example volunteer roles (see open questions for full list)
- **Contact entry point**: Form (name, email, message) OR direct email link with pre-filled subject
- **Bilingual**: Full NL/FR

### Nice to have (Phase 2)
- A short quote or testimonial from an existing volunteer
- A "Kom langs voor een koffiemoment" CTA (informal meet & greet before committing)

### Explicitly excluded
- Formal application process or multi-step form
- Lengthy role descriptions or contracts
- Volunteer management / scheduling features
- Separate pages per volunteer type

## Volunteer roles (from yearly report + Notion)

Two distinct types — the page should make both visible:

**Type 1: Activiteitenleiders** (lead their own activity)
- Ciné-Club (dinsdag, 17x per year, 6 deelnemers)
- Conversatietafel (Engels, Spaans, Italiaans, Nederlands — 35–45x per year)
- Crea atelier / Diamond Painting
- Bingo, Verjaardagen
- Zumba, Country Line Dance, Stoelengym
- Any new activity a volunteer wants to propose

**Type 2: Praktische helpers** (support existing events/operations)
- Plazey festival (annual, confirmed from Notion meeting)
- Supporting activity days
- [ ] Onthaal / welcome desk?
- [ ] Kitchen / restaurant help?

**Key stat from yearly report:** 80% of activities are volunteer-run — use this prominently.
**Key message:** "Van bezoeker naar vrijwilliger" — you don't need to be a professional; if you have a passion, you can lead it here.

## Technical scope

- New Blade view: `resources/views/pages/vrijwilligers.blade.php`
- New routes: `nl.vrijwilligers` at `/vrijwilligers`, `fr.benevoles` at `/fr/benevoles`
- New translation keys: `pages.vrijwilligers_*` in `nl.json` and `fr.json`
- Contact form (if chosen): reuse existing Livewire patterns from `RegistrationForm`, or simple mailto link
- Nav: footer link + cross-link from Over ons (NOT primary nav — keep 5 items)
- Photos: confirm which images are available / whether new photos are needed

## Open questions (for client)

- [ ] Confirm volunteer roles — what can someone actually do when they sign up?
- [ ] Are there existing photos of volunteers specifically? Or use existing visitor photos?
- [ ] Should there be a contact form, or just a mailto link to the right address?
- [ ] Should the page be linked from the homepage (e.g. small "word vrijwilliger" card)?
