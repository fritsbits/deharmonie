# Teamcategorie Admin Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a Filament resource under "Instellingen" that lets the team add, rename, reorder, and delete `TeamCategorie` rows themselves.

**Architecture:** Single Filament 4 resource backed by the existing `TeamCategorie` model and `team_categorieen` table. Order managed by Filament's native `reorderable('volgorde')`. Delete blocked when leden are still attached (the DB-level `cascadeOnDelete` stays as a fallback only). No migration, no model change — the data and schema already exist.

**Tech Stack:** Laravel 13, Filament 4, Livewire 3, PHPUnit 12, Pint

**Spec:** `docs/superpowers/specs/2026-04-23-teamcategorie-admin-design.md`

---

## File Map

| Action | Path | Responsibility |
|---|---|---|
| Create | `app/Filament/Resources/TeamCategorieResource.php` | Resource definition: form, table, navigation registration |
| Create | `app/Filament/Resources/TeamCategorieResource/Pages/ListTeamCategorieen.php` | Index page with create header action |
| Create | `app/Filament/Resources/TeamCategorieResource/Pages/CreateTeamCategorie.php` | Create page; assigns `volgorde = max + 1` before insert |
| Create | `app/Filament/Resources/TeamCategorieResource/Pages/EditTeamCategorie.php` | Edit page with delete header action |
| Create | `tests/Feature/Filament/TeamCategorieResourceTest.php` | All resource feature tests |

**Not modified:** `App\Models\TeamCategorie`, `App\Models\TeamLid`, the `team_categorieen` migration, the `TeamLidResource` (reads from `TeamCategorie::orderBy('volgorde')` — picks up changes automatically).

---

## Pre-flight

- [ ] **Step 1: Verify branch + clean tree**

```bash
git status && git branch --show-current
```

Expected: `nothing to commit, working tree clean` on `main` (or whichever branch the user wants this work on).

- [ ] **Step 2: Verify baseline test suite is green**

```bash
php artisan test --compact
```

Expected: all green. Stop if anything fails — we need a clean baseline.

- [ ] **Step 3: Confirm seeded categories exist locally**

```bash
php artisan tinker --execute 'echo App\Models\TeamCategorie::count();'
```

Expected: `10` (the team categories from `TeamCategorieSeeder`). If `0`, run `php artisan db:seed --class=TeamCategorieSeeder` first.

---

### Task 1: Resource scaffold with smoke tests

Goal: get the resource loading at `/admin/teamcategorieen` with empty form and table. No logic yet — just the wiring so we can test-drive features.

**Files:**
- Create: `app/Filament/Resources/TeamCategorieResource.php`
- Create: `app/Filament/Resources/TeamCategorieResource/Pages/ListTeamCategorieen.php`
- Create: `app/Filament/Resources/TeamCategorieResource/Pages/CreateTeamCategorie.php`
- Create: `app/Filament/Resources/TeamCategorieResource/Pages/EditTeamCategorie.php`
- Create: `tests/Feature/Filament/TeamCategorieResourceTest.php`

- [ ] **Step 1: Write the failing smoke tests**

Create `tests/Feature/Filament/TeamCategorieResourceTest.php`:

```php
<?php

namespace Tests\Feature\Filament;

use App\Models\TeamCategorie;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamCategorieResourceTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', 'admin@deharmonie.be')->firstOrFail();
    }

    public function test_index_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/teamcategorieen');

        $response->assertStatus(200);
    }

    public function test_create_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/teamcategorieen/create');

        $response->assertStatus(200);
    }

    public function test_edit_page_renders(): void
    {
        $this->seed(AdminUserSeeder::class);

        $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);

        $response = $this->actingAs($this->adminUser())->get("/admin/teamcategorieen/{$categorie->id}/edit");

        $response->assertStatus(200);
    }
}
```

- [ ] **Step 2: Run the smoke tests to confirm they fail**

```bash
php artisan test --compact --filter=TeamCategorieResourceTest
```

Expected: FAIL — route not found (404). The resource doesn't exist yet.

- [ ] **Step 3: Create the resource skeleton**

Create `app/Filament/Resources/TeamCategorieResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamCategorieResource\Pages;
use App\Models\TeamCategorie;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TeamCategorieResource extends Resource
{
    protected static ?string $model = TeamCategorie::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Teamcategorieën';

    protected static ?string $modelLabel = 'Teamcategorie';

    protected static ?string $pluralModelLabel = 'Teamcategorieën';

    protected static ?string $slug = 'teamcategorieen';

    protected static \UnitEnum|string|null $navigationGroup = 'Instellingen';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamCategorieen::route('/'),
            'create' => Pages\CreateTeamCategorie::route('/create'),
            'edit' => Pages\EditTeamCategorie::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 4: Create the three page classes**

Create `app/Filament/Resources/TeamCategorieResource/Pages/ListTeamCategorieen.php`:

```php
<?php

namespace App\Filament\Resources\TeamCategorieResource\Pages;

use App\Filament\Resources\TeamCategorieResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamCategorieen extends ListRecords
{
    protected static string $resource = TeamCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
```

Create `app/Filament/Resources/TeamCategorieResource/Pages/CreateTeamCategorie.php`:

```php
<?php

namespace App\Filament\Resources\TeamCategorieResource\Pages;

use App\Filament\Resources\TeamCategorieResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamCategorie extends CreateRecord
{
    protected static string $resource = TeamCategorieResource::class;
}
```

Create `app/Filament/Resources/TeamCategorieResource/Pages/EditTeamCategorie.php`:

```php
<?php

namespace App\Filament\Resources\TeamCategorieResource\Pages;

use App\Filament\Resources\TeamCategorieResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamCategorie extends EditRecord
{
    protected static string $resource = TeamCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
```

- [ ] **Step 5: Run the smoke tests to confirm they pass**

```bash
php artisan test --compact --filter=TeamCategorieResourceTest
```

Expected: PASS (3 tests).

- [ ] **Step 6: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 7: Commit (stop and ask user before committing)**

Tell the user the scaffold is in place and ask permission to commit. Suggested message:

```
feat(admin): scaffold TeamCategorieResource under Instellingen
```

---

### Task 2: Form fields (NL + FR) and create/edit persistence

**Files:**
- Modify: `app/Filament/Resources/TeamCategorieResource.php` (form method)
- Modify: `tests/Feature/Filament/TeamCategorieResourceTest.php` (add tests)

- [ ] **Step 1: Write failing tests for create + edit persistence**

Append to `TeamCategorieResourceTest.php`:

```php
public function test_can_create_categorie_with_nl_and_fr_names(): void
{
    $this->seed(AdminUserSeeder::class);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\CreateTeamCategorie::class)
        ->fillForm([
            'naam_nl' => 'Vrijwilligers',
            'naam_fr' => 'Bénévoles',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('team_categorieen', [
        'naam_nl' => 'Vrijwilligers',
        'naam_fr' => 'Bénévoles',
    ]);
}

public function test_can_edit_categorie_names(): void
{
    $this->seed(AdminUserSeeder::class);

    $categorie = TeamCategorie::factory()->create([
        'naam_nl' => 'Oude naam',
        'naam_fr' => 'Ancien nom',
        'volgorde' => 1,
    ]);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\EditTeamCategorie::class, [
            'record' => $categorie->getRouteKey(),
        ])
        ->fillForm([
            'naam_nl' => 'Nieuwe naam',
            'naam_fr' => 'Nouveau nom',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $categorie->refresh();
    $this->assertSame('Nieuwe naam', $categorie->naam_nl);
    $this->assertSame('Nouveau nom', $categorie->naam_fr);
}

public function test_naam_nl_and_naam_fr_are_required(): void
{
    $this->seed(AdminUserSeeder::class);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\CreateTeamCategorie::class)
        ->fillForm(['naam_nl' => '', 'naam_fr' => ''])
        ->call('create')
        ->assertHasFormErrors(['naam_nl' => 'required', 'naam_fr' => 'required']);
}
```

- [ ] **Step 2: Run the new tests to confirm they fail**

```bash
php artisan test --compact --filter='TeamCategorieResourceTest::test_can_create_categorie_with_nl_and_fr_names'
```

Expected: FAIL — form has no fields, so the values don't persist.

- [ ] **Step 3: Add form fields**

Replace the `form` method in `app/Filament/Resources/TeamCategorieResource.php`:

```php
use Filament\Forms\Components\TextInput;

public static function form(Schema $schema): Schema
{
    return $schema->components([
        TextInput::make('naam_nl')
            ->label('Naam (NL)')
            ->required()
            ->maxLength(255),

        TextInput::make('naam_fr')
            ->label('Nom (FR)')
            ->required()
            ->maxLength(255),
    ]);
}
```

- [ ] **Step 4: Run the form tests to confirm they pass**

```bash
php artisan test --compact --filter=TeamCategorieResourceTest
```

Expected: PASS (6 tests).

- [ ] **Step 5: Run Pint and commit (stop and ask user)**

```bash
vendor/bin/pint --dirty --format agent
```

Suggested message: `feat(admin): TeamCategorieResource form for NL/FR names`

---

### Task 3: Table columns + reorderable

**Files:**
- Modify: `app/Filament/Resources/TeamCategorieResource.php` (table method)
- Modify: `tests/Feature/Filament/TeamCategorieResourceTest.php`

- [ ] **Step 1: Write failing tests for listing in volgorde order, search, and reorder persistence**

Append to `TeamCategorieResourceTest.php`:

```php
public function test_lists_categorieen_in_volgorde_order(): void
{
    $this->seed(AdminUserSeeder::class);

    $third = TeamCategorie::factory()->create(['naam_nl' => 'Derde', 'volgorde' => 30]);
    $first = TeamCategorie::factory()->create(['naam_nl' => 'Eerste', 'volgorde' => 10]);
    $second = TeamCategorie::factory()->create(['naam_nl' => 'Tweede', 'volgorde' => 20]);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\ListTeamCategorieen::class)
        ->assertCanSeeTableRecords([$first, $second, $third], inOrder: true);
}

public function test_table_can_be_reordered_by_volgorde(): void
{
    $this->seed(AdminUserSeeder::class);

    $a = TeamCategorie::factory()->create(['naam_nl' => 'A', 'volgorde' => 1]);
    $b = TeamCategorie::factory()->create(['naam_nl' => 'B', 'volgorde' => 2]);
    $c = TeamCategorie::factory()->create(['naam_nl' => 'C', 'volgorde' => 3]);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\ListTeamCategorieen::class)
        ->reorderTable([$c->id, $a->id, $b->id]);

    $this->assertSame(1, $c->fresh()->volgorde);
    $this->assertSame(2, $a->fresh()->volgorde);
    $this->assertSame(3, $b->fresh()->volgorde);
}
```

- [ ] **Step 2: Add `volgorde` to the model's `$fillable` (required for reorderable mass assignment)**

Modify `app/Models/TeamCategorie.php`. The `$fillable` already includes `volgorde`, so no change is needed — confirm by reading the file. If `volgorde` is missing, add it:

```php
protected $fillable = ['naam_nl', 'naam_fr', 'volgorde'];
```

- [ ] **Step 3: Run the table tests to confirm they fail**

```bash
php artisan test --compact --filter='TeamCategorieResourceTest::test_lists_categorieen_in_volgorde_order'
```

Expected: FAIL — table has no columns.

- [ ] **Step 4: Add table columns + reorderable + default sort**

Replace the `table` method in `app/Filament/Resources/TeamCategorieResource.php`:

```php
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;

public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('naam_nl')
                ->label('Naam (NL)')
                ->searchable(),
            TextColumn::make('naam_fr')
                ->label('Nom (FR)')
                ->searchable(),
        ])
        ->defaultSort('volgorde')
        ->reorderable('volgorde')
        ->actions([
            EditAction::make(),
            DeleteAction::make(),
        ])
        ->bulkActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
}
```

- [ ] **Step 5: Run the table tests to confirm they pass**

```bash
php artisan test --compact --filter=TeamCategorieResourceTest
```

Expected: PASS (8 tests).

- [ ] **Step 6: Run Pint and commit (stop and ask user)**

```bash
vendor/bin/pint --dirty --format agent
```

Suggested message: `feat(admin): TeamCategorieResource table with drag-and-drop ordering`

---

### Task 4: Leden count badge column

**Files:**
- Modify: `app/Filament/Resources/TeamCategorieResource.php` (table columns)
- Modify: `tests/Feature/Filament/TeamCategorieResourceTest.php`

- [ ] **Step 1: Write the failing test**

Append to `TeamCategorieResourceTest.php`:

```php
public function test_leden_count_column_renders_count_per_categorie(): void
{
    $this->seed(AdminUserSeeder::class);

    $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);
    \App\Models\TeamLid::factory()->count(3)->create(['team_categorie_id' => $categorie->id]);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\ListTeamCategorieen::class)
        ->assertCanSeeTableRecords([$categorie])
        ->assertTableColumnStateSet('leden_count', 3, $categorie);
}
```

- [ ] **Step 2: Run the test to confirm it fails**

```bash
php artisan test --compact --filter='TeamCategorieResourceTest::test_leden_count_column_renders_count_per_categorie'
```

Expected: FAIL — column doesn't exist.

- [ ] **Step 3: Add the leden_count column**

In `app/Filament/Resources/TeamCategorieResource.php`, append to the `columns([...])` array (before the array closes):

```php
TextColumn::make('leden_count')
    ->label('Teamleden')
    ->counts('leden')
    ->badge(),
```

- [ ] **Step 4: Run the test to confirm it passes**

```bash
php artisan test --compact --filter=TeamCategorieResourceTest
```

Expected: PASS (9 tests).

- [ ] **Step 5: Run Pint and commit (stop and ask user)**

```bash
vendor/bin/pint --dirty --format agent
```

Suggested message: `feat(admin): show teamleden count badge on TeamCategorieResource`

---

### Task 5: Delete safety — block when leden exist (single + bulk)

**Files:**
- Modify: `app/Filament/Resources/TeamCategorieResource.php` (DeleteAction + DeleteBulkAction with `before` hooks)
- Modify: `app/Filament/Resources/TeamCategorieResource/Pages/EditTeamCategorie.php` (header DeleteAction with same `before` hook)
- Modify: `tests/Feature/Filament/TeamCategorieResourceTest.php`

- [ ] **Step 1: Write failing tests for delete safety**

Append to `TeamCategorieResourceTest.php`:

```php
public function test_delete_blocked_when_leden_exist(): void
{
    $this->seed(AdminUserSeeder::class);

    $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);
    \App\Models\TeamLid::factory()->create(['team_categorie_id' => $categorie->id]);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\ListTeamCategorieen::class)
        ->callTableAction('delete', $categorie);

    $this->assertDatabaseHas('team_categorieen', ['id' => $categorie->id]);
    $this->assertSame(1, \App\Models\TeamLid::where('team_categorie_id', $categorie->id)->count());
}

public function test_delete_succeeds_when_categorie_is_empty(): void
{
    $this->seed(AdminUserSeeder::class);

    $categorie = TeamCategorie::factory()->create(['volgorde' => 1]);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\ListTeamCategorieen::class)
        ->callTableAction('delete', $categorie);

    $this->assertDatabaseMissing('team_categorieen', ['id' => $categorie->id]);
}

public function test_bulk_delete_skips_categorieen_with_leden(): void
{
    $this->seed(AdminUserSeeder::class);

    $empty = TeamCategorie::factory()->create(['volgorde' => 1]);
    $populated = TeamCategorie::factory()->create(['volgorde' => 2]);
    \App\Models\TeamLid::factory()->create(['team_categorie_id' => $populated->id]);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\ListTeamCategorieen::class)
        ->callTableBulkAction('delete', [$empty->id, $populated->id]);

    $this->assertDatabaseMissing('team_categorieen', ['id' => $empty->id]);
    $this->assertDatabaseHas('team_categorieen', ['id' => $populated->id]);
}
```

- [ ] **Step 2: Run the new tests to confirm they fail**

```bash
php artisan test --compact --filter='TeamCategorieResourceTest::test_delete_blocked_when_leden_exist'
```

Expected: FAIL — current `DeleteAction` cascades, so the categorie row is gone (and so are the leden).

- [ ] **Step 3: Add the `before` guard to `DeleteAction` and `DeleteBulkAction`**

Modify the `table` method in `app/Filament/Resources/TeamCategorieResource.php`. Update the imports and replace the actions:

```php
use App\Models\TeamCategorie;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
```

Replace the `actions([...])` and `bulkActions([...])` blocks with:

```php
->actions([
    EditAction::make(),
    DeleteAction::make()
        ->before(function (DeleteAction $action, TeamCategorie $record) {
            if ($record->leden()->exists()) {
                Notification::make()
                    ->danger()
                    ->title('Kan niet verwijderen')
                    ->body('Deze categorie heeft '.$record->leden()->count().' teamleden. Verplaats ze eerst naar een andere categorie.')
                    ->send();
                $action->cancel();
            }
        }),
])
->bulkActions([
    BulkActionGroup::make([
        DeleteBulkAction::make()
            ->before(function (DeleteBulkAction $action, Collection $records) {
                $blocked = $records->filter(fn (TeamCategorie $record) => $record->leden()->exists());

                if ($blocked->isNotEmpty()) {
                    Notification::make()
                        ->warning()
                        ->title($blocked->count().' van '.$records->count().' categorieën overgeslagen')
                        ->body('Categorieën met teamleden zijn niet verwijderd: '.$blocked->pluck('naam_nl')->implode(', '))
                        ->send();
                }

                $deletable = $records->reject(fn (TeamCategorie $record) => $record->leden()->exists());
                $deletable->each->delete();

                $action->cancel();
            }),
    ]),
])
```

The bulk handler does the deletion of safe rows itself and then `cancel()`s so Filament's default handler doesn't delete the blocked ones too.

- [ ] **Step 4: Add the same guard to the EditPage's header DeleteAction**

Modify `app/Filament/Resources/TeamCategorieResource/Pages/EditTeamCategorie.php`:

```php
<?php

namespace App\Filament\Resources\TeamCategorieResource\Pages;

use App\Filament\Resources\TeamCategorieResource;
use App\Models\TeamCategorie;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTeamCategorie extends EditRecord
{
    protected static string $resource = TeamCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action, TeamCategorie $record) {
                    if ($record->leden()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Kan niet verwijderen')
                            ->body('Deze categorie heeft '.$record->leden()->count().' teamleden. Verplaats ze eerst naar een andere categorie.')
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
```

- [ ] **Step 5: Run the delete tests to confirm they pass**

```bash
php artisan test --compact --filter=TeamCategorieResourceTest
```

Expected: PASS (12 tests).

- [ ] **Step 6: Run Pint and commit (stop and ask user)**

```bash
vendor/bin/pint --dirty --format agent
```

Suggested message: `feat(admin): block deleting TeamCategorie when teamleden are attached`

---

### Task 6: Create appends new categorie at the bottom

**Files:**
- Modify: `app/Filament/Resources/TeamCategorieResource/Pages/CreateTeamCategorie.php`
- Modify: `tests/Feature/Filament/TeamCategorieResourceTest.php`

- [ ] **Step 1: Write the failing test**

Append to `TeamCategorieResourceTest.php`:

```php
public function test_create_appends_new_categorie_at_bottom(): void
{
    $this->seed(AdminUserSeeder::class);

    TeamCategorie::factory()->create(['volgorde' => 1]);
    TeamCategorie::factory()->create(['volgorde' => 2]);
    TeamCategorie::factory()->create(['volgorde' => 7]);

    \Livewire\Livewire::actingAs($this->adminUser())
        ->test(\App\Filament\Resources\TeamCategorieResource\Pages\CreateTeamCategorie::class)
        ->fillForm([
            'naam_nl' => 'Nieuwste',
            'naam_fr' => 'Plus récente',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertSame(8, TeamCategorie::where('naam_nl', 'Nieuwste')->value('volgorde'));
}
```

- [ ] **Step 2: Run the test to confirm it fails**

```bash
php artisan test --compact --filter='TeamCategorieResourceTest::test_create_appends_new_categorie_at_bottom'
```

Expected: FAIL — `volgorde` defaults to 0 in the migration, so the new row gets `0`, not `8`.

- [ ] **Step 3: Add `mutateFormDataBeforeCreate` to the create page**

Replace `app/Filament/Resources/TeamCategorieResource/Pages/CreateTeamCategorie.php`:

```php
<?php

namespace App\Filament\Resources\TeamCategorieResource\Pages;

use App\Filament\Resources\TeamCategorieResource;
use App\Models\TeamCategorie;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamCategorie extends CreateRecord
{
    protected static string $resource = TeamCategorieResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['volgorde'] = (int) TeamCategorie::max('volgorde') + 1;

        return $data;
    }
}
```

- [ ] **Step 4: Run the test to confirm it passes**

```bash
php artisan test --compact --filter=TeamCategorieResourceTest
```

Expected: PASS (13 tests).

- [ ] **Step 5: Run Pint and commit (stop and ask user)**

```bash
vendor/bin/pint --dirty --format agent
```

Suggested message: `feat(admin): append new TeamCategorie at end of volgorde`

---

### Task 7: Final verification

- [ ] **Step 1: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests green, including the existing `TeamLidResourceTest`.

- [ ] **Step 2: Run Pint across the whole project**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 3: Manual smoke check in the browser**

Open `https://deharmonie.test/admin/teamcategorieen` and verify:

1. The 10 seeded categories are listed in their seed order (Onthaal & Animatie first, Wijkraad last).
2. Each row shows a teamleden count badge that matches what's on the public team page.
3. The reorder toggle button appears in the table header; toggling it shows drag handles; dragging a row updates the order on the next refresh.
4. Clicking **+ Nieuw** opens a form with two fields: "Naam (NL)" and "Nom (FR)" (no volgorde input).
5. Submitting a new categorie places it at the bottom of the list.
6. Editing an existing categorie updates only its names, not its position.
7. Trying to delete "Onthaal & Animatie" (which has leden) shows a red notification and the row stays.
8. Creating a fresh empty categorie and then deleting it succeeds without warning.
9. The category change is immediately reflected in the **Wie is wie** dropdown when editing a `TeamLid`.

If any check fails, stop and investigate before claiming the work done.

- [ ] **Step 4: Final commit (stop and ask user)**

If you made any tweaks during the smoke check, commit them. Suggested message: `chore: tidy up after manual smoke check`. Otherwise this step is a no-op.
