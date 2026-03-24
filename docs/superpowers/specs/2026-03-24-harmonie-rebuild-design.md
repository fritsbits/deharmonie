# Design Spec — De Harmonie Laravel Rebuild

**Date:** 2026-03-24
**Project:** De Harmonie — lokaal dienstencentrum en sociaal restaurant
**Organisation:** VZW Buurtwerk Noordwijk, Brussels

---

## Scope

Rebuild the active functionality of www.deharmonie.be in Laravel. Not a full migration — only what is actually used today.

**In scope:**
- Public website with activity overview (NL + FR)
- Activity detail pages (NL + FR)
- Print view for activities (A4)
- Registration form with email notifications
- Weekly menu page (Google Doc embed)
- Filament admin: activities CRUD, registration requests overview
- Dashboard with upcoming activities + open requests count

**Out of scope:**
- Client CRM
- Complex email automations
- Separate translation workflow system
- Team management module
- Category/interest linking (nice-to-have, deferred)
- SEO sitemap.xml (local-only project, no production domain)

---

## Tech Stack

| Layer | Choice |
|---|---|
| Framework | Laravel 11 |
| Admin panel | Filament 3 |
| Frontend interactivity | Livewire 3 |
| Styling | Tailwind CSS v4 |
| Database | MySQL (local) |
| Email | Mailtrap (SMTP) |
| Image handling | Spatie Media Library |
| Local dev | Laravel Herd |

---

## Architecture

```
Laravel 11
├── /admin                  Filament admin panel
│   ├── Dashboard           Upcoming activities + open requests
│   ├── Activiteiten        CRUD with NL/FR tabs, status, image upload
│   └── Deelnameverzoeken   Registrations table + one-click status toggle
├── /                       Public frontend (Blade + Livewire islands)
│   ├── Blade templates     All static pages
│   └── Livewire components Activity filter, registration form, lang switch
└── MySQL                   Database: harmonie
```

---

## Data Model

### `activiteiten`

| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| slug | string unique | URL-safe, NL-based. Same slug used for both NL and FR URLs. |
| titel_nl | string | |
| titel_fr | string | |
| beschrijving_nl | text nullable | |
| beschrijving_fr | text nullable | |
| notice_nl | text nullable | Cancellation/extra notice |
| notice_fr | text nullable | |
| datum | date | |
| startuur | time | |
| einduur | time nullable | |
| locatie | string | DB default "De Harmonie". Pre-filled in Filament form. |
| prijs | decimal(8,2) nullable | Null or 0.00 = free ("Gratis / Gratuit") |
| max_deelnemers | integer nullable | Null = unlimited |
| status | enum | concept, gepubliceerd, geannuleerd |
| timestamps | | created_at / updated_at |

Image stored via Spatie Media Library (single collection `afbeelding`).

### `deelnameverzoeken`

| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| activiteit_id | FK → activiteiten | |
| naam | string | |
| email | string | |
| telefoon | string nullable | |
| bericht | text nullable | |
| status | enum | te_contacteren, afgehandeld |
| timestamps | | created_at used as aanvraagmoment — no separate column |

---

## Routing

### Public (NL default, no prefix)

| Route | Controller | View |
|---|---|---|
| `/` | ActivityController@index | Homepage with activity list + month filter |
| `/activiteiten/{slug}` | ActivityController@show | Activity detail + registration form |
| `/activiteiten/{slug}/print` | ActivityController@print | A4 print layout |
| `/diensten` | PageController@diensten | Services (static) |
| `/weekmenu` | PageController@weekmenu | Google Doc embed |
| `/contact` | PageController@contact | Contact info |

### French (prefix `/fr`)

| Route | Maps to |
|---|---|
| `/fr/activites` | Activities in FR |
| `/fr/activites/{slug}` | Activity detail in FR (same NL slug in URL — acceptable) |
| `/fr/activites/{slug}/imprimer` | Print in FR |
| `/fr/services` | Services in FR |
| `/fr/menu-semaine` | Menu in FR |
| `/fr/contact` | Contact in FR |

**Slug strategy:** A single NL-based slug is used across both locales. `/fr/activites/yoga-voor-senioren` works — the slug is treated as an opaque identifier, not a translated phrase. This is a known trade-off accepted for simplicity.

### Admin

| Route | Notes |
|---|---|
| `/admin` | Filament dashboard |
| `/admin/login` | Auth |
| `/admin/activiteiten` | Activities resource |
| `/admin/deelnameverzoeken` | Registrations resource |

---

## Bilingual Implementation

- `SetLocale` middleware reads URL prefix (`/fr/` → `fr`, default → `nl`)
- Route model binding resolves by slug regardless of locale
- All UI strings in `lang/nl/*.php` and `lang/fr/*.php`
- Dates formatted via Carbon locale (`->locale('nl')->isoFormat(...)` or `->locale('fr')`)
- Language switch component in nav links to equivalent page in other locale
- Switching language while filling the registration form navigates to the FR/NL URL — form state is lost (acceptable, expected behaviour)

---

## Livewire Components

### `ActivityFilter`
- Month selector: previous / current / next month
- Default: current month
- Renders filtered list of published + cancelled activities (concept hidden)
- Empty state: "Geen activiteiten in [maand]" / "Pas d'activités en [mois]"
- No full page reload
- Year rollover handled correctly (December → January next year)

### `RegistrationForm`
- Fields: naam, email, telefoon (optional), bericht (optional)
- Honeypot field (spam prevention)
- Rate limiting: 5 submissions / minute / IP
- Capacity check: if `max_deelnemers` is set and reached, form is replaced with a "Volzet / Complet" message — no registration possible
- On submit: create `deelnameverzoek`, send two emails via Mailtrap

### `LanguageSwitch`
- Toggle between NL and FR version of current page

---

## Cancelled Activity — Public Frontend Behaviour

- **Activity list:** Cancelled activities remain visible with a "Geannuleerd / Annulé" badge. Not hidden.
- **Activity detail page:** Shows a prominent cancellation banner at the top using `notice_nl` / `notice_fr` if set, or a default cancellation message. Registration form is hidden for cancelled activities.
- **Print view:** Shows cancellation status prominently.

---

## Capacity Enforcement (`max_deelnemers`)

- The `Activiteit` model has a computed `isBeschikbaar()` method: returns true if `max_deelnemers` is null, or if the count of `te_contacteren` + `afgehandeld` registrations is below `max_deelnemers`.
- Registration form (Livewire): checks `isBeschikbaar()` before showing the form. Shows "Volzet / Complet" if full.
- No waitlist.
- Admin table shows a "X / Y" capacity column (registrations / max_deelnemers).

---

## Price Display

| `prijs` value | Displayed as |
|---|---|
| `null` | "Gratis / Gratuit" |
| `0.00` | "Gratis / Gratuit" |
| `> 0` | "€ 5,00" (locale-formatted) |

---

## Admin Panel (Filament 3)

### Dashboard widgets
- Upcoming activities (next 30 days) count
- Open registration requests (`te_contacteren`) count

### Activiteiten resource
- Table columns: titel_nl, datum, status badge, registrations count (X / max or X / ∞)
- Form: NL/FR tabs with all bilingual fields, status select, image upload
- `locatie` field pre-filled with "De Harmonie"
- Status transitions: concept → gepubliceerd → geannuleerd
- Bulk actions: publish, cancel

### Deelnameverzoeken resource
- Table columns: naam, email, activiteit, created_at, status
- Filter by: status, activiteit
- One-click toggle: te_contacteren → afgehandeld
- No delete (audit trail)

---

## Email Notifications (Mailtrap)

All mail via Mailtrap SMTP. Two mails triggered per registration:

1. **To `animatie@deharmonie.be`** — always sent in **NL**, regardless of the submitter's locale. Contains: activity name, date, time, submitter name, email, phone, message.
2. **To submitter** — sent in the **locale of the submission** (NL or FR). Contains: activity name, date, time, confirmation message.

---

## Frontend Design

- **Fonts:** Nunito Sans + Source Sans 3 (Google Fonts, matching original)
- **Colors:** Warm neutral palette from original site (extracted during implementation)
- **Assets:** Images sourced from www.deharmonie.be during seeding
- **Responsive:** Mobile-first, tested from 375px up
- **Print view:** Dedicated `print.blade.php` layout, A4-optimised CSS
- **Meta tags:** Basic `<title>` and `<meta description>` per page and per activity. Open Graph tags on activity detail pages. No sitemap (local-only project).

---

## Seeders

- `AdminUserSeeder` — creates one admin user (credentials in `.env`)
- `ActiviteitSeeder` — seeds upcoming activities from live site content (manual data entry, no Webflow/Airtable import)

---

## Email & Environment Config

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<mailtrap_user>
MAIL_PASSWORD=<mailtrap_pass>
MAIL_FROM_ADDRESS=noreply@deharmonie.be
MAIL_FROM_NAME="De Harmonie"

DB_DATABASE=harmonie
DB_USERNAME=root
DB_PASSWORD=
```

---

## What Is Not Built

- Deployment pipeline / staging environment
- Production server setup
- Real email delivery (Mailtrap only)
- Webflow/Airtable data import
- Client CRM
- Team management module
- SEO sitemap.xml
- Waitlist for full activities
