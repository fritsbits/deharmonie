# Week Menu Database Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace `resources/data/weekmenu.json` with a `weekmenu_dagen` database table, expose it through a Filament admin resource, and update all consumers (Livewire component, PageController, blade templates) to query the DB.

**Architecture:** A single `WeekMenuDag` Eloquent model (table `weekmenu_dagen`) with locale-aware accessors following the `Activiteit` pattern. The Livewire `WeekMenu` component and the `PageController::weekmenuPrint` method swap their JSON reads for targeted Eloquent queries. A Filament resource provides the admin UI for creating and editing menu days.

**Tech Stack:** Laravel 13, Livewire 3, Filament 4, SQLite (tests), MySQL (production), PHPUnit 12

---

## File Map

| Action | File | Purpose |
|---|---|---|
| Create | `database/migrations/2026_03_30_xxxxxx_create_weekmenu_dagen_table.php` | Table schema |
| Create | `app/Models/WeekMenuDag.php` | Model with locale-aware accessors |
| Create | `database/factories/WeekMenuDagFactory.php` | Factory with `closed()` and `specialEvent()` states |
| Create | `tests/Unit/WeekMenuDagTest.php` | Unit tests for model accessors |
| Modify | `lang/nl/weekmenu.php` | Add `soup_default` key |
| Modify | `lang/fr/weekmenu.php` | Add `soup_default` key |
| Create | `database/seeders/WeekMenuDagSeeder.php` | Import JSON data into DB |
| Modify | `database/seeders/DatabaseSeeder.php` | Register the seeder |
| Modify | `app/Livewire/WeekMenu.php` | Replace JSON reads with DB queries |
| Modify | `tests/Feature/WeekMenuTest.php` | Use factory + RefreshDatabase |
| Modify | `resources/views/livewire/week-menu.blade.php` | Use model attributes |
| Modify | `app/Http/Controllers/PageController.php` | Replace JSON read with DB query |
| Modify | `resources/views/pages/weekmenu-print.blade.php` | Use model attributes |
| Modify | `tests/Feature/WeekMenuPrintTest.php` | Use factory + RefreshDatabase |
| Create | `app/Filament/Resources/WeekMenuDagResource.php` | Filament resource |
| Create | `app/Filament/Resources/WeekMenuDagResource/Pages/ListWeekMenuDagen.php` | List page |
| Create | `app/Filament/Resources/WeekMenuDagResource/Pages/CreateWeekMenuDag.php` | Create page |
| Create | `app/Filament/Resources/WeekMenuDagResource/Pages/EditWeekMenuDag.php` | Edit page |

---

## Task 1: Migration, Model, Factory, Unit Tests

**Files:**
- Create: `database/migrations/2026_03_30_xxxxxx_create_weekmenu_dagen_table.php`
- Create: `app/Models/WeekMenuDag.php`
- Create: `database/factories/WeekMenuDagFactory.php`
- Create: `tests/Unit/WeekMenuDagTest.php`

- [ ] **Step 1: Write the failing unit tests**

Create `tests/Unit/WeekMenuDagTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Models\WeekMenuDag;
use Tests\TestCase;

class WeekMenuDagTest extends TestCase
{
    protected function tearDown(): void
    {
        app()->setLocale('nl');
        parent::tearDown();
    }

    public function test_main_accessor_returns_nl_by_default(): void
    {
        app()->setLocale('nl');
        $dag = new WeekMenuDag(['main_nl' => 'Stoofvlees', 'main_fr' => 'Carbonnade']);

        $this->assertEquals('Stoofvlees', $dag->main);
    }

    public function test_main_accessor_returns_fr(): void
    {
        app()->setLocale('fr');
        $dag = new WeekMenuDag(['main_nl' => 'Stoofvlees', 'main_fr' => 'Carbonnade']);

        $this->assertEquals('Carbonnade', $dag->main);
    }

    public function test_event_label_accessor_returns_nl(): void
    {
        app()->setLocale('nl');
        $dag = new WeekMenuDag(['event_label_nl' => 'Paasmenu', 'event_label_fr' => 'Menu de Pâques']);

        $this->assertEquals('Paasmenu', $dag->event_label);
    }

    public function test_event_label_accessor_returns_fr(): void
    {
        app()->setLocale('fr');
        $dag = new WeekMenuDag(['event_label_nl' => 'Paasmenu', 'event_label_fr' => 'Menu de Pâques']);

        $this->assertEquals('Menu de Pâques', $dag->event_label);
    }

    public function test_courses_for_locale_returns_flat_nl_array(): void
    {
        app()->setLocale('nl');
        $dag = new WeekMenuDag();
        $dag->courses = [
            ['nl' => 'Scampi met look', 'fr' => "Scampi à l'Ail"],
            ['nl' => 'Eendenborst', 'fr' => 'Magret de Canard'],
        ];

        $this->assertEquals(['Scampi met look', 'Eendenborst'], $dag->coursesForLocale);
    }

    public function test_courses_for_locale_returns_flat_fr_array(): void
    {
        app()->setLocale('fr');
        $dag = new WeekMenuDag();
        $dag->courses = [
            ['nl' => 'Scampi met look', 'fr' => "Scampi à l'Ail"],
            ['nl' => 'Eendenborst', 'fr' => 'Magret de Canard'],
        ];

        $this->assertEquals(["Scampi à l'Ail", 'Magret de Canard'], $dag->coursesForLocale);
    }

    public function test_courses_for_locale_returns_empty_array_when_null(): void
    {
        $dag = new WeekMenuDag(['courses' => null]);

        $this->assertEquals([], $dag->coursesForLocale);
    }

    public function test_type_is_gesloten_when_closed(): void
    {
        $dag = new WeekMenuDag(['closed' => true, 'special_event' => false]);

        $this->assertEquals('Gesloten', $dag->type);
    }

    public function test_type_is_speciaal_for_special_event(): void
    {
        $dag = new WeekMenuDag(['closed' => false, 'special_event' => true]);

        $this->assertEquals('Speciaal', $dag->type);
    }

    public function test_type_is_normaal_for_open_day(): void
    {
        $dag = new WeekMenuDag(['closed' => false, 'special_event' => false]);

        $this->assertEquals('Normaal', $dag->type);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test --compact tests/Unit/WeekMenuDagTest.php
```

Expected: FAIL — `App\Models\WeekMenuDag` not found.

- [ ] **Step 3: Create the migration**

```bash
php artisan make:migration create_weekmenu_dagen_table --no-interaction
```

Open the generated file and replace its `up()` and `down()` with:

```php
public function up(): void
{
    Schema::create('weekmenu_dagen', function (Blueprint $table) {
        $table->id();
        $table->date('date')->unique();
        $table->boolean('closed')->default(false);
        $table->boolean('special_event')->default(false);
        $table->unsignedSmallInteger('price')->nullable();
        $table->string('main_nl')->nullable();
        $table->string('main_fr')->nullable();
        $table->string('event_label_nl')->nullable();
        $table->string('event_label_fr')->nullable();
        $table->json('courses')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('weekmenu_dagen');
}
```

- [ ] **Step 4: Create the model**

```bash
php artisan make:model WeekMenuDag --no-interaction
```

Replace the generated `app/Models/WeekMenuDag.php` with:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeekMenuDag extends Model
{
    use HasFactory;

    protected $table = 'weekmenu_dagen';

    protected $fillable = [
        'date',
        'closed',
        'special_event',
        'price',
        'main_nl',
        'main_fr',
        'event_label_nl',
        'event_label_fr',
        'courses',
    ];

    protected $casts = [
        'date' => 'date',
        'closed' => 'boolean',
        'special_event' => 'boolean',
        'courses' => 'array',
    ];

    public function getMainAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->main_fr : $this->main_nl;
    }

    public function getEventLabelAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->event_label_fr : $this->event_label_nl;
    }

    public function getCoursesForLocaleAttribute(): array
    {
        if (empty($this->courses)) {
            return [];
        }
        $locale = app()->getLocale();

        return array_column($this->courses, $locale);
    }

    public function getTypeAttribute(): string
    {
        if ($this->closed) {
            return 'Gesloten';
        }
        if ($this->special_event) {
            return 'Speciaal';
        }

        return 'Normaal';
    }
}
```

- [ ] **Step 5: Create the factory**

```bash
php artisan make:factory WeekMenuDagFactory --no-interaction
```

Replace `database/factories/WeekMenuDagFactory.php` with:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WeekMenuDagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => $this->faker->unique()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'closed' => false,
            'special_event' => false,
            'price' => 9,
            'main_nl' => $this->faker->sentence(3),
            'main_fr' => $this->faker->sentence(3),
            'event_label_nl' => null,
            'event_label_fr' => null,
            'courses' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state([
            'closed' => true,
            'special_event' => false,
            'price' => null,
            'main_nl' => null,
            'main_fr' => null,
        ]);
    }

    public function specialEvent(): static
    {
        return $this->state([
            'closed' => false,
            'special_event' => true,
            'price' => 20,
            'main_nl' => null,
            'main_fr' => null,
            'event_label_nl' => $this->faker->words(2, true),
            'event_label_fr' => $this->faker->words(2, true),
            'courses' => [
                ['nl' => $this->faker->words(2, true), 'fr' => $this->faker->words(2, true)],
                ['nl' => $this->faker->words(2, true), 'fr' => $this->faker->words(2, true)],
            ],
        ]);
    }
}
```

- [ ] **Step 6: Run tests to verify they pass**

```bash
php artisan test --compact tests/Unit/WeekMenuDagTest.php
```

Expected: 10 passed.

- [ ] **Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 8: Commit**

```bash
git add database/migrations/ app/Models/WeekMenuDag.php database/factories/WeekMenuDagFactory.php tests/Unit/WeekMenuDagTest.php
git commit -m "feat: add WeekMenuDag model, migration, and factory"
```

---

## Task 2: Lang Keys + Seeder

**Files:**
- Modify: `lang/nl/weekmenu.php`
- Modify: `lang/fr/weekmenu.php`
- Create: `database/seeders/WeekMenuDagSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Add `soup_default` to the NL lang file**

In `lang/nl/weekmenu.php`, add after `'allergen_note'`:

```php
'soup_default' => 'Soep van de dag',
```

- [ ] **Step 2: Add `soup_default` to the FR lang file**

In `lang/fr/weekmenu.php`, add after `'allergen_note'`:

```php
'soup_default' => 'Potage du jour',
```

- [ ] **Step 3: Create the seeder**

```bash
php artisan make:seeder WeekMenuDagSeeder --no-interaction
```

Replace `database/seeders/WeekMenuDagSeeder.php` with:

```php
<?php

namespace Database\Seeders;

use App\Models\WeekMenuDag;
use Illuminate\Database\Seeder;

class WeekMenuDagSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(resource_path('data/weekmenu.json')), true);

        foreach ($data['days'] as $day) {
            WeekMenuDag::updateOrCreate(
                ['date' => $day['date']],
                [
                    'closed' => $day['closed'],
                    'special_event' => $day['special_event'],
                    'price' => $day['price'],
                    'main_nl' => $day['nl']['main'] ?? null,
                    'main_fr' => $day['fr']['main'] ?? null,
                    'event_label_nl' => $day['nl']['event_label'] ?? null,
                    'event_label_fr' => $day['fr']['event_label'] ?? null,
                    'courses' => $this->buildCourses($day),
                ]
            );
        }
    }

    private function buildCourses(array $day): ?array
    {
        $nl = $day['nl']['courses'] ?? [];
        $fr = $day['fr']['courses'] ?? [];

        if (empty($nl) && empty($fr)) {
            return null;
        }

        $courses = [];
        $count = max(count($nl), count($fr));
        for ($i = 0; $i < $count; $i++) {
            $courses[] = [
                'nl' => $nl[$i] ?? '',
                'fr' => $fr[$i] ?? '',
            ];
        }

        return $courses;
    }
}
```

- [ ] **Step 4: Register the seeder in `DatabaseSeeder`**

In `database/seeders/DatabaseSeeder.php`, add `WeekMenuDagSeeder::class` to the array:

```php
public function run(): void
{
    $this->call([
        AdminUserSeeder::class,
        ActiviteitTemplateSeeder::class,
        ActiviteitSeeder::class,
        WeekMenuDagSeeder::class,
    ]);
}
```

- [ ] **Step 5: Run migration and seeder**

```bash
php artisan migrate
php artisan db:seed --class=WeekMenuDagSeeder
```

Expected: No errors. Verify with:

```bash
php artisan tinker --execute 'echo App\Models\WeekMenuDag::count() . " rows\n";'
```

Expected: prints the number of days in the JSON (12 rows for the current data).

- [ ] **Step 6: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 7: Commit**

```bash
git add lang/nl/weekmenu.php lang/fr/weekmenu.php database/seeders/WeekMenuDagSeeder.php database/seeders/DatabaseSeeder.php
git commit -m "feat: add soup_default lang keys and WeekMenuDagSeeder"
```

---

## Task 3: Update WeekMenu Livewire Component + blade + Tests

**Files:**
- Modify: `app/Livewire/WeekMenu.php`
- Modify: `resources/views/livewire/week-menu.blade.php`
- Modify: `tests/Feature/WeekMenuTest.php`

- [ ] **Step 1: Write the updated WeekMenuTest**

Replace the entire `tests/Feature/WeekMenuTest.php` with:

```php
<?php

namespace Tests\Feature;

use App\Livewire\WeekMenu;
use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WeekMenuTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        app()->setLocale('nl');
        parent::tearDown();
    }

    public function test_weekmenu_page_loads_in_nl(): void
    {
        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Menu deze week');
        $response->assertSee('Openingsuren');
        $response->assertSee('Gewoon binnenlopen');
    }

    public function test_weekmenu_page_loads_in_fr(): void
    {
        $response = $this->get('/fr/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Menu de cette semaine');
        $response->assertSee("Heures d'ouverture");
        $response->assertSee('Entrez librement');
    }

    public function test_today_card_is_highlighted_before_cutoff(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create([
            'date' => '2026-03-23',
            'main_nl' => 'Stoofvlees met Sla en Kroketjes',
            'main_fr' => 'Carbonnades, Frites et Salade',
        ]);

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Vandaag');
        $response->assertSee('Stoofvlees met Sla en Kroketjes');
    }

    public function test_tomorrow_card_is_highlighted_after_cutoff(): void
    {
        Carbon::setTestNow('2026-03-23 15:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23', 'main_nl' => 'Stoofvlees met Sla en Kroketjes']);
        WeekMenuDag::factory()->create(['date' => '2026-03-24', 'main_nl' => 'Chicon Gratin met Puree']);

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Morgen');
        $response->assertSee('Chicon Gratin met Puree');
    }

    public function test_closed_day_is_not_shown(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);
        WeekMenuDag::factory()->closed()->create(['date' => '2026-03-28']);

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertDontSee('Gesloten'); // closed days are filtered out of the live component entirely
    }

    public function test_special_event_shows_all_courses(): void
    {
        Carbon::setTestNow('2026-04-01 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-04-01', 'main_nl' => 'Soep dag']);
        WeekMenuDag::factory()->create([
            'date' => '2026-04-02',
            'special_event' => true,
            'price' => 20,
            'main_nl' => null,
            'main_fr' => null,
            'event_label_nl' => 'Paasmenu',
            'event_label_fr' => 'Menu de Pâques',
            'courses' => [
                ['nl' => 'Kir Royal', 'fr' => 'Kir Royal'],
                ['nl' => 'Eendenborst', 'fr' => 'Magret de Canard'],
            ],
        ]);

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Paasmenu');
        $response->assertSee('Kir Royal');
        $response->assertSee('Eendenborst');
        $response->assertSee('€ 20');
    }

    public function test_closed_day_is_skipped_when_resolving_highlighted_date(): void
    {
        Carbon::setTestNow('2026-03-27 15:00:00');
        WeekMenuDag::factory()->closed()->create(['date' => '2026-03-28']);
        WeekMenuDag::factory()->create(['date' => '2026-03-30', 'main_nl' => 'Kalf blanket met Bulgur']);

        $response = $this->get('/restaurant-menu');

        $response->assertStatus(200);
        $response->assertSee('Kalf blanket met Bulgur');
    }

    public function test_week_menu_component_shows_print_link_in_nl(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);

        Livewire::test(WeekMenu::class)
            ->assertSee('Druk af')
            ->assertSee('restaurant-menu/print');
    }

    public function test_week_menu_component_shows_print_link_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        app()->setLocale('fr');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);

        Livewire::test(WeekMenu::class)
            ->assertSee('Imprimer')
            ->assertSee('fr/restaurant-menu/print');
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test --compact tests/Feature/WeekMenuTest.php
```

Expected: FAIL — tests that create factory records will error because the component still reads from JSON.

- [ ] **Step 3: Update `app/Livewire/WeekMenu.php`**

Replace the entire file with:

```php
<?php

namespace App\Livewire;

use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class WeekMenu extends Component
{
    public int $weekOffset = 0;

    public function mount(): void
    {
        $now = Carbon::now();
        $candidate = $now->hour >= 14 ? $now->copy()->addDay() : $now->copy();

        $day = WeekMenuDag::where('closed', false)
            ->where('date', '>=', $candidate->toDateString())
            ->orderBy('date')
            ->first();

        if ($day) {
            $highlightedWeekStart = $day->date->startOfWeek(Carbon::MONDAY);
            $currentWeekStart = $now->copy()->startOfWeek(Carbon::MONDAY);
            $this->weekOffset = (int) ($currentWeekStart->diffInDays($highlightedWeekStart) / 7);
        }
    }

    private function weekStart(int $offset): Carbon
    {
        return Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($offset);
    }

    #[Computed]
    public function days(): Collection
    {
        $weekStart = $this->weekStart($this->weekOffset);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        return WeekMenuDag::where('closed', false)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('date')
            ->get();
    }

    #[Computed]
    public function weekHeading(): string
    {
        if ($this->weekOffset === 0) {
            return __('weekmenu.this_week');
        }
        if ($this->weekOffset === 1) {
            return __('weekmenu.next_week');
        }
        if ($this->weekOffset === -1) {
            return __('weekmenu.prev_week');
        }

        return $this->weekLabel;
    }

    #[Computed]
    public function weekLabel(): string
    {
        $locale = app()->getLocale();
        $open = $this->days;

        if ($open->isEmpty()) {
            $ws = $this->weekStart($this->weekOffset);

            return $ws->locale($locale)->isoFormat('D MMM')
                .' – '
                .$ws->copy()->endOfWeek(Carbon::SUNDAY)->locale($locale)->isoFormat('D MMM YYYY');
        }

        $first = $open->first()->date->locale($locale);
        $last = $open->last()->date->locale($locale);

        if ($first->month === $last->month) {
            return $first->isoFormat('D').' – '.$last->isoFormat('D MMMM YYYY');
        }

        return $first->isoFormat('D MMMM').' – '.$last->isoFormat('D MMMM YYYY');
    }

    #[Computed]
    public function highlightedDate(): ?string
    {
        $now = Carbon::now();
        $candidate = $now->hour >= 14 ? $now->copy()->addDay() : $now->copy();

        return WeekMenuDag::where('closed', false)
            ->where('date', '>=', $candidate->toDateString())
            ->orderBy('date')
            ->value('date');
    }

    #[Computed]
    public function highlightedIsToday(): bool
    {
        return $this->highlightedDate !== null
            && $this->highlightedDate === Carbon::now()->toDateString();
    }

    #[Computed]
    public function highlightedIsTomorrow(): bool
    {
        return $this->highlightedDate !== null
            && $this->highlightedDate === Carbon::now()->addDay()->toDateString();
    }

    #[Computed]
    public function hasPrev(): bool
    {
        $prevStart = $this->weekStart($this->weekOffset - 1);
        $prevEnd = $prevStart->copy()->endOfWeek(Carbon::SUNDAY);

        return WeekMenuDag::where('closed', false)
            ->whereBetween('date', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->exists();
    }

    #[Computed]
    public function hasNext(): bool
    {
        $nextStart = $this->weekStart($this->weekOffset + 1);
        $nextEnd = $nextStart->copy()->endOfWeek(Carbon::SUNDAY);

        return WeekMenuDag::where('closed', false)
            ->whereBetween('date', [$nextStart->toDateString(), $nextEnd->toDateString()])
            ->exists();
    }

    public function prevWeek(): void
    {
        if ($this->hasPrev) {
            $this->weekOffset--;
        }
    }

    public function nextWeek(): void
    {
        if ($this->hasNext) {
            $this->weekOffset++;
        }
    }

    public function render(): View
    {
        return view('livewire.week-menu');
    }
}
```

- [ ] **Step 4: Update `resources/views/livewire/week-menu.blade.php`**

In the blade, find all occurrences of array-style access and replace with model property access. The changes are:

**In the `@php` block** (around line 33), replace:
```php
$carbon        = \Carbon\Carbon::parse($day['date'])->locale($locale);
$isPast        = $carbon->lt(\Carbon\Carbon::today());
$isHighlighted = $this->highlightedDate && $day['date'] === $this->highlightedDate;
$dateNum       = $carbon->day;
$monthAbbr     = $carbon->isoFormat('MMM');
```
with:
```php
$carbon        = $day->date->locale($locale);
$isPast        = $carbon->lt(\Carbon\Carbon::today());
$isHighlighted = $this->highlightedDate && $day->date->toDateString() === $this->highlightedDate;
$dateNum       = $carbon->day;
$monthAbbr     = $carbon->isoFormat('MMM');
```

**Special event check** (line 66), replace:
```php
@if ($day['special_event'])
```
with:
```php
@if ($day->special_event)
```

**Special event content** — replace all `$day[$locale][...]` and `$day[...]` with model attributes:
```php
{{-- Old --}}
$day['price']                → $day->price
$day[$locale]['event_label'] → $day->event_label
$day[$locale]['courses']     → $day->coursesForLocale
$day[$locale]['main']        → $day->main
```

**Special event price** (inside special event block), replace:
```php
<p style="...">€ {{ $day['price'] }}</p>
```
with:
```php
<p style="...">€ {{ $day->price }}</p>
```

**Special event label**, replace:
```php
<p style="...">{{ $day[$locale]['event_label'] }}</p>
```
with:
```php
<p style="...">{{ $day->event_label }}</p>
```

**Courses loop**, replace:
```php
@foreach ($day[$locale]['courses'] as $course)
```
with:
```php
@foreach ($day->coursesForLocale as $course)
```

**Standard day main dish**, replace:
```php
<p style="...font-size: 1.5rem...">{{ $day[$locale]['main'] }}</p>
<p style="...">€&thinsp;{{ $day['price'] }}</p>
```
with:
```php
<p style="...font-size: 1.5rem...">{{ $day->main }}</p>
<p style="...">€&thinsp;{{ $day->price }}</p>
```

Also replace `$isPast ? 'opacity: 0.45;' : ''` in the special event row — this already uses the computed `$isPast` variable, which is now correctly calculated from `$day->date->locale($locale)`, so no change needed there.

- [ ] **Step 5: Run tests**

```bash
php artisan test --compact tests/Feature/WeekMenuTest.php
```

Expected: all tests pass.

- [ ] **Step 6: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 7: Commit**

```bash
git add app/Livewire/WeekMenu.php resources/views/livewire/week-menu.blade.php tests/Feature/WeekMenuTest.php
git commit -m "feat: migrate WeekMenu component and blade from JSON to database"
```

---

## Task 4: Update PageController + Print Blade + Tests

**Files:**
- Modify: `app/Http/Controllers/PageController.php`
- Modify: `resources/views/pages/weekmenu-print.blade.php`
- Modify: `tests/Feature/WeekMenuPrintTest.php`

- [ ] **Step 1: Write the updated WeekMenuPrintTest**

Replace the entire `tests/Feature/WeekMenuPrintTest.php` with:

```php
<?php

namespace Tests\Feature;

use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeekMenuPrintTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        app()->setLocale('nl');
        parent::tearDown();
    }

    public function test_print_route_loads_in_nl(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
    }

    public function test_print_route_loads_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
    }

    public function test_print_view_shows_nl_content(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create([
            'date' => '2026-03-23',
            'main_nl' => 'Stoofvlees met Sla en Kroketjes',
            'main_fr' => 'Carbonnades, Frites et Salade',
        ]);

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Stoofvlees met Sla en Kroketjes');
        $response->assertSee('Soep van de dag');
    }

    public function test_print_view_shows_fr_content(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create([
            'date' => '2026-03-23',
            'main_nl' => 'Stoofvlees met Sla en Kroketjes',
            'main_fr' => 'Carbonnades, Frites et Salade',
        ]);

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Carbonnades, Frites et Salade');
        $response->assertSee('Potage du jour');
    }

    public function test_print_view_shows_closed_day(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);
        WeekMenuDag::factory()->closed()->create(['date' => '2026-03-28']);

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Gesloten');
    }

    public function test_print_view_shows_closed_day_in_fr(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);
        WeekMenuDag::factory()->closed()->create(['date' => '2026-03-28']);

        $response = $this->get('/fr/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertSee('Fermé');
    }

    public function test_print_view_responds_for_next_week(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create([
            'date' => '2026-03-30',
            'main_nl' => 'Kalf blanket met Bulgur',
        ]);

        $response = $this->get('/restaurant-menu/print?week=1');

        $response->assertStatus(200);
        $response->assertSee('Kalf blanket met Bulgur');
    }

    public function test_print_view_does_not_contain_nav(): void
    {
        Carbon::setTestNow('2026-03-23 10:00:00');
        WeekMenuDag::factory()->create(['date' => '2026-03-23']);

        $response = $this->get('/restaurant-menu/print?week=0');

        $response->assertStatus(200);
        $response->assertDontSee('<nav', false);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test --compact tests/Feature/WeekMenuPrintTest.php
```

Expected: FAIL — controller still reads from JSON.

- [ ] **Step 3: Update `PageController::weekmenuPrint`**

In `app/Http/Controllers/PageController.php`, add the import at the top:
```php
use App\Models\WeekMenuDag;
```

Replace the `weekmenuPrint` method with:

```php
public function weekmenuPrint(Request $request): View
{
    $weekOffset = (int) $request->query('week', 0);
    $locale = app()->getLocale();

    $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
    $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

    $days = WeekMenuDag::whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
        ->orderBy('date')
        ->get();

    if ($days->isNotEmpty()) {
        $first = $days->first()->date->locale($locale);
        $last = $days->last()->date->locale($locale);
        $weekLabel = $first->month === $last->month
            ? $first->isoFormat('D').' – '.$last->isoFormat('D MMMM YYYY')
            : $first->isoFormat('D MMMM').' – '.$last->isoFormat('D MMMM YYYY');
    } else {
        $weekLabel = $weekStart->locale($locale)->isoFormat('D MMM')
            .' – '
            .$weekEnd->locale($locale)->isoFormat('D MMM YYYY');
    }

    return view('pages.weekmenu-print', compact('days', 'weekLabel', 'locale'));
}
```

- [ ] **Step 4: Update `resources/views/pages/weekmenu-print.blade.php`**

In the `@php` block inside the `@forelse` loop, replace:
```php
$carbon = \Carbon\Carbon::parse($day['date'])->locale($locale);
```
with:
```php
$carbon = $day->date->locale($locale);
```

Replace the closed check:
```php
@if ($day['closed'])
```
with:
```php
@if ($day->closed)
```

Replace the closed label:
```php
<p class="day-closed-label">{{ $day['closed_label_' . $locale] ?? __('weekmenu.closed') }}</p>
```
with:
```php
<p class="day-closed-label">{{ __('weekmenu.closed') }}</p>
```

Replace the special event check:
```php
@elseif ($day['special_event'])
```
with:
```php
@elseif ($day->special_event)
```

Replace special event content (inside the `@elseif ($day->special_event)` block):
```php
{{-- Old --}}
<p class="day-event-label">{{ $day[$locale]['event_label'] }}</p>
<p class="day-price">€ {{ $day['price'] }}</p>
@foreach ($day[$locale]['courses'] as $course)

{{-- New --}}
<p class="day-event-label">{{ $day->event_label }}</p>
<p class="day-price">€ {{ $day->price }}</p>
@foreach ($day->coursesForLocale as $course)
```

Replace standard day content (inside the final `@else` block):
```php
{{-- Old --}}
@if ($day[$locale]['soup'])
    <p class="day-soup">{{ $day[$locale]['soup'] }}</p>
@endif
<p class="day-main">{{ $day[$locale]['main'] }}</p>
<p class="day-price">€&thinsp;{{ $day['price'] }}</p>

{{-- New --}}
<p class="day-soup">{{ __('weekmenu.soup_default') }}</p>
<p class="day-main">{{ $day->main }}</p>
<p class="day-price">€&thinsp;{{ $day->price }}</p>
```

- [ ] **Step 5: Run tests**

```bash
php artisan test --compact tests/Feature/WeekMenuPrintTest.php
```

Expected: all tests pass.

- [ ] **Step 6: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests pass.

- [ ] **Step 7: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/PageController.php resources/views/pages/weekmenu-print.blade.php tests/Feature/WeekMenuPrintTest.php
git commit -m "feat: migrate PageController weekmenuPrint and print blade from JSON to database"
```

---

## Task 5: Filament Resource

**Files:**
- Create: `app/Filament/Resources/WeekMenuDagResource.php`
- Create: `app/Filament/Resources/WeekMenuDagResource/Pages/ListWeekMenuDagen.php`
- Create: `app/Filament/Resources/WeekMenuDagResource/Pages/CreateWeekMenuDag.php`
- Create: `app/Filament/Resources/WeekMenuDagResource/Pages/EditWeekMenuDag.php`

No automated tests for Filament resources — consistent with the existing `ActiviteitResource` pattern. Verify manually in the browser.

- [ ] **Step 1: Create the pages directory**

```bash
mkdir -p app/Filament/Resources/WeekMenuDagResource/Pages
```

- [ ] **Step 2: Create the List page**

Create `app/Filament/Resources/WeekMenuDagResource/Pages/ListWeekMenuDagen.php`:

```php
<?php

namespace App\Filament\Resources\WeekMenuDagResource\Pages;

use App\Filament\Resources\WeekMenuDagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeekMenuDagen extends ListRecords
{
    protected static string $resource = WeekMenuDagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
```

- [ ] **Step 3: Create the Create page**

Create `app/Filament/Resources/WeekMenuDagResource/Pages/CreateWeekMenuDag.php`:

```php
<?php

namespace App\Filament\Resources\WeekMenuDagResource\Pages;

use App\Filament\Resources\WeekMenuDagResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWeekMenuDag extends CreateRecord
{
    protected static string $resource = WeekMenuDagResource::class;
}
```

- [ ] **Step 4: Create the Edit page**

Create `app/Filament/Resources/WeekMenuDagResource/Pages/EditWeekMenuDag.php`:

```php
<?php

namespace App\Filament\Resources\WeekMenuDagResource\Pages;

use App\Filament\Resources\WeekMenuDagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeekMenuDag extends EditRecord
{
    protected static string $resource = WeekMenuDagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
```

- [ ] **Step 5: Create the Resource**

Create `app/Filament/Resources/WeekMenuDagResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeekMenuDagResource\Pages;
use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WeekMenuDagResource extends Resource
{
    protected static ?string $model = WeekMenuDag::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Weekmenu';

    protected static ?string $modelLabel = 'Menudag';

    protected static ?string $pluralModelLabel = 'Menudagen';

    protected static ?string $slug = 'weekmenu';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('date')
                ->label('Datum')
                ->required()
                ->unique(ignoreRecord: true),

            Toggle::make('closed')
                ->label('Gesloten')
                ->live()
                ->default(false),

            Toggle::make('special_event')
                ->label('Speciaal menu')
                ->live()
                ->default(false)
                ->hidden(fn (Get $get): bool => (bool) $get('closed')),

            TextInput::make('main_nl')
                ->label('Gerecht (NL)')
                ->required()
                ->hidden(fn (Get $get): bool => (bool) $get('closed') || (bool) $get('special_event')),

            TextInput::make('main_fr')
                ->label('Plat (FR)')
                ->required()
                ->hidden(fn (Get $get): bool => (bool) $get('closed') || (bool) $get('special_event')),

            TextInput::make('price')
                ->label('Prijs (€)')
                ->numeric()
                ->required()
                ->prefix('€')
                ->hidden(fn (Get $get): bool => (bool) $get('closed')),

            TextInput::make('event_label_nl')
                ->label('Naam speciaal menu (NL)')
                ->required()
                ->hidden(fn (Get $get): bool => ! (bool) $get('special_event')),

            TextInput::make('event_label_fr')
                ->label('Nom menu spécial (FR)')
                ->required()
                ->hidden(fn (Get $get): bool => ! (bool) $get('special_event')),

            Repeater::make('courses')
                ->label('Gangen')
                ->schema([
                    TextInput::make('nl')
                        ->label('Gang (NL)')
                        ->required(),
                    TextInput::make('fr')
                        ->label('Plat (FR)')
                        ->required(),
                ])
                ->columnSpanFull()
                ->hidden(fn (Get $get): bool => ! (bool) $get('special_event')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Datum')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Gesloten' => 'gray',
                        'Speciaal' => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('price')
                    ->label('Prijs')
                    ->formatStateUsing(fn ($state): string => $state ? '€ '.$state : '—'),
                Tables\Columns\TextColumn::make('main_nl')
                    ->label('Gerecht (NL)')
                    ->limit(50)
                    ->placeholder('—'),
            ])
            ->defaultSort('date', 'asc')
            ->filters([
                Tables\Filters\Filter::make('week')
                    ->form([
                        \Filament\Forms\Components\Select::make('week_range')
                            ->options([
                                'this' => 'Deze week',
                                'next' => 'Volgende week',
                            ])
                            ->placeholder('Alle weken'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['week_range'] ?? null) {
                            'this' => $query->whereBetween('date', [
                                Carbon::now()->startOfWeek()->toDateString(),
                                Carbon::now()->endOfWeek()->toDateString(),
                            ]),
                            'next' => $query->whereBetween('date', [
                                Carbon::now()->addWeek()->startOfWeek()->toDateString(),
                                Carbon::now()->addWeek()->endOfWeek()->toDateString(),
                            ]),
                            default => $query,
                        };
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWeekMenuDagen::route('/'),
            'create' => Pages\CreateWeekMenuDag::route('/create'),
            'edit' => Pages\EditWeekMenuDag::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 6: Verify in browser**

Visit `https://harmonie.test/admin/weekmenu` (log in if prompted).

Check:
- List view shows rows with date, type badge (Normaal/Gesloten/Speciaal), price, and NL dish
- "Diese week" / "Volgende week" filters work
- Create form: toggling "Gesloten" hides all other fields
- Create form: toggling "Speciaal menu" hides main_nl/main_fr and shows event_label + courses repeater
- Edit an existing row — data loads correctly

- [ ] **Step 7: Run the full test suite one final time**

```bash
php artisan test --compact
```

Expected: all tests pass.

- [ ] **Step 8: Run pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 9: Commit**

```bash
git add app/Filament/Resources/WeekMenuDagResource.php app/Filament/Resources/WeekMenuDagResource/
git commit -m "feat: add WeekMenuDag Filament resource"
```
