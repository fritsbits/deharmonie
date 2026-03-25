# Real Data Seeder — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Seed 15 `ActiviteitTemplate` records derived from recurring activity series identified in the real CSV export, so the admin has a ready-made Reeksen list after every `migrate:fresh --seed`.

**Architecture:** A single new `ActiviteitTemplateSeeder` hardcodes all 15 templates with canonical NL/FR names, weekday, time, location, and price. `DatabaseSeeder` calls it before `ActiviteitSeeder`. No sessions are generated and no existing activities are linked to templates — the admin does that via the Filament Reeksen UI.

**Tech Stack:** Laravel 13, PHP 8.4, PHPUnit 12, MySQL via DBngin

---

## File Map

| File | Action | Purpose |
|---|---|---|
| `database/seeders/ActiviteitTemplateSeeder.php` | Create | Seeds 15 template records |
| `database/seeders/DatabaseSeeder.php` | Modify | Add `ActiviteitTemplateSeeder` call before `ActiviteitSeeder` |
| `tests/Feature/ActiviteitTemplateSeederTest.php` | Create | Verifies seeder output |

## Context You Need

### Model

`App\Models\ActiviteitTemplate` — `$table = 'activiteit_templates'`

Key fillable fields:
```
titel_nl, titel_fr, beschrijving_nl, beschrijving_fr, notice_nl, notice_fr,
startuur, einduur, locatie, prijs, max_deelnemers, interesse,
dag_van_de_week, reeks_start, reeks_einde
```

Casts: `interesse => Interesse::class`, `dag_van_de_week => 'integer'`, `reeks_start`/`reeks_einde` => `'date'`, `prijs => 'decimal:2'`

### Enums

`App\Enums\Interesse` — string-backed enum. Relevant values:
- `Interesse::Activiteiten` → stored as `'activiteiten'`
- `Interesse::Diensten` → stored as `'diensten'`

`App\Enums\DagVanDeWeek` — int-backed enum (not used directly in seeder, just for reference):
- Maandag=1, Dinsdag=2, Woensdag=3, Donderdag=4, Vrijdag=5

### Existing seeder pattern

See `database/seeders/ActiviteitSeeder.php` for the FK-check truncate pattern:
```php
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
ActiviteitTemplate::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
```

### Running tests
```bash
php artisan test --compact tests/Feature/ActiviteitTemplateSeederTest.php
```

---

## Task 1: Write and run the failing test

**Files:**
- Create: `tests/Feature/ActiviteitTemplateSeederTest.php`

- [ ] **Step 1: Create the test file**

```php
<?php

namespace Tests\Feature;

use App\Enums\Interesse;
use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use Database\Seeders\ActiviteitTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitTemplateSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_fifteen_templates(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);

        $this->assertSame(15, ActiviteitTemplate::count());
    }

    public function test_country_line_dance_template_has_correct_data(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);

        $template = ActiviteitTemplate::where('titel_nl', 'Country Line Dance')->first();

        $this->assertNotNull($template);
        $this->assertSame(4, $template->dag_van_de_week); // Donderdag
        $this->assertSame('14:00:00', $template->startuur);
        $this->assertSame('16:00:00', $template->einduur);
        $this->assertSame('De Harmonie', $template->locatie);
        $this->assertSame('2.00', $template->prijs);
        $this->assertSame(Interesse::Activiteiten, $template->interesse);
    }

    public function test_boodschappendienst_has_diensten_interesse(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);

        $template = ActiviteitTemplate::where('titel_nl', 'Boodschappendienst')->first();

        $this->assertNotNull($template);
        $this->assertSame(Interesse::Diensten, $template->interesse);
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);
        $this->seed(ActiviteitTemplateSeeder::class);

        $this->assertSame(15, ActiviteitTemplate::count());
    }

    public function test_no_existing_activities_are_linked_to_templates(): void
    {
        $this->seed(ActiviteitTemplateSeeder::class);

        $this->assertSame(0, Activiteit::whereNotNull('template_id')->count());
    }
}
```

- [ ] **Step 2: Run the test — confirm it fails**

```bash
php artisan test --compact tests/Feature/ActiviteitTemplateSeederTest.php
```

Expected: FAIL — `ActiviteitTemplateSeeder` class not found.

---

## Task 2: Implement `ActiviteitTemplateSeeder`

**Files:**
- Create: `database/seeders/ActiviteitTemplateSeeder.php`

- [ ] **Step 1: Create the seeder**

```php
<?php

namespace Database\Seeders;

use App\Enums\Interesse;
use App\Models\ActiviteitTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActiviteitTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ActiviteitTemplate::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $start = today();
        $einde = today()->addMonths(3);

        $templates = [
            [
                'titel_nl' => 'Conversatietafel Spaans',
                'titel_fr' => 'Table de conversation Espagnole',
                'dag_van_de_week' => 4,
                'startuur' => '10:00:00',
                'einduur' => '12:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Conversatietafel Engels',
                'titel_fr' => 'Table de Conversation Anglais',
                'dag_van_de_week' => 2,
                'startuur' => '10:30:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Conversatietafel Italiaans',
                'titel_fr' => 'Table de Conversation Italien',
                'dag_van_de_week' => 1,
                'startuur' => '11:30:00',
                'einduur' => '12:30:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Nederlandse conversatietafel',
                'titel_fr' => 'Table de Conversation Néerlandais',
                'dag_van_de_week' => 5,
                'startuur' => '10:30:00',
                'einduur' => '11:30:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Country Line Dance',
                'titel_fr' => 'Country Dance en Ligne',
                'dag_van_de_week' => 4,
                'startuur' => '14:00:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => 2.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Geheugenatelier',
                'titel_fr' => 'Atelier de Mémoire',
                'dag_van_de_week' => 1,
                'startuur' => '13:30:00',
                'einduur' => '15:15:00',
                'locatie' => 'De Harmonie',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Stoel-gym met Nicole',
                'titel_fr' => 'Gym sur chaise avec Nicole',
                'dag_van_de_week' => 1,
                'startuur' => '11:00:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Digitale workshop',
                'titel_fr' => 'Atelier Numérique',
                'dag_van_de_week' => 3,
                'startuur' => '14:00:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Bingo',
                'titel_fr' => 'Bingo',
                'dag_van_de_week' => 3,
                'startuur' => '13:30:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Creativiteit workshop',
                'titel_fr' => 'Atelier de Créativité',
                'dag_van_de_week' => 1,
                'startuur' => '14:00:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Zumba',
                'titel_fr' => 'Zumba',
                'dag_van_de_week' => 5,
                'startuur' => '14:00:00',
                'einduur' => '15:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Diamond Painting met Nadia',
                'titel_fr' => 'Atelier de Diamond Painting avec Nadia',
                'dag_van_de_week' => 5,
                'startuur' => '14:00:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Naaiworkshop',
                'titel_fr' => 'Atelier de Couture',
                'dag_van_de_week' => 3,
                'startuur' => '13:30:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
            [
                'titel_nl' => 'Boodschappendienst',
                'titel_fr' => 'Service de Courses',
                'dag_van_de_week' => 1,
                'startuur' => '14:00:00',
                'einduur' => null,
                'locatie' => 'De Harmonie',
                'prijs' => 2.50,
                'interesse' => Interesse::Diensten,
            ],
            [
                'titel_nl' => 'Pilates & Fitness',
                'titel_fr' => 'Pilates & Fitness',
                'dag_van_de_week' => 5,
                'startuur' => '11:00:00',
                'einduur' => null,
                'locatie' => 'Pôle Nord',
                'prijs' => 1.00,
                'interesse' => Interesse::Activiteiten,
            ],
        ];

        foreach ($templates as $data) {
            ActiviteitTemplate::create(array_merge($data, [
                'reeks_start' => $start,
                'reeks_einde' => $einde,
            ]));
        }

        $this->command->info('Seeded '.count($templates).' activity templates.');
    }
}
```

- [ ] **Step 2: Run the tests — confirm they pass**

```bash
php artisan test --compact tests/Feature/ActiviteitTemplateSeederTest.php
```

Expected: 5 passed.

- [ ] **Step 3: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

Expected: `{"result":"pass"}`

- [ ] **Step 4: Commit**

```bash
git add database/seeders/ActiviteitTemplateSeeder.php tests/Feature/ActiviteitTemplateSeederTest.php
git commit -m "feat: seed 15 activity templates from recurring series data"
```

---

## Task 3: Wire into `DatabaseSeeder`

**Files:**
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Add `ActiviteitTemplateSeeder` before `ActiviteitSeeder`**

Current `DatabaseSeeder::run()`:
```php
$this->call([
    AdminUserSeeder::class,
    ActiviteitSeeder::class,
]);
```

Updated:
```php
$this->call([
    AdminUserSeeder::class,
    ActiviteitTemplateSeeder::class,
    ActiviteitSeeder::class,
]);
```

- [ ] **Step 2: Verify the full seeder pipeline runs cleanly**

```bash
php artisan migrate:fresh --seed
```

Expected output includes:
```
Seeded 15 activity templates.
Imported 662 activities from CSV.
```

- [ ] **Step 3: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests pass (no regressions).

- [ ] **Step 4: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 5: Commit**

```bash
git add database/seeders/DatabaseSeeder.php
git commit -m "feat: wire ActiviteitTemplateSeeder into DatabaseSeeder"
```
