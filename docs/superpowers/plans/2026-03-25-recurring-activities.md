# Recurring Activities Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Let the admin define a weekly recurring activity series once, have the system generate all individual sessions automatically, and propagate metadata changes to future sessions on request.

**Architecture:** A new `activiteit_templates` table stores series definitions. A service class generates `Activiteit` rows linked via `template_id`. The Filament admin gets a "Reeksen" resource for managing series; individual sessions remain editable as before. The public site is unchanged.

**Tech Stack:** Laravel 13, Filament 4 (uses `Filament\Schemas\Schema`), Livewire 3, PHPUnit 12, Pint

---

## File Map

| Action | Path | Responsibility |
|---|---|---|
| Create | `database/migrations/XXXX_create_activiteit_templates_table.php` | `activiteit_templates` schema |
| Create | `database/migrations/XXXX_add_template_id_to_activiteiten_table.php` | FK column on `activiteiten` |
| Create | `app/Models/ActiviteitTemplate.php` | Model with casts, fillable, relationships |
| Modify | `app/Models/Activiteit.php` | Add `template_id` to fillable + `belongsTo` |
| Create | `app/Services/ActiviteitTemplateService.php` | `generateSessions()` + `propagateToFutureSessions()` |
| Create | `database/factories/ActiviteitTemplateFactory.php` | Test factory |
| Create | `tests/Feature/ActiviteitTemplateServiceTest.php` | Service unit/feature tests |
| Create | `app/Filament/Resources/ActiviteitTemplateResource.php` | Form schema + table columns |
| Create | `app/Filament/Resources/ActiviteitTemplateResource/Pages/ListActiviteitTemplates.php` | List page |
| Create | `app/Filament/Resources/ActiviteitTemplateResource/Pages/CreateActiviteitTemplate.php` | Create page + `afterCreate` hook |
| Create | `app/Filament/Resources/ActiviteitTemplateResource/Pages/EditActiviteitTemplate.php` | Edit page + propagation action |
| Modify | `app/Filament/Resources/ActiviteitResource.php` | Add "Reeks" column to table |

---

### Task 1: Migrations

**Files:**
- Create: `database/migrations/XXXX_create_activiteit_templates_table.php`
- Create: `database/migrations/XXXX_add_template_id_to_activiteiten_table.php`

- [ ] **Step 1: Generate both migrations**

```bash
cd /Users/frederikvincx/Herd/harmonie
php artisan make:migration create_activiteit_templates_table --no-interaction
php artisan make:migration add_template_id_to_activiteiten_table --no-interaction
```

- [ ] **Step 2: Fill in the templates migration**

Open the generated `create_activiteit_templates_table` migration. Replace the `up()` body:

```php
Schema::create('activiteit_templates', function (Blueprint $table) {
    $table->id();
    $table->string('titel_nl');
    $table->string('titel_fr');
    $table->text('beschrijving_nl')->nullable();
    $table->text('beschrijving_fr')->nullable();
    $table->text('notice_nl')->nullable();
    $table->text('notice_fr')->nullable();
    $table->time('startuur');
    $table->time('einduur')->nullable();
    $table->string('locatie')->default('De Harmonie');
    $table->decimal('prijs', 8, 2)->nullable();
    $table->integer('max_deelnemers')->nullable();
    $table->string('interesse')->nullable();
    $table->tinyInteger('dag_van_de_week'); // 0=Sun, 1=Mon, ..., 6=Sat (Carbon convention)
    $table->date('reeks_start');
    $table->date('reeks_einde');
    $table->timestamps();
});
```

- [ ] **Step 3: Fill in the template_id migration**

Open the generated `add_template_id_to_activiteiten_table` migration. Replace the `up()` and `down()` bodies:

```php
public function up(): void
{
    Schema::table('activiteiten', function (Blueprint $table) {
        $table->foreignId('template_id')
            ->nullable()
            ->after('id')
            ->constrained('activiteit_templates')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('activiteiten', function (Blueprint $table) {
        $table->dropForeignIdFor(\App\Models\ActiviteitTemplate::class);
        $table->dropColumn('template_id');
    });
}
```

- [ ] **Step 4: Run migrations**

```bash
php artisan migrate --no-interaction
```

Expected: both migrations run without errors.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/
git commit -m "feat: add activiteit_templates table and template_id FK on activiteiten"
```

---

### Task 2: Models

**Files:**
- Create: `app/Models/ActiviteitTemplate.php`
- Modify: `app/Models/Activiteit.php`

- [ ] **Step 1: Generate the model**

```bash
php artisan make:model ActiviteitTemplate --no-interaction
```

- [ ] **Step 2: Replace the generated model content**

`app/Models/ActiviteitTemplate.php`:

```php
<?php

namespace App\Models;

use App\Enums\Interesse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActiviteitTemplate extends Model
{
    use HasFactory;

    protected $table = 'activiteit_templates';

    protected $fillable = [
        'titel_nl', 'titel_fr',
        'beschrijving_nl', 'beschrijving_fr',
        'notice_nl', 'notice_fr',
        'startuur', 'einduur',
        'locatie', 'prijs', 'max_deelnemers',
        'interesse', 'dag_van_de_week',
        'reeks_start', 'reeks_einde',
    ];

    protected $casts = [
        'interesse'       => Interesse::class,
        'dag_van_de_week' => 'integer',
        'reeks_start'     => 'date',
        'reeks_einde'     => 'date',
        'prijs'           => 'decimal:2',
    ];

    public function activiteiten(): HasMany
    {
        return $this->hasMany(Activiteit::class, 'template_id');
    }
}
```

- [ ] **Step 3: Update Activiteit model**

In `app/Models/Activiteit.php`:

Add `'template_id'` to the `$fillable` array.

Add the relationship method:

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

public function template(): BelongsTo
{
    return $this->belongsTo(ActiviteitTemplate::class, 'template_id');
}
```

- [ ] **Step 4: Create the factory**

`database/factories/ActiviteitTemplateFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Enums\Interesse;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActiviteitTemplateFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+1 month');
        $end   = $this->faker->dateTimeBetween('+2 months', '+4 months');

        return [
            'titel_nl'        => $this->faker->sentence(3),
            'titel_fr'        => $this->faker->sentence(3),
            'beschrijving_nl' => $this->faker->paragraph(),
            'beschrijving_fr' => $this->faker->paragraph(),
            'notice_nl'       => null,
            'notice_fr'       => null,
            'startuur'        => '10:00:00',
            'einduur'         => '12:00:00',
            'locatie'         => 'De Harmonie',
            'prijs'           => null,
            'max_deelnemers'  => null,
            'interesse'       => Interesse::Activiteiten->value,
            'dag_van_de_week' => 1, // Monday
            'reeks_start'     => $start->format('Y-m-d'),
            'reeks_einde'     => $end->format('Y-m-d'),
        ];
    }
}
```

- [ ] **Step 5: Format with Pint**

```bash
vendor/bin/pint app/Models/ActiviteitTemplate.php app/Models/Activiteit.php database/factories/ActiviteitTemplateFactory.php --format agent
```

- [ ] **Step 6: Commit**

```bash
git add app/Models/ database/factories/ActiviteitTemplateFactory.php
git commit -m "feat: add ActiviteitTemplate model and update Activiteit with template relationship"
```

---

### Task 3: Service (TDD)

**Files:**
- Create: `app/Services/ActiviteitTemplateService.php`
- Create: `tests/Feature/ActiviteitTemplateServiceTest.php`

- [ ] **Step 1: Create the test file**

```bash
php artisan make:test ActiviteitTemplateServiceTest --phpunit --no-interaction
```

- [ ] **Step 2: Write failing tests**

Replace `tests/Feature/ActiviteitTemplateServiceTest.php` with:

```php
<?php

namespace Tests\Feature;

use App\Enums\ActiviteitStatus;
use App\Enums\Interesse;
use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use App\Models\Deelnameverzoek;
use App\Services\ActiviteitTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    private ActiviteitTemplateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ActiviteitTemplateService();
    }

    public function test_generates_sessions_for_every_matching_weekday(): void
    {
        // Mondays from 2026-04-06 to 2026-04-27 = 4 Mondays
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1, // Monday
            'reeks_start'     => '2026-04-06',
            'reeks_einde'     => '2026-04-27',
        ]);

        $count = $this->service->generateSessions($template);

        $this->assertSame(4, $count);
        $this->assertSame(4, Activiteit::where('template_id', $template->id)->count());
    }

    public function test_generated_sessions_have_correct_fields(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'titel_nl'        => 'Zumba',
            'dag_van_de_week' => 5, // Friday
            'reeks_start'     => '2026-04-03',
            'reeks_einde'     => '2026-04-03',
            'startuur'        => '10:00:00',
            'locatie'         => 'De Harmonie',
        ]);

        $this->service->generateSessions($template);

        $session = Activiteit::where('template_id', $template->id)->first();
        $this->assertNotNull($session);
        $this->assertSame('2026-04-03', $session->datum->format('Y-m-d'));
        $this->assertSame('10:00:00', $session->startuur);
        $this->assertSame(ActiviteitStatus::Concept, $session->status);
        $this->assertStringContainsString('2026-04-03', $session->slug);
    }

    public function test_does_not_duplicate_existing_sessions(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start'     => '2026-04-06',
            'reeks_einde'     => '2026-04-06',
        ]);

        $this->service->generateSessions($template);
        $count = $this->service->generateSessions($template); // run again

        $this->assertSame(0, $count);
        $this->assertSame(1, Activiteit::where('template_id', $template->id)->count());
    }

    public function test_generates_unique_slugs_for_title_collisions(): void
    {
        // Pre-existing activiteit with the same slug pattern
        Activiteit::factory()->create(['slug' => 'zumba-2026-04-06']);

        $template = ActiviteitTemplate::factory()->create([
            'titel_nl'        => 'Zumba',
            'dag_van_de_week' => 1,
            'reeks_start'     => '2026-04-06',
            'reeks_einde'     => '2026-04-06',
        ]);

        $this->service->generateSessions($template);

        $session = Activiteit::where('template_id', $template->id)->first();
        $this->assertNotSame('zumba-2026-04-06', $session->slug);
    }

    public function test_propagate_updates_future_eligible_sessions(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start'     => now()->subWeeks(2)->startOfWeek(),
            'reeks_einde'     => now()->addWeeks(4)->startOfWeek(),
        ]);
        $this->service->generateSessions($template);

        $template->update(['titel_nl' => 'Zumba Updated', 'titel_fr' => 'Zumba Mise à Jour']);
        $updated = $this->service->propagateToFutureSessions($template);

        $this->assertGreaterThan(0, $updated);

        // Future sessions get new title
        $futureSessions = Activiteit::where('template_id', $template->id)
            ->where('datum', '>=', today())
            ->get();
        foreach ($futureSessions as $session) {
            $this->assertSame('Zumba Updated', $session->titel_nl);
        }

        // Past sessions are untouched
        $pastSessions = Activiteit::where('template_id', $template->id)
            ->where('datum', '<', today())
            ->get();
        foreach ($pastSessions as $session) {
            $this->assertNotSame('Zumba Updated', $session->titel_nl);
        }
    }

    public function test_propagate_skips_cancelled_sessions(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start'     => now()->addWeek()->startOfWeek(),
            'reeks_einde'     => now()->addWeeks(2)->startOfWeek(),
        ]);
        $this->service->generateSessions($template);

        // Cancel the first future session
        $session = Activiteit::where('template_id', $template->id)->orderBy('datum')->first();
        $session->update(['status' => ActiviteitStatus::Geannuleerd, 'titel_nl' => 'Original']);

        $template->update(['titel_nl' => 'Changed']);
        $this->service->propagateToFutureSessions($template);

        $session->refresh();
        $this->assertSame('Original', $session->titel_nl);
    }

    public function test_propagate_skips_sessions_with_active_registrations(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start'     => now()->addWeek()->startOfWeek(),
            'reeks_einde'     => now()->addWeek()->startOfWeek(),
        ]);
        $this->service->generateSessions($template);

        $session = Activiteit::where('template_id', $template->id)->first();
        $session->update(['titel_nl' => 'Original']);
        Deelnameverzoek::factory()->create([
            'activiteit_id' => $session->id,
            'status'        => 'te_contacteren',
        ]);

        $template->update(['titel_nl' => 'Changed']);
        $this->service->propagateToFutureSessions($template);

        $session->refresh();
        $this->assertSame('Original', $session->titel_nl);
    }

    public function test_propagate_skips_session_if_new_max_deelnemers_would_overbook(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'dag_van_de_week' => 1,
            'reeks_start'     => now()->addWeek()->startOfWeek(),
            'reeks_einde'     => now()->addWeek()->startOfWeek(),
            'max_deelnemers'  => 10,
        ]);
        $this->service->generateSessions($template);

        $session = Activiteit::where('template_id', $template->id)->first();

        // Add 3 active registrations
        Deelnameverzoek::factory()->count(3)->create([
            'activiteit_id' => $session->id,
            'status'        => 'te_contacteren',
        ]);

        // Try to reduce max_deelnemers below current booking count
        $template->update(['max_deelnemers' => 2]);
        $this->service->propagateToFutureSessions($template);

        $session->refresh();
        $this->assertSame(10, $session->max_deelnemers); // unchanged
    }

    public function test_slugs_are_never_changed_during_propagation(): void
    {
        $template = ActiviteitTemplate::factory()->create([
            'titel_nl'        => 'Zumba',
            'dag_van_de_week' => 1,
            'reeks_start'     => now()->addWeek()->startOfWeek(),
            'reeks_einde'     => now()->addWeek()->startOfWeek(),
        ]);
        $this->service->generateSessions($template);

        $session       = Activiteit::where('template_id', $template->id)->first();
        $originalSlug  = $session->slug;

        $template->update(['titel_nl' => 'Renamed Activity']);
        $this->service->propagateToFutureSessions($template);

        $session->refresh();
        $this->assertSame($originalSlug, $session->slug);
    }
}
```

- [ ] **Step 3: Run tests — confirm they fail**

```bash
php artisan test --compact tests/Feature/ActiviteitTemplateServiceTest.php
```

Expected: all tests fail (service class does not exist yet).

- [ ] **Step 4: Check if Activiteit and Deelnameverzoek factories exist**

```bash
ls database/factories/
```

If `ActiviteitFactory.php` or `DeelnameverzoekFactory.php` are missing, create them. Minimal `ActiviteitFactory`:

```php
<?php

namespace Database\Factories;

use App\Enums\ActiviteitStatus;
use App\Enums\Interesse;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActiviteitFactory extends Factory
{
    public function definition(): array
    {
        $titel = $this->faker->sentence(3);
        return [
            'slug'            => Str::slug($titel) . '-' . $this->faker->unique()->numerify('####'),
            'titel_nl'        => $titel,
            'titel_fr'        => $titel,
            'beschrijving_nl' => null,
            'beschrijving_fr' => null,
            'notice_nl'       => null,
            'notice_fr'       => null,
            'datum'           => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'startuur'        => '10:00:00',
            'einduur'         => null,
            'locatie'         => 'De Harmonie',
            'prijs'           => null,
            'max_deelnemers'  => null,
            'status'          => ActiviteitStatus::Concept,
            'interesse'       => Interesse::Activiteiten->value,
            'template_id'     => null,
        ];
    }
}
```

Minimal `DeelnameverzoekFactory`:

```php
<?php

namespace Database\Factories;

use App\Models\Activiteit;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeelnameverzoekFactory extends Factory
{
    public function definition(): array
    {
        return [
            'activiteit_id' => Activiteit::factory(),
            'voornaam'      => $this->faker->firstName(),
            'achternaam'    => $this->faker->lastName(),
            'email'         => $this->faker->safeEmail(),
            'telefoon'      => $this->faker->phoneNumber(),
            'status'        => 'nieuw',
            'taal'          => 'nl',
        ];
    }
}
```

(Check `database/migrations/2026_03_24_232418_create_deelnameverzoeken_table.php` for exact columns and adjust if needed.)

- [ ] **Step 5: Create the service class**

`app/Services/ActiviteitTemplateService.php`:

```php
<?php

namespace App\Services;

use App\Enums\ActiviteitStatus;
use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ActiviteitTemplateService
{
    public function generateSessions(ActiviteitTemplate $template): int
    {
        $count  = 0;
        $period = CarbonPeriod::create($template->reeks_start, $template->reeks_einde);

        foreach ($period as $date) {
            if ($date->dayOfWeek !== $template->dag_van_de_week) {
                continue;
            }

            $dateString = $date->format('Y-m-d');

            $exists = Activiteit::where('template_id', $template->id)
                ->where('datum', $dateString)
                ->exists();

            if ($exists) {
                continue;
            }

            Activiteit::create([
                'template_id'     => $template->id,
                'titel_nl'        => $template->titel_nl,
                'titel_fr'        => $template->titel_fr,
                'beschrijving_nl' => $template->beschrijving_nl,
                'beschrijving_fr' => $template->beschrijving_fr,
                'notice_nl'       => $template->notice_nl,
                'notice_fr'       => $template->notice_fr,
                'datum'           => $dateString,
                'startuur'        => $template->startuur,
                'einduur'         => $template->einduur,
                'locatie'         => $template->locatie,
                'prijs'           => $template->prijs,
                'max_deelnemers'  => $template->max_deelnemers,
                'interesse'       => $template->interesse?->value,
                'status'          => ActiviteitStatus::Concept,
                'slug'            => $this->uniqueSlug($template->titel_nl, $dateString),
            ]);

            $count++;
        }

        return $count;
    }

    public function propagateToFutureSessions(ActiviteitTemplate $template): int
    {
        $updated  = 0;
        $sessions = Activiteit::where('template_id', $template->id)
            ->where('datum', '>=', today())
            ->where('status', '!=', ActiviteitStatus::Geannuleerd->value)
            ->get();

        foreach ($sessions as $session) {
            $activeRegistrations = $session->deelnameverzoeken()
                ->whereIn('status', ['te_contacteren', 'afgehandeld'])
                ->count();

            if ($activeRegistrations > 0) {
                continue;
            }

            $data = [
                'titel_nl'        => $template->titel_nl,
                'titel_fr'        => $template->titel_fr,
                'beschrijving_nl' => $template->beschrijving_nl,
                'beschrijving_fr' => $template->beschrijving_fr,
                'notice_nl'       => $template->notice_nl,
                'notice_fr'       => $template->notice_fr,
                'startuur'        => $template->startuur,
                'einduur'         => $template->einduur,
                'locatie'         => $template->locatie,
                'interesse'       => $template->interesse?->value,
            ];

            if ($template->max_deelnemers !== null) {
                $booked = $session->deelnameverzoeken()
                    ->whereIn('status', ['te_contacteren', 'afgehandeld'])
                    ->count();

                if ($booked >= $template->max_deelnemers) {
                    Log::warning("Skipping max_deelnemers propagation for activiteit #{$session->id}: would overbook.");
                } else {
                    $data['max_deelnemers'] = $template->max_deelnemers;
                }
            }

            $session->update($data);
            $updated++;
        }

        return $updated;
    }

    private function uniqueSlug(string $title, string $date): string
    {
        $base = Str::slug($title) . '-' . $date;
        $slug = $base;
        $i    = 2;

        while (Activiteit::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
```

- [ ] **Step 6: Run tests — confirm they pass**

```bash
php artisan test --compact tests/Feature/ActiviteitTemplateServiceTest.php
```

Expected: all tests pass.

- [ ] **Step 7: Format with Pint**

```bash
vendor/bin/pint app/Services/ActiviteitTemplateService.php tests/Feature/ActiviteitTemplateServiceTest.php database/factories/ --format agent
```

- [ ] **Step 8: Commit**

```bash
git add app/Services/ tests/Feature/ActiviteitTemplateServiceTest.php database/factories/
git commit -m "feat: add ActiviteitTemplateService with session generation and propagation"
```

---

### Task 4: Filament Resource (List + Create)

**Files:**
- Create: `app/Filament/Resources/ActiviteitTemplateResource.php`
- Create: `app/Filament/Resources/ActiviteitTemplateResource/Pages/ListActiviteitTemplates.php`
- Create: `app/Filament/Resources/ActiviteitTemplateResource/Pages/CreateActiviteitTemplate.php`

- [ ] **Step 1: Scaffold with artisan**

```bash
php artisan make:filament-resource ActiviteitTemplate --no-interaction
```

This generates the resource and page classes. Check that `app/Filament/Resources/ActiviteitTemplateResource.php` and the `Pages/` subdirectory were created.

- [ ] **Step 2: Replace ActiviteitTemplateResource.php**

```php
<?php

namespace App\Filament\Resources;

use App\Enums\Interesse;
use App\Filament\Resources\ActiviteitTemplateResource\Pages;
use App\Models\ActiviteitTemplate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ActiviteitTemplateResource extends Resource
{
    protected static ?string $model = ActiviteitTemplate::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $navigationLabel = 'Reeksen';
    protected static ?string $modelLabel = 'Reeks';
    protected static ?string $pluralModelLabel = 'Reeksen';
    protected static ?string $slug = 'reeksen';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Talen')->tabs([
                Tab::make('Nederlands')->schema([
                    TextInput::make('titel_nl')
                        ->label('Titel (NL)')
                        ->required()
                        ->maxLength(255),
                    RichEditor::make('beschrijving_nl')
                        ->label('Beschrijving (NL)')
                        ->toolbarButtons(['bold', 'bulletList', 'link']),
                    Textarea::make('notice_nl')
                        ->label('Opmerking (NL)'),
                ]),
                Tab::make('Français')->schema([
                    TextInput::make('titel_fr')
                        ->label('Titre (FR)')
                        ->required()
                        ->maxLength(255),
                    RichEditor::make('beschrijving_fr')
                        ->label('Description (FR)')
                        ->toolbarButtons(['bold', 'bulletList', 'link']),
                    Textarea::make('notice_fr')
                        ->label('Remarque (FR)'),
                ]),
            ])->columnSpanFull(),

            Select::make('dag_van_de_week')
                ->label('Dag van de week')
                ->options([
                    0 => 'Zondag',
                    1 => 'Maandag',
                    2 => 'Dinsdag',
                    3 => 'Woensdag',
                    4 => 'Donderdag',
                    5 => 'Vrijdag',
                    6 => 'Zaterdag',
                ])
                ->required(),

            DatePicker::make('reeks_start')
                ->label('Start van de reeks')
                ->required(),

            DatePicker::make('reeks_einde')
                ->label('Einde van de reeks')
                ->required()
                ->after('reeks_start'),

            TimePicker::make('startuur')
                ->label('Startuur')
                ->required()
                ->seconds(false),

            TimePicker::make('einduur')
                ->label('Einduur')
                ->seconds(false),

            TextInput::make('locatie')
                ->label('Locatie')
                ->default('De Harmonie')
                ->required(),

            TextInput::make('prijs')
                ->label('Prijs (€, leeg = gratis)')
                ->numeric()
                ->prefix('€'),

            TextInput::make('max_deelnemers')
                ->label('Max deelnemers (leeg = onbeperkt)')
                ->integer(),

            Select::make('interesse')
                ->label('Categorie')
                ->options(Interesse::class),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('titel_nl')
                    ->label('Titel')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dag_van_de_week')
                    ->label('Dag')
                    ->formatStateUsing(fn (int $state): string => [
                        0 => 'Zondag', 1 => 'Maandag', 2 => 'Dinsdag',
                        3 => 'Woensdag', 4 => 'Donderdag', 5 => 'Vrijdag', 6 => 'Zaterdag',
                    ][$state] ?? '—'),
                Tables\Columns\TextColumn::make('reeks_start')
                    ->label('Start')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reeks_einde')
                    ->label('Einde')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('activiteiten_count')
                    ->label('Sessies')
                    ->counts('activiteiten'),
            ])
            ->defaultSort('reeks_start', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListActiviteitTemplates::route('/'),
            'create' => Pages\CreateActiviteitTemplate::route('/create'),
            'edit'   => Pages\EditActiviteitTemplate::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Replace CreateActiviteitTemplate.php**

`app/Filament/Resources/ActiviteitTemplateResource/Pages/CreateActiviteitTemplate.php`:

```php
<?php

namespace App\Filament\Resources\ActiviteitTemplateResource\Pages;

use App\Filament\Resources\ActiviteitTemplateResource;
use App\Services\ActiviteitTemplateService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateActiviteitTemplate extends CreateRecord
{
    protected static string $resource = ActiviteitTemplateResource::class;

    protected function afterCreate(): void
    {
        $service = new ActiviteitTemplateService();
        $count   = $service->generateSessions($this->record);

        Notification::make()
            ->title("{$count} sessies aangemaakt voor {$this->record->titel_nl}")
            ->success()
            ->send();
    }
}
```

- [ ] **Step 4: Replace ListActiviteitTemplates.php**

`app/Filament/Resources/ActiviteitTemplateResource/Pages/ListActiviteitTemplates.php`:

```php
<?php

namespace App\Filament\Resources\ActiviteitTemplateResource\Pages;

use App\Filament\Resources\ActiviteitTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActiviteitTemplates extends ListRecords
{
    protected static string $resource = ActiviteitTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
```

- [ ] **Step 5: Verify the resource loads in browser**

Visit `https://harmonie.test/admin/reeksen` — the list page should render without errors.

- [ ] **Step 6: Format with Pint**

```bash
vendor/bin/pint app/Filament/Resources/ActiviteitTemplateResource.php app/Filament/Resources/ActiviteitTemplateResource/ --format agent
```

- [ ] **Step 7: Commit**

```bash
git add app/Filament/Resources/ActiviteitTemplateResource.php app/Filament/Resources/ActiviteitTemplateResource/
git commit -m "feat: add ActiviteitTemplateResource with create-and-generate flow"
```

---

### Task 5: Edit Page with Propagation Action

**Files:**
- Modify: `app/Filament/Resources/ActiviteitTemplateResource/Pages/EditActiviteitTemplate.php`

- [ ] **Step 1: Replace EditActiviteitTemplate.php**

```php
<?php

namespace App\Filament\Resources\ActiviteitTemplateResource\Pages;

use App\Filament\Resources\ActiviteitTemplateResource;
use App\Services\ActiviteitTemplateService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditActiviteitTemplate extends EditRecord
{
    protected static string $resource = ActiviteitTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),

            Actions\Action::make('saveAndPropagate')
                ->label('Opslaan en toepassen op toekomstige sessies')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Toepassen op toekomstige sessies')
                ->modalDescription('Wijzigingen worden toegepast op alle toekomstige sessies die nog geen inschrijvingen hebben en niet geannuleerd zijn.')
                ->modalSubmitActionLabel('Ja, toepassen')
                ->action(function (): void {
                    $this->save();

                    $service = new ActiviteitTemplateService();
                    $count   = $service->propagateToFutureSessions($this->record);

                    Notification::make()
                        ->title("{$count} toekomstige sessies bijgewerkt")
                        ->success()
                        ->send();
                }),

            $this->getCancelFormAction(),
        ];
    }

    protected function afterSave(): void
    {
        $service = new ActiviteitTemplateService();
        $service->generateSessions($this->record); // generates new sessions if reeks_einde was extended
    }
}
```

- [ ] **Step 2: Verify in browser**

Visit `https://harmonie.test/admin/reeksen`, create a test series, then open it for editing. Confirm two buttons appear: "Opslaan" and "Opslaan en toepassen op toekomstige sessies". Click the latter and confirm the modal appears.

- [ ] **Step 3: Format with Pint**

```bash
vendor/bin/pint app/Filament/Resources/ActiviteitTemplateResource/Pages/EditActiviteitTemplate.php --format agent
```

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Resources/ActiviteitTemplateResource/Pages/EditActiviteitTemplate.php
git commit -m "feat: add propagation action to EditActiviteitTemplate"
```

---

### Task 6: Add "Reeks" Column to ActiviteitResource

**Files:**
- Modify: `app/Filament/Resources/ActiviteitResource.php`

- [ ] **Step 1: Add the column**

In `app/Filament/Resources/ActiviteitResource.php`, inside the `table()` method's `->columns([...])` array, add after the `status` column:

```php
Tables\Columns\TextColumn::make('template.titel_nl')
    ->label('Reeks')
    ->placeholder('—')
    ->sortable(),
```

- [ ] **Step 2: Verify in browser**

Visit `https://harmonie.test/admin/activiteiten` — confirm the "Reeks" column appears, showing the series name for linked sessions and "—" for one-off activities.

- [ ] **Step 3: Format with Pint**

```bash
vendor/bin/pint app/Filament/Resources/ActiviteitResource.php --format agent
```

- [ ] **Step 4: Commit**

```bash
git add app/Filament/Resources/ActiviteitResource.php
git commit -m "feat: show series name in activiteiten table"
```

---

### Task 7: Final verification

- [ ] **Step 1: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests pass.

- [ ] **Step 2: Smoke test the full admin flow**

1. Go to `https://harmonie.test/admin/reeksen` → Create new series (e.g. "Zumba test", Vrijdag, 4-week range)
2. Confirm notification shows correct session count
3. Go to `https://harmonie.test/admin/activiteiten` — confirm sessions appear with "Reeks" = "Zumba test"
4. Edit the template → change the title → click "Opslaan en toepassen" → confirm modal → confirm sessions updated
5. Cancel one session manually in Activiteiten — confirm series is unchanged
6. Delete the template — confirm sessions remain but `template_id` is null
