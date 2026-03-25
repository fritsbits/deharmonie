# Recurring Activities — Design Spec

**Date:** 2026-03-25
**Status:** Draft

## Problem

The activity admin (low digital confidence — activity director or social worker) currently creates one `Activiteit` row per weekly session. With ~10 recurring series running 15–18 sessions each per semester, that's 150+ manual entries per semester. Analysis of the database confirms the pattern: every recurring series has identical metadata across all sessions — only the date differs.

Additionally, the same series was being recreated under slightly different names each new semester (e.g. "Zumba" → "NIEUW : Zumba" → "🤸🏻 Zumba 🤸🏻"), indicating the admin was starting from scratch each time rather than extending an existing series.

## Solution

**Activity templates + generated instances (Option B).**

Introduce an `ActiviteitTemplate` model that stores the static definition of a recurring series. Individual `Activiteit` rows are generated from the template and linked back to it via `template_id`. The public site and registration system are unchanged — sessions remain real rows in `activiteiten`.

## Data Model

### New table: `activiteit_templates`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `titel_nl` | string | |
| `titel_fr` | string | |
| `beschrijving_nl` | text, nullable | |
| `beschrijving_fr` | text, nullable | |
| `notice_nl` | text, nullable | |
| `notice_fr` | text, nullable | |
| `startuur` | time | |
| `einduur` | time, nullable | |
| `locatie` | string | default 'De Harmonie' |
| `prijs` | decimal(8,2), nullable | |
| `max_deelnemers` | integer, nullable | |
| `interesse` | string, nullable | stored as string, cast to `Interesse` enum in PHP — consistent with `activiteiten.interesse` |
| `dag_van_de_week` | tinyint | 0=Sun … 6=Sat |
| `reeks_start` | date | first session date |
| `reeks_einde` | date | last session date |
| `timestamps` | | |

Migration uses `$table->string('interesse')->nullable()` — no MySQL ENUM type.

### `ActiviteitTemplate` model

Must declare an explicit `$table = 'activiteit_templates'` (consistent with project convention to avoid Laravel auto-pluralisation issues with Dutch).

`$fillable` must include all columns listed above.

`$casts`: `'interesse' => Interesse::class`, `'dag_van_de_week' => 'integer'`, `'reeks_start' => 'date'`, `'reeks_einde' => 'date'`.

### Modified table: `activiteiten`

Add one nullable column:

| Column | Type | Notes |
|---|---|---|
| `template_id` | bigint, nullable, FK | → `activiteit_templates.id`, `onDelete('set null')` |

Add `template_id` to `Activiteit::$fillable`.

## Admin UX (Filament)

### New "Reeksen" resource in sidebar

A new Filament resource `ActiviteitTemplateResource` in `app/Filament/Resources/` — auto-discovered by `AdminPanelProvider` via `discoverResources()`, no registration step needed. Appears in sidebar as **"Reeksen"** (Series).

**Create flow:**
1. Admin fills in one form: title NL/FR, description NL/FR, optional notice NL/FR, start time, end time, location, price, max participants, category (interesse), day of week, series start date, series end date
2. On save (`afterCreate()` hook), the service generates all `Activiteit` sessions within the date range
3. Each generated session gets: slug = `{slug-of-titel_nl}-{YYYY-MM-DD}`, status = `concept`, and `template_id` pointing to the template
4. Admin sees a Filament notification: "X sessies aangemaakt voor [Titel]"

**Edit flow — propagation to future sessions:**

The edit page overrides `getSaveFormAction()` (or uses an `afterSave()` hook) to trigger a follow-up Filament `Action` with `requiresConfirmation()` placed alongside the Save button. The modal asks:
> "Wijzigingen toepassen op alle toekomstige sessies?"
> [Ja] [Nee — alleen de reeks opslaan]

"Future sessions eligible for update" = sessions where:
- `datum >= today`
- `status != 'geannuleerd'`
- no active registrations (`deelnameverzoeken` with status `te_contacteren` or `afgehandeld`)

Fields propagated: `titel_nl`, `titel_fr`, `beschrijving_nl`, `beschrijving_fr`, `notice_nl`, `notice_fr`, `startuur`, `einduur`, `locatie`, `interesse`.

**`max_deelnemers` propagation edge case:** if a session's active registration count already meets or exceeds the new `max_deelnemers` value, skip that session entirely — do not create an over-booked state. The implementer may log a warning but should not throw.

**`prijs` is not propagated** — price is set at generation time and treated as immutable per-session (confirmed: price never changes for individual sessions).

**Slugs are never updated** during propagation. A session's slug is set once at creation and never changed, preserving existing URLs and registration links.

**Extending a series:**
If the admin updates `reeks_einde` to a later date, new sessions are generated to fill the gap. The generation service checks `not exists` before inserting, so already-existing sessions are never duplicated.

**Deleting a template:**
`template_id` on linked sessions is set to null via `onDelete('set null')`. Sessions themselves are preserved.

### Existing "Activiteiten" resource — unchanged

Individual sessions remain editable as today. The admin can:
- Cancel a single session (status → `geannuleerd`) without affecting the series
- Change the date of a single session (one-off rescheduling)
- Add a one-time notice to a single session

The table view gains one optional `TextColumn` for `template.titel_nl` labelled "Reeks", showing the series name if `template_id` is set.

## Generation Logic (Service Class)

`App\Services\ActiviteitTemplateService::generateSessions(ActiviteitTemplate $template): int`

```
count = 0
foreach date in [template.reeks_start .. template.reeks_einde]:
    if date.dayOfWeek == template.dag_van_de_week:
        if not Activiteit::where('template_id', template->id)->where('datum', date)->exists():
            slug = Str::slug(template->titel_nl) . '-' . date->format('Y-m-d')
            // ensure slug uniqueness by appending -2, -3 etc if collision
            Activiteit::create([...template fields..., 'datum' => date, 'slug' => slug, 'template_id' => template->id, 'status' => ActiviteitStatus::Concept])
            count++
return count
```

## Open Questions (validate with admin before implementation)

1. **Should generated sessions auto-publish, or stay as concept?** Recommendation: stay as `concept` — admin bulk-publishes when ready using the existing bulk action.
2. **What happens if the admin changes `dag_van_de_week` mid-season?** Recommendation: generate new sessions from today for the new day; leave existing sessions untouched.

## Out of Scope

- Bi-weekly or monthly recurrence (all current recurring activities are strictly weekly)
- Attendee notifications when a series changes
- Public-facing "series" page grouping all sessions of a recurring activity

## Implementation Notes

- `php artisan make:model ActiviteitTemplate --migration` then `php artisan make:filament-resource ActiviteitTemplate` (separate commands — `--resource` generates a Laravel API controller, not a Filament resource)
- Service class: `app/Services/ActiviteitTemplateService.php`
- `AdminPanelProvider` needs no changes — resource auto-discovery already covers `app/Filament/Resources/`
- Run `vendor/bin/pint --dirty` after all PHP changes
- Feature tests must cover: session generation, propagation of edits skipping past/cancelled/registered sessions, `max_deelnemers` over-booking guard, slug uniqueness, set-null cascade on template deletion
