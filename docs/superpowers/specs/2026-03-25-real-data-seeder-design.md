# Real Activity Data & Series Templates — Design Spec

**Date:** 2026-03-25
**Status:** Draft

## Problem

The project already reads activity data from `database/seeders/data/activities.csv` (662 real rows, identical to the Webflow export). However:

1. No `ActiviteitTemplate` records exist — the new Reeksen feature has no starting data for the admin to work with.
2. Running `migrate:fresh --seed` gives the admin an empty Reeksen list despite 15 identifiable recurring series being present in the historical data.

## Goal

Seed 15 `ActiviteitTemplate` records derived from the recurring series identified in the CSV, so the admin can open the Reeksen tab, adjust the date range for the current semester, and generate sessions — without any manual data entry.

## Out of Scope

- Linking existing `activiteiten` rows to templates via `template_id` (historical activities stay unlinked)
- Generating sessions as part of the seeder (admin does this via the Filament UI)
- Price field normalization in `ActiviteitSeeder` (existing `(float)` cast is acceptable)
- Importing activity images (`Imgname` column ignored)

## New File: `ActiviteitTemplateSeeder`

`database/seeders/ActiviteitTemplateSeeder.php`

### Behaviour

1. Disable FK checks, truncate `activiteit_templates`, re-enable FK checks
2. Insert all 15 templates via `ActiviteitTemplate::create()` — no `generateSessions()` call
3. `reeks_start` = `today()`, `reeks_einde` = `today()->addMonths(3)` — a working starting point the admin adjusts before generating sessions
4. Report count via `$this->command->info()`

### Template Data

Canonical names strip emojis and "NIEUW:"/"Copy" noise from the source. French titles taken from the most-recent occurrence of each series in the CSV.

| # | titel_nl | titel_fr | dag_van_de_week | startuur | einduur | locatie | prijs | interesse |
|---|---|---|---|---|---|---|---|---|
| 1 | Conversatietafel Spaans | Table de conversation Espagnole | 4 (Thu) | 10:00 | 12:00 | De Harmonie | null | Activiteiten |
| 2 | Conversatietafel Engels | Table de Conversation Anglais | 2 (Tue) | 10:30 | null | De Harmonie | null | Activiteiten |
| 3 | Conversatietafel Italiaans | Table de Conversation Italien | 1 (Mon) | 11:30 | 12:30 | De Harmonie | null | Activiteiten |
| 4 | Nederlandse conversatietafel | Table de Conversation Néerlandais | 5 (Fri) | 10:30 | 11:30 | De Harmonie | null | Activiteiten |
| 5 | Country Line Dance | Country Dance en Ligne | 4 (Thu) | 14:00 | 16:00 | De Harmonie | 2.00 | Activiteiten |
| 6 | Geheugenatelier | Atelier de Mémoire | 1 (Mon) | 13:30 | 15:15 | De Harmonie | 1.00 | Activiteiten |
| 7 | Stoel-gym met Nicole | Gym sur chaise avec Nicole | 1 (Mon) | 11:00 | null | De Harmonie | null | Activiteiten |
| 8 | Digitale workshop | Atelier Numérique | 3 (Wed) | 14:00 | 16:00 | De Harmonie | null | Activiteiten |
| 9 | Bingo | Bingo | 3 (Wed) | 13:30 | 16:00 | De Harmonie | 1.00 | Activiteiten |
| 10 | Creativiteit workshop | Atelier de Créativité | 1 (Mon) | 14:00 | 16:00 | De Harmonie | null | Activiteiten |
| 11 | Zumba | Zumba | 5 (Fri) | 14:00 | 15:00 | De Harmonie | 1.00 | Activiteiten |
| 12 | Diamond Painting met Nadia | Atelier de Diamond Painting avec Nadia | 5 (Fri) | 14:00 | null | De Harmonie | null | Activiteiten |
| 13 | Naaiworkshop | Atelier de Couture | 3 (Wed) | 13:30 | 16:00 | De Harmonie | 1.00 | Activiteiten |
| 14 | Boodschappendienst | Service de Courses | 1 (Mon) | 14:00 | null | De Harmonie | 2.50 | Diensten |
| 15 | Pilates & Fitness | Pilates & Fitness | 5 (Fri) | 11:00 | null | Pôle Nord | 1.00 | Activiteiten |

`dag_van_de_week` integer mapping: 0=Sun, 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat — consistent with `DagVanDeWeek` enum.

## Modified File: `DatabaseSeeder`

Add `ActiviteitTemplateSeeder::class` call **before** `ActiviteitSeeder::class`.

## Testing

- Feature test: run seeder, assert `ActiviteitTemplate::count() === 15`
- Assert one specific template has correct `dag_van_de_week`, `startuur`, `interesse`
- Assert `Activiteit::whereNotNull('template_id')->count() === 0` (no activities linked)
