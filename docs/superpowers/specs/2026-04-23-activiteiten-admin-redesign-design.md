# Activiteiten admin redesign

## Problem

The current admin splits activities into two resources — `ActiviteitResource` (individual dated activities) and `ActiviteitTemplateResource` (recurring series, surfaced as "Terugkerende activiteiten" under Instellingen). User testing surfaces three problems for activity directors and social workers:

1. **The two-resource model is confusing.** Editing a template doesn't propagate to existing activities, so the "centralized series" mental model that "Reeksen" implies is false. The template is in practice only a generation shortcut.
2. **The "Zumba 2" trap.** Users wanting more Zumba sessions next season instinctively create a second template, leading to duplicated series with diverging metadata.
3. **The admin layout doesn't match the public site.** The agenda on the site is week-grouped by day; the admin shows a flat sortable table. Users have to mentally translate between the two structures.

Two related issues:

4. **Agenda icons are derived from title keyword matching.** Brittle, undocumented, and effectively a hand-rolled heuristic. No deterministic source of truth for "what kind of activity is this".
5. **The four themes on `activiteiten/overzicht` ("Beweeg mee", "Maak iets", "Praat & leer", "Vier mee") are populated by hardcoded template ID lists.** Adding a new recurring activity requires editing the blade template.

## Goal

Reframe the admin around a single "Activiteiten" resource whose layout mirrors the public agenda, with two clear creation flows (vast vs. speciaal) and category-driven icons. Eliminate the template concept.

## Non-goals

- Changing the public site layout. The overzicht page (theme cards + bijzondere momenten) and the agenda page keep their current structure; only the data sources change.
- Multi-admin / roles. Single admin remains.
- Replacing Filament. Stay within the Filament 4 panel.
- Backwards compatibility for `template_id`. Data migrates once; the column and table go.
- Deelnameverzoeken (registrations). Already hidden, out of scope here.

## Approach

Single resource: `ActiviteitResource`. Two new fields on `Activiteit`: `soort` (`vast | speciaal`) and `categorie` (`beweeg | maak | praat | vier`). The admin index becomes a custom Livewire page that renders the same week-grouped, day-sectioned layout as the public agenda. Two header buttons — `[+ Vaste activiteit]` and `[+ Speciaal moment]` — drive the create flow and pre-set `soort`. Reuse is handled by a `Kopieer naar...` action on existing activities, with bulk date generation as part of both the create and copy flows.

`ActiviteitTemplate` model, table, and resource are removed. The frontend overzicht page derives its theme cards by querying distinct `Activiteit` titles where `soort = vast` and grouping by `categorie`, instead of reading from templates by hardcoded IDs.

## Components

### 1. Data model

**`Activiteit` table — add columns:**

- `soort` enum, NOT NULL, no default — `'vast'` or `'speciaal'`. Drives where the activity surfaces on the public site.
- `categorie` enum, NOT NULL, no default — `'beweeg' | 'maak' | 'praat' | 'vier'`. Drives icon, color, and theme grouping. Always required (no fallback): forces the team to make a deliberate categorization.

**`Activiteit` table — drop columns:**

- `template_id` (after backfill, see Migration).

**`Activiteit` table — keep as-is:**

- `interesse` enum stays untouched. It is unrelated to the four themes and is consumed by the subscription flow elsewhere on the site (out of scope).

**Drop entirely:**

- `App\Models\ActiviteitTemplate`
- `activiteit_templates` table
- `App\Filament\Resources\ActiviteitTemplateResource` (and its Pages directory)
- `App\Enums\DagVanDeWeek` (only used by the template resource)

**New enum: `App\Enums\Soort`**

```php
case Vast = 'vast';
case Speciaal = 'speciaal';
```

Implements `HasLabel` for Filament. Labels: "Vast" / "Speciaal".

**New enum: `App\Enums\Categorie`**

```php
case Beweeg = 'beweeg';
case Maak = 'maak';
case Praat = 'praat';
case Vier = 'vier';
```

Implements `HasLabel`, `HasColor`, and a custom `icon()` method returning the SVG path string for that category. Labels (NL/FR), colors (matching `--color-brand-*` tokens), and icons are defined once in this enum and consumed by both admin and public views.

| Categorie | NL label    | FR label             | Color        | Icon (heroicon-style)   |
|-----------|-------------|----------------------|--------------|-------------------------|
| Beweeg    | Beweeg mee  | Bougez avec nous     | brand-orange | bolt (lightning)        |
| Maak      | Maak iets   | Créez ensemble       | brand-green  | sparkles                |
| Praat     | Praat & leer| Parlez & apprenez    | brand-blue   | chat-bubble             |
| Vier      | Vier mee    | Fêtez avec nous      | warm-tan     | star                    |

(Exact icon SVGs lifted from the existing `agenda.blade.php` set so the visual stays continuous.)

### 2. Admin — `ActiviteitResource` index

Replace the default Filament table with a custom list page rendered as a Livewire component. The page lives at `App\Filament\Resources\ActiviteitResource\Pages\ListActiviteiten` and uses Filament's `Page` chrome (header, breadcrumbs, actions) but renders a custom Blade view in the content slot.

**Header actions** (top-right of the page):

- `[+ Vaste activiteit]` — primary button, opens create form with `soort = vast` pre-set
- `[+ Speciaal moment]` — secondary button, opens create form with `soort = speciaal` pre-set

**Filters** (above the list):

- Periode: deze week (default) / volgende 4 weken / deze maand / alles vanaf vandaag / archief
- Categorie: alle / Beweeg / Maak / Praat / Vier
- Soort: alle / vast / speciaal
- Status: alle / concept / gepubliceerd / geannuleerd

**List body:** week-grouped, day-sectioned, mirroring `agenda.blade.php`:

```
─── WEEK VAN 27 APRIL – 3 MEI ───

MAANDAG     [icon] Creativiteit workshop      14:00 · De Harmonie
            [icon] Conversatietafel Italiaans 14:30 · De Harmonie

DINSDAG     [icon] Zumba                      14:00 · De Harmonie
            [geannuleerd] [icon] Uitstap museum 13:00 · Brussel ⭐speciaal

WOENSDAG    [icon] Bingo                      14:00 · De Harmonie
```

- Icon comes from `categorie->icon()`, colored from `categorie->color()`.
- Cancelled activities: muted, with `[geannuleerd]` badge.
- Speciale momenten: small `⭐ speciaal` badge to distinguish at a glance.
- Concept activities: greyed background, `[concept]` badge.
- Click anywhere on a row → navigates to edit page.
- Each row has a row-level menu (kebab): Bewerken / Kopieer naar... / Annuleren / Verwijderen.

**Bulk actions** (visible when ≥1 row selected via checkbox):

- Publiceer
- Annuleer
- Verwijder
- Bewerk gemeenschappelijke velden (small bulk-edit form for title/description/price — handles the "tikfout in alle Zumba's" case)

### 3. Admin — Create / Edit form

Single form, used by both create flows and edit. The `soort` field is hidden but pre-populated from the create button (or kept from the existing record on edit).

**Sections in form:**

- **Talen tabs** (NL / FR): titel, beschrijving, opmerking — unchanged from current
- **Categorie**: required dropdown, 4 options
- **Wanneer**:
  - Datum (single date picker)
  - Startuur, Einduur
  - Locatie (default "De Harmonie")
- **Bulk-generatie** (only visible when `soort = vast` AND on create, not edit):
  - Toggle: "Plan automatisch in: elke [dropdown: dag-van-de-week, default = same as datum] t/m [datum]"
  - Live preview: "Hiermee maak je N sessies aan."
- **Praktisch**: prijs, max_deelnemers
- **Status**: concept / gepubliceerd / geannuleerd
- **Foto**: SpatieMediaLibrary upload

Submit button label is dynamic: `[Maak speciaal moment]`, `[Maak activiteit]`, or `[Maak N sessies]` depending on flow.

### 4. Admin — Kopieer-actie

Available on every activity row (in the row menu) and on the edit page (header action).

**Form:**

```
Naar welke datums?
  ○ Specifieke datums    [+ datum toevoegen]
  ● Wekelijks            elke [dag-van-de-week] t/m [datum]

Wat overnemen?
  ☑ Beschrijving  ☑ Opmerking  ☑ Tijd  ☑ Locatie  ☑ Prijs  ☑ Foto

Soort blijft: vast / speciaal (gelijk aan origineel)
Categorie blijft: <origineel>
```

Always copies title (NL+FR), categorie, soort, max_deelnemers. Foto is copied by re-attaching the same media (Spatie Media Library copy). Unchecked fields revert to defaults (or empty for opmerking).

Status of all copies defaults to `concept` (so the user reviews before publishing).

### 5. Frontend — `ActivityController@index` (`activiteiten/overzicht`)

**Before:**
```php
$reeksen = ActiviteitTemplate::orderBy(...)->get()->keyBy('id');
$bijzondereActiviteiten = Activiteit::whereNull('template_id')->...
```

**After:**
```php
// One row per distinct title within each category, picked from upcoming vaste activiteiten.
$vasteAanbod = Activiteit::query()
    ->where('soort', Soort::Vast)
    ->where('datum', '>=', today())
    ->where('status', ActiviteitStatus::Gepubliceerd)
    ->orderBy('datum')
    ->get()
    ->groupBy(fn ($a) => $a->categorie->value)
    ->map(fn ($acts) => $acts->unique('titel_nl')->values());

$bijzondereActiviteiten = Activiteit::query()
    ->where('soort', Soort::Speciaal)
    ->where('datum', '>=', today())
    ->where('status', ActiviteitStatus::Gepubliceerd)
    ->orderBy('datum')
    ->limit(2)
    ->get();
```

In `overzicht.blade.php`: the four hardcoded `$themes` arrays keep their decorative metadata (photo, tagline, rotation, color) but read their list of activities from `$vasteAanbod[$categorie->value]` instead of `$reeksen->only($theme['ids'])`. The hardcoded ID lists disappear.

### 6. Frontend — `agenda.blade.php`

The ~150-line keyword-matching block (lines ~120–155) is replaced by:

```php
$icon = $activiteit->categorie->icon();
$iconColor = $activiteit->categorie->color();
```

Same visual output, deterministic source.

### 7. Frontend — homepage and other views

`activiteiten/index.blade.php` and `livewire/activity-filter.blade.php` continue to query `Activiteit` directly with no template join. Where they currently render activities, they get the same icon/color from the new enum.

## Behaviour

- Creating a vaste activiteit with bulk-generation creates N `Activiteit` rows in one transaction. Each gets its own slug (existing `generateUniqueSlug` handles dedup with numeric suffix).
- Editing an activity edits only that one date. (No propagation, same as today — but now this is the only model, so the mental model is consistent.)
- Kopieer creates new `Activiteit` rows with `status = concept`. The user must publish them.
- Bulk-edit "gemeenschappelijke velden" updates the selected rows in one transaction.
- Annuleren (single or bulk) sets `status = geannuleerd`; the row stays visible in the agenda with a `[geannuleerd]` badge — both in admin and on the public site.
- Filters in the admin list combine with AND. Default load: `periode = deze week`, alle categorieën, alle soorten, alle statussen.
- The 4 thema-kaarten on the public overzicht show the unique titles of upcoming `vast` activities per categorie. If a categorie has no upcoming activities, its card shows a "binnenkort meer" empty state (one-line tagline + the categorie's photo, no list).

## Migration

Single migration file `XXXX_XX_XX_activiteiten_soort_and_categorie.php` performs in order:

1. Add `soort` enum column, nullable (temporary).
2. Add `categorie` enum column, nullable (temporary).
3. Backfill `soort`:
   - `template_id IS NULL` → `'speciaal'`
   - `template_id IS NOT NULL` → `'vast'`
4. Backfill `categorie` from a hand-mapped table of current template titles → categorie. The 19 existing templates are explicitly mapped; speciale momenten get a best-effort default of `praat` (most-common bucket) and are flagged for manual admin review afterwards.
5. Make `soort` and `categorie` NOT NULL.
6. Drop FK and `template_id` column from `activiteiten`.
7. Drop `activiteit_templates` table.

Backfill mapping table (NL titles → categorie) lives inside the migration file as an inline array; it's a one-shot.

After the migration ships and runs in prod, the team is asked once to review the speciaal-flagged rows and correct any miscategorized one-offs via the admin.

## Testing

Feature tests in `tests/Feature/`:

- **`ActiviteitCreateVastTest`** — POST to create a vaste activiteit with bulk-generation creates the expected N rows with correct `soort`, `categorie`, dates, slugs.
- **`ActiviteitCreateSpeciaalTest`** — creates one row, `soort = speciaal`, no bulk options shown.
- **`ActiviteitKopieerTest`** — copies an activity to specific dates and to "elke X t/m Y"; asserts new rows exist with correct fields, status = concept, foto re-attached.
- **`ActiviteitBulkEditTest`** — selects 3 Zumba's, updates description, asserts all 3 are updated and other Zumba's are not.
- **`OverzichtPaginaTest`** — seeds vaste + speciale activiteiten, asserts the 4 thema-kaarten group correctly by categorie and the bijzondere momenten section shows speciale rows.
- **`AgendaIconTest`** — asserts `categorie->icon()` returns the expected SVG path for each categorie; asserts the agenda blade renders the right icon for a known activity.
- **`MigratieBackfillTest`** — seeds the pre-migration schema with templates and activiteiten, runs the migration, asserts soort/categorie are correctly set and the templates table is gone.

The existing `ActiviteitenSeeder` is updated to set `soort` and `categorie` on every seeded row. The `ActiviteitTemplateSeeder` is deleted.

## Open questions

None — all decisions confirmed during brainstorming on 2026-04-23.
