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

Single resource: `ActiviteitResource`. Two new fields on `Activiteit`: `soort` (`vast | speciaal`) and `categorie` (8 cases — the Resoo umbrella organisation's standard activity types). The admin index uses Filament 4's standard `Tables\Table` with native `Group::make('week_start')` to render a week-grouped layout, plus a single `ViewColumn` for the rich icon+title+badges+meta cell — staying inside Filament idioms instead of building a custom Livewire page. Two header buttons — `[+ Vaste activiteit]` and `[+ Speciaal moment]` — drive the create flow and pre-set `soort`. Reuse is handled by a `Kopieer naar...` row action, with bulk date generation as part of both the create and copy flows.

`ActiviteitTemplate` model, table, and resource are removed. The frontend overzicht page reduces from 4 themed cards to 3 highlight sections ("Beweeg mee", "Maak & leer mee", "Ontmoet & beleef mee"), each bundling a few of the 8 categorieën and showing 4–5 highlighted recurring activities + a "meer →" link. Section grouping is a presentation concern (lives in the blade), not a database concept.

## Components

### 1. Data model

**`Activiteit` table — add columns:**

- `soort` enum, NOT NULL, no default — `'vast'` or `'speciaal'`. Drives where the activity surfaces on the public site.
- `categorie` enum, NOT NULL, no default — one of 8 values (see Categorie enum below). Drives the agenda icon and the section grouping on the public overzicht page.

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

8 cases, the Resoo umbrella organisation's standard activity types. Each case implements `HasLabel` (NL via `getLabel()`, FR via `labelFr()`), and exposes `icon(): string` (SVG path) and `section(): string` (which of the three public-overzicht highlight sections this categorie belongs to: `'beweeg' | 'maak_leer' | 'ontmoet_beleef'`).

| Categorie          | Section          | NL label            | FR label              | Icon          |
|--------------------|------------------|---------------------|-----------------------|---------------|
| `sport_beweging`   | beweeg           | Sport & beweging    | Sport & mouvement     | bolt          |
| `creatief`         | maak_leer        | Creatief            | Créatif               | sparkles      |
| `bijleren`         | maak_leer        | Bijleren            | Apprendre             | brein         |
| `ontmoeting`       | ontmoet_beleef   | Ontmoeting          | Rencontre             | tekstballon   |
| `spelletjes`       | ontmoet_beleef   | Spelletjes          | Jeux                  | dobbelstenen  |
| `culinair`         | ontmoet_beleef   | Culinair            | Culinaire             | bestek        |
| `film_muziek`      | ontmoet_beleef   | Film & muziek       | Cinéma & musique      | muzieknoot    |
| `op_uitstap`       | ontmoet_beleef   | Op uitstap          | En sortie             | voetafdruk    |

The 3 sections (presentation only — not a separate enum or column):

| Section value     | NL heading              | FR heading                     |
|-------------------|-------------------------|--------------------------------|
| `beweeg`          | Beweeg mee              | Bougez avec nous               |
| `maak_leer`       | Maak & leer mee         | Créez & apprenez avec nous     |
| `ontmoet_beleef`  | Ontmoet & beleef mee    | Rencontrez & vivez avec nous   |

Section labels, taglines, photos, colors, and rotation/decoration metadata live inline in the overzicht blade (same pattern as the current `$themes` array). The blade looks up activities by section by calling `$activiteit->categorie->section()`.

Icon SVGs: 8 needed, all lifted from the existing 15-icon working set in the previous spec revision. 7 are reused as-is from `agenda.blade.php` (bolt, sparkles, chat-bubble = tekstballon, dice = dobbelstenen, food = bestek (cutlery), music = muzieknoot). 1 (brein for Bijleren) and 1 (voetafdruk for Op uitstap) come from Heroicons solid (light-bulb and map-pin respectively, as close-fits). The icon library lives in `App\Support\CategorieIcons` as a `match` returning SVG path strings, so it can be referenced from both the enum and the views.

### 2. Admin — `ActiviteitResource` index

Use Filament's standard `Tables\Table` with the native `Group` API to produce a week-grouped, day-ordered listing. No custom Livewire page, no custom blade view — staying within Filament idioms keeps bulk actions, row actions, filters, pagination, dark mode, accessibility, and Filament-version compatibility working out of the box.

**Header actions** (top-right of the page) — defined on `ListActiviteiten extends ListRecords` via `getHeaderActions()`:

- `[+ Vaste activiteit]` — primary `CreateAction` with `->url(fn () => static::getResource()::getUrl('create', ['soort' => 'vast']))`
- `[+ Speciaal moment]` — secondary `CreateAction` with `->url(fn () => static::getResource()::getUrl('create', ['soort' => 'speciaal']))`

**Default group** (set on the table, no group dropdown needed for the user — call `->groupsOnly()` to hide the group switcher):

```php
->defaultGroup(
    Group::make('week_start')
        ->getKeyFromRecordUsing(fn (Activiteit $a) => $a->datum->copy()->startOfWeek()->toDateString())
        ->getTitleFromRecordUsing(fn (Activiteit $a) => 'WEEK VAN ' . $a->datum->copy()->startOfWeek()->locale('nl')->isoFormat('D MMMM') . ' – ' . $a->datum->copy()->endOfWeek()->locale('nl')->isoFormat('D MMMM YYYY'))
        ->collapsible()
)
```

**Columns:**

- `ViewColumn::make('rich')` — custom blade view (`resources/views/filament/tables/columns/activiteit-rich-cell.blade.php`) renders: categorie icon + title + soort/status badges + meta line (date, time, location). One column doing the visual work; everything else stays as standard Filament columns or is absorbed into this cell. The view is a small, focused blade — orders of magnitude less code than a full Livewire page.

**Filters** (Filament `SelectFilter` instances on the table):

- Periode: `Filter::make('periode')` with a custom form — options "Deze week" (default), "Komende 4 weken", "Deze maand", "Alles vanaf vandaag", "Archief". Default selection is "Komende 4 weken" so the begeleider sees the planning horizon.
- Categorie: `SelectFilter::make('categorie')` with options from `Categorie::cases()`.
- Soort: `SelectFilter::make('soort')` with vast/speciaal options.
- Status: `SelectFilter::make('status')` with concept/gepubliceerd/geannuleerd options.

**Row actions** (Filament `Tables\Actions\ActionGroup` per row):

- `EditAction` — default
- `Action::make('kopieer')` — opens the kopieer-form (see Section 4)
- `Action::make('annuleer')` — sets status to geannuleerd

**Bulk actions** (Filament `BulkActionGroup` on the table):

- `BulkAction::make('publish')` — sets status to gepubliceerd
- `BulkAction::make('cancel')` — sets status to geannuleerd
- `BulkAction::make('bulk_edit')` — opens a form with optional fields (beschrijving NL/FR, locatie, prijs); only filled fields are applied. Handles the "tikfout in alle Zumba's" case.
- `DeleteBulkAction`

**Trade-off accepted:** the admin presentation is tabular within each week-group rather than the public agenda's day-card layout. The information architecture (week-grouped, chronological, icon + title + meta per row) still matches the public agenda — just rendered as Filament rows instead of cards. This is a deliberate "good enough WYSIWYG" choice in exchange for staying inside Filament idioms.

### 3. Admin — Create / Edit form

Single form, used by both create flows and edit. The `soort` field is hidden but pre-populated from the create button (or kept from the existing record on edit).

**Sections in form:**

- **Talen tabs** (NL / FR): titel, beschrijving, opmerking — unchanged from current
- **Categorie**: required dropdown, 8 options (Filament `Select::make()->options()`). Each option's label includes its icon's name in parentheses so the begeleider knows which visual will appear on the agenda.
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
// One row per distinct title within each section, picked from upcoming vaste activiteiten.
$vasteAanbod = Activiteit::query()
    ->where('soort', Soort::Vast)
    ->where('datum', '>=', today())
    ->where('status', ActiviteitStatus::Gepubliceerd)
    ->orderBy('datum')
    ->get()
    ->groupBy(fn ($a) => $a->categorie->section())
    ->map(fn ($acts) => $acts->unique('titel_nl')->values());

$bijzondereActiviteiten = Activiteit::query()
    ->where('soort', Soort::Speciaal)
    ->where('datum', '>=', today())
    ->where('status', ActiviteitStatus::Gepubliceerd)
    ->orderBy('datum')
    ->limit(2)
    ->get();
```

In `overzicht.blade.php`: the four hardcoded `$themes` arrays are reduced to **three** `$sections` arrays — Beweeg mee, Maak & leer mee, Ontmoet & beleef mee. Each keeps its decorative metadata (photo, tagline, rotation, color) and reads its list of activities from `$vasteAanbod[$section_id]` (where `$section_id` is `'beweeg'`, `'maak_leer'`, or `'ontmoet_beleef'`). Each section shows up to 5 highlighted recurring titles + a "en meer →" link to the agenda. The hardcoded template-ID lists disappear.

### 6. Frontend — `agenda.blade.php`

The ~150-line keyword-matching block (lines ~120–155) is replaced by:

```php
$icon = $activiteit->categorie->icon();
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
4. Backfill `categorie` from an inline title→categorie map. Two passes:
   - **Pass A — exact map for the 19 existing templates** (Zumba → `sport_beweging`, Bingo → `spelletjes`, Geheugenatelier → `bijleren`, Conversatietafel * → `ontmoeting`, etc.). All vaste activiteiten inherit the categorie of their template.
   - **Pass B — keyword match for speciale momenten.** Museum/expo/tentoon → `op_uitstap`; wandel/balade/marche → `op_uitstap`; festival/concert/musette → `film_muziek`; documentaire/film/theater/voorstelling → `film_muziek`; brunch/buffet/aperitief/koffie/confituur → `culinair`; feest/verjaardag/inhuldiging → `ontmoeting`; atelier/workshop/haken/naai/diamond → `creatief`; spel/jeu/scrabble/bingo → `spelletjes`; geheugen/digitaal/cursus → `bijleren`; conversatie/startbabbel → `ontmoeting`; zumba/dans/gym/pilates/yoga → `sport_beweging`. Anything that doesn't match falls back to `ontmoeting` (the most catch-all bucket for one-off events) and is flagged for manual review.
5. Make `soort` and `categorie` NOT NULL.
6. Drop FK and `template_id` column from `activiteiten`.
7. Drop `activiteit_templates` table.

Backfill mapping tables (template-title → categorie, and keyword → categorie) live inside the migration file as inline arrays; one-shot, never reused.

After the migration ships and runs in prod, the admin reviews any rows where the keyword fallback fired (logged during migration to `storage/logs/categorie-backfill.log` with each affected activiteit ID + title) and corrects them via the admin.

## Testing

Feature tests in `tests/Feature/`:

- **`ActiviteitCreateVastTest`** — POST to create a vaste activiteit with bulk-generation creates the expected N rows with correct `soort`, `categorie`, dates, slugs.
- **`ActiviteitCreateSpeciaalTest`** — creates one row, `soort = speciaal`, no bulk options shown.
- **`ActiviteitKopieerTest`** — copies an activity to specific dates and to "elke X t/m Y"; asserts new rows exist with correct fields, status = concept, foto re-attached.
- **`ActiviteitBulkEditTest`** — selects 3 Zumba's, updates description, asserts all 3 are updated and other Zumba's are not.
- **`OverzichtPaginaTest`** — seeds vaste activiteiten across all 8 categorieën + a few speciale, asserts the 3 sections group correctly via `Categorie::section()` and the bijzondere momenten section shows the speciale rows.
- **`AgendaIconTest`** — asserts `Categorie::SportBeweging->icon()` returns the bolt SVG, `Categorie::Spelletjes->icon()` returns the dobbelstenen SVG, etc., for all 8 categorieën; asserts the agenda blade renders the right icon for a known activity.
- **`CategorieSectionTest`** — asserts every `Categorie` case has a `section()` returning one of `'beweeg'`, `'maak_leer'`, or `'ontmoet_beleef'` (no orphans, no nulls).
- **`MigratieBackfillTest`** — seeds the pre-migration schema with the existing 19 templates and a sample of speciale activiteiten, runs the migration, asserts soort + categorie are correctly set per the title→categorie map, the templates table is gone, and the fallback log captured the speciale momenten that hit the catch-all.

The existing `ActiviteitenSeeder` is updated to set `soort` and `categorie` on every seeded row. The `ActiviteitTemplateSeeder` is deleted.

## Open questions

None — all decisions confirmed during brainstorming on 2026-04-23.
