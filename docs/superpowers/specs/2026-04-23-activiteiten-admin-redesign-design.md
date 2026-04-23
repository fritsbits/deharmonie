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
- `subcategorie` enum, NOT NULL, no default — one of 15 values (see Subcategorie enum below). Drives the agenda icon. Hoofdcategorie (theme grouping on the overzicht page) is derived from subcategorie via `Subcategorie::hoofd()` — not stored separately.

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

**New enum: `App\Enums\Hoofdcategorie`**

```php
case Beweeg = 'beweeg';
case Maak = 'maak';
case Praat = 'praat';
case Vier = 'vier';
```

Implements `HasLabel` and `HasColor`. Defines the four theme groupings on the public overzicht page. Stored nowhere; derived from `Subcategorie::hoofd()`.

| Hoofdcategorie | NL label    | FR label             | Color         |
|----------------|-------------|----------------------|---------------|
| Beweeg         | Beweeg mee  | Bougez avec nous     | brand-orange  |
| Maak           | Maak iets   | Créez ensemble       | brand-green   |
| Praat          | Praat & leer| Parlez & apprenez    | brand-blue    |
| Vier           | Vier mee    | Fêtez avec nous      | warm-tan (#d4956a) |

**New enum: `App\Enums\Subcategorie`**

15 cases, grouped by hoofdcategorie (Beweeg 3 + Maak 4 + Praat 4 + Vier 4). Each case implements `HasLabel` (NL+FR via `getLabel()` and `labelFr()`), and exposes `hoofd(): Hoofdcategorie` and `icon(): string` (SVG path).

| Subcategorie         | Hoofd  | NL label              | FR label                  | Icon            |
|----------------------|--------|-----------------------|---------------------------|-----------------|
| `dans`               | Beweeg | Dans                  | Danse                     | dansend figuur  |
| `gym_fitness`        | Beweeg | Gym & fitness         | Gym & fitness             | bolt            |
| `wandeling`          | Beweeg | Wandeling             | Promenade                 | voetafdruk      |
| `handwerk`           | Maak   | Handwerk              | Travaux manuels           | naald & draad   |
| `creatief_atelier`   | Maak   | Creatief atelier      | Atelier créatif           | sparkles        |
| `koken`              | Maak   | Koken & confituur     | Cuisine & confiture       | kookpot         |
| `digitaal_atelier`   | Maak   | Digitaal atelier      | Atelier numérique         | laptop          |
| `conversatietafel`   | Praat  | Conversatietafel      | Table de conversation     | tekstballon     |
| `geheugen_brein`     | Praat  | Geheugen & brein      | Mémoire & cerveau         | brein           |
| `info_spreekuur`     | Praat  | Info & spreekuur      | Info & permanence         | info-cirkel     |
| `cultuur_museum`     | Praat  | Cultuur & museum      | Culture & musée           | kaderlijst      |
| `spelletjes`         | Vier   | Spelletjes            | Jeux                      | dobbelstenen    |
| `feest`              | Vier   | Feest & verjaardag    | Fête & anniversaire       | ster            |
| `muziek_concert`     | Vier   | Muziek & concert      | Musique & concert         | muzieknoot      |
| `eten_drinken`       | Vier   | Eten & drinken        | Repas & boissons          | bestek          |

Icon SVGs: 9 are reused from the existing `agenda.blade.php` keyword set (chat, music, star, bolt, food, game, info, sparkles, kaderlijst). 6 new icons need to be added: dans, voetafdruk, naald, kookpot, laptop, brein. All lifted from Heroicons solid set where available; for icons not in Heroicons (naald & draad), use a close-fit alternative or a custom SVG. The icon library lives in `App\Support\SubcategorieIcons` as a `match` returning SVG path strings, so it can be referenced from both the enum and the views.

### 2. Admin — `ActiviteitResource` index

Replace the default Filament table with a custom list page rendered as a Livewire component. The page lives at `App\Filament\Resources\ActiviteitResource\Pages\ListActiviteiten` and uses Filament's `Page` chrome (header, breadcrumbs, actions) but renders a custom Blade view in the content slot.

**Header actions** (top-right of the page):

- `[+ Vaste activiteit]` — primary button, opens create form with `soort = vast` pre-set
- `[+ Speciaal moment]` — secondary button, opens create form with `soort = speciaal` pre-set

**Filters** (above the list):

- Periode: deze week (default) / volgende 4 weken / deze maand / alles vanaf vandaag / archief
- Hoofdcategorie: alle / Beweeg / Maak / Praat / Vier (filtering on hoofd is done by `whereIn('subcategorie', $hoofd->subs())`)
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

- Icon comes from `subcategorie->icon()`, colored from `subcategorie->hoofd()->color()` (so the four hoofdcategorieën each get their consistent brand color across all their subs).
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
- **Subcategorie**: required dropdown, 15 options grouped by hoofdcategorie (Filament `Select::make()->options()` with grouped array). Each option shows its own icon as a prefix so the begeleider sees the visual that will appear on the agenda.
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

Always copies title (NL+FR), subcategorie, soort, max_deelnemers. Foto is copied by re-attaching the same media (Spatie Media Library copy). Unchecked fields revert to defaults (or empty for opmerking).

Status of all copies defaults to `concept` (so the user reviews before publishing).

### 5. Frontend — `ActivityController@index` (`activiteiten/overzicht`)

**Before:**
```php
$reeksen = ActiviteitTemplate::orderBy(...)->get()->keyBy('id');
$bijzondereActiviteiten = Activiteit::whereNull('template_id')->...
```

**After:**
```php
// One row per distinct title within each hoofdcategorie, picked from upcoming vaste activiteiten.
$vasteAanbod = Activiteit::query()
    ->where('soort', Soort::Vast)
    ->where('datum', '>=', today())
    ->where('status', ActiviteitStatus::Gepubliceerd)
    ->orderBy('datum')
    ->get()
    ->groupBy(fn ($a) => $a->subcategorie->hoofd()->value)
    ->map(fn ($acts) => $acts->unique('titel_nl')->values());

$bijzondereActiviteiten = Activiteit::query()
    ->where('soort', Soort::Speciaal)
    ->where('datum', '>=', today())
    ->where('status', ActiviteitStatus::Gepubliceerd)
    ->orderBy('datum')
    ->limit(2)
    ->get();
```

In `overzicht.blade.php`: the four hardcoded `$themes` arrays keep their decorative metadata (photo, tagline, rotation, color) but read their list of activities from `$vasteAanbod[$hoofdcategorie->value]` instead of `$reeksen->only($theme['ids'])`. The hardcoded ID lists disappear.

### 6. Frontend — `agenda.blade.php`

The ~150-line keyword-matching block (lines ~120–155) is replaced by:

```php
$icon = $activiteit->subcategorie->icon();
$iconColor = $activiteit->subcategorie->hoofd()->color();
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
2. Add `subcategorie` enum column, nullable (temporary).
3. Backfill `soort`:
   - `template_id IS NULL` → `'speciaal'`
   - `template_id IS NOT NULL` → `'vast'`
4. Backfill `subcategorie` from an inline title→subcategorie map. Two passes:
   - **Pass A — exact map for the 19 existing templates** (Zumba → `dans`, Bingo → `spelletjes`, Geheugenatelier → `geheugen_brein`, etc.). All vaste activiteiten inherit the subcategorie of their template.
   - **Pass B — keyword match for speciale momenten.** Match the same keyword sets the agenda blade currently uses (museum/expo → `cultuur_museum`, wandeling/balade → `wandeling`, festival/concert → `muziek_concert`, etc.). Anything that doesn't match falls back to `cultuur_museum` (the most catch-all bucket for one-off events) and is flagged for manual review.
5. Make `soort` and `subcategorie` NOT NULL.
6. Drop FK and `template_id` column from `activiteiten`.
7. Drop `activiteit_templates` table.

Backfill mapping tables (template-title → subcategorie, and keyword → subcategorie) live inside the migration file as inline arrays; one-shot, never reused.

After the migration ships and runs in prod, the admin reviews any rows where the keyword fallback fired (logged during migration to `storage/logs/categorie-backfill.log` with each affected activiteit ID + title) and corrects them via the admin.

## Testing

Feature tests in `tests/Feature/`:

- **`ActiviteitCreateVastTest`** — POST to create a vaste activiteit with bulk-generation creates the expected N rows with correct `soort`, `categorie`, dates, slugs.
- **`ActiviteitCreateSpeciaalTest`** — creates one row, `soort = speciaal`, no bulk options shown.
- **`ActiviteitKopieerTest`** — copies an activity to specific dates and to "elke X t/m Y"; asserts new rows exist with correct fields, status = concept, foto re-attached.
- **`ActiviteitBulkEditTest`** — selects 3 Zumba's, updates description, asserts all 3 are updated and other Zumba's are not.
- **`OverzichtPaginaTest`** — seeds vaste activiteiten across all 4 hoofdcategorieën + a few speciale, asserts the 4 thema-kaarten group correctly via `subcategorie->hoofd()` and the bijzondere momenten section shows the speciale rows.
- **`AgendaIconTest`** — asserts `Subcategorie::dans->icon()` returns the dans SVG, `Subcategorie::bingo... → spelletjes` returns the dobbelstenen SVG, etc., for all 15 subcategorieën; asserts the agenda blade renders the right icon for a known activity.
- **`HoofdcategorieDerivationTest`** — asserts every `Subcategorie` case has a `hoofd()` that returns one of the four `Hoofdcategorie` cases (no orphans, no nulls).
- **`MigratieBackfillTest`** — seeds the pre-migration schema with the existing 19 templates and a sample of speciale activiteiten, runs the migration, asserts soort + subcategorie are correctly set per the title→subcategorie map, the templates table is gone, and the fallback log captured the speciale momenten that hit the catch-all.

The existing `ActiviteitenSeeder` is updated to set `soort` and `categorie` on every seeded row. The `ActiviteitTemplateSeeder` is deleted.

## Open questions

None — all decisions confirmed during brainstorming on 2026-04-23.
