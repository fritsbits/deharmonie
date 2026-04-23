# Teamcategorie admin

## Problem

The `TeamCategorie` model holds the 10 categories that `TeamLid` rows are assigned to (e.g., "Onthaal & Animatie", "Keuken – Chefs & Instructeurs"). On the public team page these categories are shown in a fixed order (`volgorde`), and on the `TeamLid` form in the Filament admin they appear as a dropdown.

There is no admin UI to manage them. To add, rename, or reorder a category today, the team would need a developer to edit the seeder and re-run it (or write SQL). This blocks the team from independently maintaining their organizational structure.

## Goal

Add a Filament resource that exposes the existing `team_categorieen` table so the team can add, rename, reorder, and delete categories themselves.

## Non-goals

- Hierarchy / parent-child categories. The current flat list stays flat.
- Migrating or restructuring the 10 existing rows. They are already seeded; the new resource just exposes them.
- Touching `TeamLidResource`. Its existing dropdown reads from `TeamCategorie::orderBy('volgorde')` and will pick up changes automatically.
- Multilingual UI for the admin itself. The admin remains in NL; the form lets the user enter both NL and FR values.

## Approach

Single Filament resource `TeamCategorieResource` registered under the existing `Instellingen` navigation group (alongside `ActiviteitTemplateResource`). Order is managed by drag-and-drop using Filament's native `reorderable('volgorde')` — the `volgorde` field is not exposed on the form. Delete is blocked when the category still has `team_leden` attached, to prevent accidental cascade-deletion of staff.

## Components

### 1. Resource

`App\Filament\Resources\TeamCategorieResource` — standard Filament 4 resource backed by the existing `TeamCategorie` model.

```php
protected static ?string $model = TeamCategorie::class;
protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';
protected static ?string $navigationLabel = 'Teamcategorieën';
protected static ?string $modelLabel = 'Teamcategorie';
protected static ?string $pluralModelLabel = 'Teamcategorieën';
protected static ?string $slug = 'teamcategorieen';
protected static \UnitEnum|string|null $navigationGroup = 'Instellingen';
protected static ?int $navigationSort = 2;
```

`navigationSort = 2` places it after "Terugkerende activiteiten" (sort 1) within Instellingen.

### 2. Form

Two fields — `volgorde` is intentionally absent (drag-and-drop handles ordering).

- `TextInput::make('naam_nl')` — required, label "Naam (NL)"
- `TextInput::make('naam_fr')` — required, label "Nom (FR)"

### 3. Table

```php
->columns([
    TextColumn::make('naam_nl')->label('Naam (NL)')->searchable(),
    TextColumn::make('naam_fr')->label('Nom (FR)')->searchable(),
    TextColumn::make('leden_count')->label('Teamleden')->counts('leden')->badge(),
])
->defaultSort('volgorde')
->reorderable('volgorde')
```

- `leden_count` uses Eloquent's `withCount` (Filament's `counts('leden')` helper) to show how many members are in each category.
- `reorderable('volgorde')` provides drag handles; Filament persists the new order to the `volgorde` column on drop.

### 4. Row actions

- `EditAction::make()`
- `DeleteAction::make()->before(...)` — see Delete safety below.

### 5. Delete safety

The current FK on `team_leden.team_categorie_id` is `cascadeOnDelete`. Deleting a category silently wipes all its members. The admin guards against this:

```php
DeleteAction::make()
    ->before(function (TeamCategorie $record, Action $action) {
        if ($record->leden()->exists()) {
            Notification::make()
                ->danger()
                ->title('Kan niet verwijderen')
                ->body('Deze categorie heeft '.$record->leden()->count().' teamleden. Verplaats ze eerst naar een andere categorie.')
                ->send();
            $action->cancel();
        }
    });
```

Same guard is applied to the bulk `DeleteBulkAction` (filter the selection to records with no leden, notify the user about any skipped categories).

The DB-level `cascadeOnDelete` stays — it remains the safety net if a delete ever bypasses the admin (e.g., tinker, future code path), but is no longer the primary mechanism.

### 6. Pages

Standard three-page setup:

- `ListTeamCategorieen` — index with table + reorder + create header action
- `CreateTeamCategorie` — uses the form; overrides `mutateFormDataBeforeCreate` to set `volgorde = TeamCategorie::max('volgorde') + 1` so new categories land at the bottom (the migration default of 0 would otherwise pile them at the top)
- `EditTeamCategorie` — uses the form

### 7. Existing data

No migration needed. The 10 categories from `database/seeders/TeamCategorieSeeder.php` are already in the DB and will appear in the new resource on first load.

## Behaviour

- Opening **Instellingen → Teamcategorieën** shows the 10 existing categories in their current `volgorde` order.
- Dragging a row updates `volgorde` for the affected rows; the `TeamLid` dropdown and the public team page both reflect the new order on next request (no caching to bust).
- Creating a new category appends it at the bottom (`CreateTeamCategorie` sets `volgorde = max + 1` before insert).
- Editing only changes `naam_nl` / `naam_fr`; nothing in `team_leden` needs to change because the relationship is by `id`.
- Attempting to delete a category with leden shows a red notification and aborts. Reassigning the leden via the `TeamLidResource` first, then returning, allows the delete to succeed.
- The leden-count badge gives the admin instant feedback on which categories are populated and which are empty (and therefore safe to delete).

## Testing

Feature tests in `tests/Feature/Filament/`:

- `TeamCategorieResourceTest`:
  - `test_lists_all_categorieen_in_volgorde_order` — seeds 3 categories with mixed `volgorde`, asserts the Livewire table renders them in order.
  - `test_can_create_categorie` — submits the form with `naam_nl` + `naam_fr`, asserts the row exists.
  - `test_create_appends_to_bottom_of_volgorde` — seeds 3 categories with `volgorde` 1/2/3, creates a new one, asserts the new row's `volgorde` is 4.
  - `test_can_edit_categorie_names` — updates NL and FR names, asserts both persist.
  - `test_reorder_persists_volgorde` — calls Filament's `reorderRecords` action, asserts `volgorde` columns updated.
  - `test_delete_blocked_when_leden_exist` — creates a categorie with one `TeamLid`, attempts delete, asserts the categorie still exists and the leden are untouched.
  - `test_delete_succeeds_when_empty` — creates an empty categorie, deletes it, asserts the row is gone.
  - `test_leden_count_column_renders` — asserts the count badge value matches `leden()->count()`.

## Open questions

None.
