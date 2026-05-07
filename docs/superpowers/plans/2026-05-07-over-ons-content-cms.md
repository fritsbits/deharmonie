# Over ons content + Jaarverslag CMS Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the three impact stats and the linked yearly report on the `Over ons` page editable from the Filament admin, replacing hardcoded translation strings and a static PDF in `public/docs/`.

**Architecture:** A singleton Eloquent model `OverOnsContent` (one row, `id = 1`) holds the impact-stat numbers (string), bilingual descriptions, and the year. The PDF is attached via Spatie Media Library's `singleFile()` collection — first usage of media library in this codebase. A custom Filament 4 page (`ManageOverOnsContent` under a new `Inhoud` nav group) provides the edit form, following the official Filament 4 "Singular resource via custom page" pattern. The public Blade page reads via the controller and conditionally renders the jaarverslag card when a PDF is present.

**Tech Stack:** Laravel 13, Filament 4 (`Filament\Schemas\Schema`, `SpatieMediaLibraryFileUpload`), Livewire 3, Spatie Media Library v11, PHPUnit 12.

**Spec:** [`docs/superpowers/specs/2026-05-07-over-ons-content-cms-design.md`](../specs/2026-05-07-over-ons-content-cms-design.md)

---

## File Structure

**Created:**

- `database/migrations/2026_05_07_120000_create_over_ons_content_table.php` — schema
- `app/Models/OverOnsContent.php` — singleton model with `HasMedia`
- `database/factories/OverOnsContentFactory.php` — factory for tests
- `app/Filament/Pages/ManageOverOnsContent.php` — Filament page class (renamed from spec's `OverOnsContent` to avoid namespace clash with the model in `use` statements)
- `resources/views/filament/pages/manage-over-ons-content.blade.php` — page view
- `database/migrations/2026_05_07_120100_seed_over_ons_content.php` — one-off data migration that seeds the singleton + copies the existing PDF
- `tests/Feature/Filament/ManageOverOnsContentTest.php` — admin-page tests

**Modified:**

- `app/Http/Controllers/PageController.php` — `overOns()` passes the singleton to the view
- `resources/views/pages/over-ons.blade.php` — impact stats read from the model; jaarverslag block becomes conditional
- `lang/nl/pages.php` and `lang/fr/pages.php` — drop the now-DB-driven keys
- `tests/Feature/OverOnsPageTest.php` — add tests for dynamic stats + conditional jaarverslag

---

## Task 1: Singleton model, migration, factory

**Files:**
- Create: `database/migrations/2026_05_07_120000_create_over_ons_content_table.php`
- Create: `app/Models/OverOnsContent.php`
- Create: `database/factories/OverOnsContentFactory.php`
- Test: `tests/Unit/OverOnsContentTest.php`

- [ ] **Step 1: Create the migration**

```bash
php artisan make:migration create_over_ons_content_table --no-interaction
```

Replace the generated file contents (use the timestamp Laravel produced — keep it):

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('over_ons_content', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('jaarverslag_jaar')->nullable();
            $table->string('impact_1_aantal', 20);
            $table->string('impact_1_omschrijving_nl', 120);
            $table->string('impact_1_omschrijving_fr', 120);
            $table->string('impact_2_aantal', 20);
            $table->string('impact_2_omschrijving_nl', 120);
            $table->string('impact_2_omschrijving_fr', 120);
            $table->string('impact_3_aantal', 20);
            $table->string('impact_3_omschrijving_nl', 120);
            $table->string('impact_3_omschrijving_fr', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('over_ons_content');
    }
};
```

- [ ] **Step 2: Write the failing model test**

Create `tests/Unit/OverOnsContentTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Models\OverOnsContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverOnsContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_creates_singleton_when_missing(): void
    {
        $first = OverOnsContent::current();
        $second = OverOnsContent::current();

        $this->assertSame(1, $first->id);
        $this->assertSame(1, $second->id);
        $this->assertSame(1, OverOnsContent::count());
    }

    public function test_impact_omschrijving_returns_locale_specific_value(): void
    {
        $content = OverOnsContent::factory()->create([
            'impact_1_omschrijving_nl' => 'wekelijks bij ons',
            'impact_1_omschrijving_fr' => 'chaque semaine',
        ]);

        app()->setLocale('nl');
        $this->assertSame('wekelijks bij ons', $content->impactOmschrijving(1));

        app()->setLocale('fr');
        $this->assertSame('chaque semaine', $content->impactOmschrijving(1));
    }

    public function test_jaarverslag_url_is_null_without_media(): void
    {
        $content = OverOnsContent::factory()->create();

        $this->assertNull($content->getJaarverslagUrl());
        $this->assertNull($content->getJaarverslagSize());
    }
}
```

- [ ] **Step 3: Run the test to verify it fails**

Run: `php artisan test --compact --filter=OverOnsContentTest`
Expected: FAIL — class `App\Models\OverOnsContent` does not exist (or factory missing).

- [ ] **Step 4: Create the model**

Create `app/Models/OverOnsContent.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class OverOnsContent extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $table = 'over_ons_content';

    protected $fillable = [
        'jaarverslag_jaar',
        'impact_1_aantal', 'impact_1_omschrijving_nl', 'impact_1_omschrijving_fr',
        'impact_2_aantal', 'impact_2_omschrijving_nl', 'impact_2_omschrijving_fr',
        'impact_3_aantal', 'impact_3_omschrijving_nl', 'impact_3_omschrijving_fr',
    ];

    protected $casts = [
        'jaarverslag_jaar' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('jaarverslag')
            ->singleFile()
            ->acceptsMimeTypes(['application/pdf']);
    }

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'impact_1_aantal' => '0',
            'impact_1_omschrijving_nl' => '',
            'impact_1_omschrijving_fr' => '',
            'impact_2_aantal' => '0',
            'impact_2_omschrijving_nl' => '',
            'impact_2_omschrijving_fr' => '',
            'impact_3_aantal' => '0',
            'impact_3_omschrijving_nl' => '',
            'impact_3_omschrijving_fr' => '',
        ]);
    }

    public function impactOmschrijving(int $stat): string
    {
        $locale = app()->getLocale();
        $key = "impact_{$stat}_omschrijving_{$locale}";

        return (string) ($this->{$key} ?? '');
    }

    public function getJaarverslagUrl(): ?string
    {
        $url = $this->getFirstMediaUrl('jaarverslag');

        return $url !== '' ? $url : null;
    }

    public function getJaarverslagSize(): ?string
    {
        $bytes = $this->getFirstMedia('jaarverslag')?->size;

        return $bytes ? Number::fileSize($bytes, precision: 1) : null;
    }
}
```

- [ ] **Step 5: Create the factory**

Create `database/factories/OverOnsContentFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\OverOnsContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OverOnsContent>
 */
class OverOnsContentFactory extends Factory
{
    protected $model = OverOnsContent::class;

    public function definition(): array
    {
        return [
            'jaarverslag_jaar' => 2025,
            'impact_1_aantal' => '250',
            'impact_1_omschrijving_nl' => 'wekelijks bij ons over de vloer',
            'impact_1_omschrijving_fr' => 'chaque semaine chez nous',
            'impact_2_aantal' => '4500',
            'impact_2_omschrijving_nl' => 'maaltijden per maand',
            'impact_2_omschrijving_fr' => 'repas par mois',
            'impact_3_aantal' => '60+',
            'impact_3_omschrijving_nl' => 'activiteiten per jaar',
            'impact_3_omschrijving_fr' => 'activités par an',
        ];
    }
}
```

- [ ] **Step 6: Run the test to verify it passes**

Run: `php artisan test --compact --filter=OverOnsContentTest`
Expected: PASS — 3 tests, 9 assertions.

- [ ] **Step 7: Lint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/migrations/*_create_over_ons_content_table.php \
        app/Models/OverOnsContent.php \
        database/factories/OverOnsContentFactory.php \
        tests/Unit/OverOnsContentTest.php
git commit -m "feat(over-ons): add singleton OverOnsContent model with media

The model holds three impact stats (number + bilingual descriptions)
and a single-file PDF collection 'jaarverslag' for the yearly report."
```

---

## Task 2: Public page renders impact stats from the database

**Files:**
- Modify: `app/Http/Controllers/PageController.php` (the `overOns()` method)
- Modify: `resources/views/pages/over-ons.blade.php` (the three impact-stat blocks)
- Modify: `lang/nl/pages.php` (remove DB-driven keys)
- Modify: `lang/fr/pages.php` (remove DB-driven keys)
- Test: `tests/Feature/OverOnsPageTest.php` (extend existing)

- [ ] **Step 1: Add a failing test for dynamic stats rendering**

Open `tests/Feature/OverOnsPageTest.php` and:

(a) add `use App\Models\OverOnsContent;`, `use Illuminate\Foundation\Testing\RefreshDatabase;`, and `use RefreshDatabase;` inside the class;

(b) append these test methods:

```php
public function test_impact_stats_render_from_database_in_nl(): void
{
    OverOnsContent::factory()->create([
        'impact_1_aantal' => '777',
        'impact_1_omschrijving_nl' => 'mijn unieke NL omschrijving',
        'impact_2_aantal' => '888',
        'impact_3_aantal' => '999',
    ]);

    $response = $this->get(route('nl.over-ons'));

    $response->assertStatus(200);
    $response->assertSee('777');
    $response->assertSee('888');
    $response->assertSee('999');
    $response->assertSee('mijn unieke NL omschrijving');
}

public function test_impact_stats_render_locale_specific_descriptions_in_fr(): void
{
    OverOnsContent::factory()->create([
        'impact_1_omschrijving_fr' => 'ma description FR unique',
    ]);

    $response = $this->get(route('fr.over-ons'));

    $response->assertStatus(200);
    $response->assertSee('ma description FR unique');
}
```

- [ ] **Step 2: Run the new tests to verify they fail**

Run: `php artisan test --compact --filter='OverOnsPageTest::test_impact_stats'`
Expected: FAIL — the page shows the old hardcoded strings, not "777", "888", "999", or the unique seeded text.

- [ ] **Step 3: Update the controller**

Edit `app/Http/Controllers/PageController.php`. Add `use App\Models\OverOnsContent;` at the top and replace the existing `overOns()` method (around lines 45-48):

```php
public function overOns(): View
{
    return view('pages.over-ons', [
        'content' => OverOnsContent::current(),
    ]);
}
```

- [ ] **Step 4: Update the Blade view's impact stats**

Edit `resources/views/pages/over-ons.blade.php`. In the stats sidebar (around lines 41-58), replace the three `__('pages.over_ons_impact_N_number')` and `__('pages.over_ons_impact_N_desc')` calls. The label calls (`over_ons_impact_N_label`) stay unchanged.

Before (stat 1, around line 44-46):
```blade
<x-eyebrow size="sm" color="blue" mb="0.35rem">{{ __('pages.over_ons_impact_1_label') }}</x-eyebrow>
<div style="font-family: var(--font-sans); font-size: 2.75rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1; letter-spacing: -0.02em;">{{ __('pages.over_ons_impact_1_number') }}</div>
<p style="font-size: 0.9375rem; color: var(--color-brand-muted); margin: 0.25rem 0 0; line-height: 1.4;">{{ __('pages.over_ons_impact_1_desc') }}</p>
```

After:
```blade
<x-eyebrow size="sm" color="blue" mb="0.35rem">{{ __('pages.over_ons_impact_1_label') }}</x-eyebrow>
<div style="font-family: var(--font-sans); font-size: 2.75rem; font-weight: 900; color: var(--color-brand-dark); line-height: 1; letter-spacing: -0.02em;">{{ $content->impact_1_aantal }}</div>
<p style="font-size: 0.9375rem; color: var(--color-brand-muted); margin: 0.25rem 0 0; line-height: 1.4;">{{ $content->impactOmschrijving(1) }}</p>
```

Apply the same change for stat 2 (replace `_number` → `$content->impact_2_aantal`, `_desc` → `$content->impactOmschrijving(2)`) and stat 3 (`$content->impact_3_aantal`, `$content->impactOmschrijving(3)`).

- [ ] **Step 5: Remove DB-driven translation keys**

In `lang/nl/pages.php`, delete the lines defining:

- `over_ons_impact_1_number`
- `over_ons_impact_1_desc`
- `over_ons_impact_2_number`
- `over_ons_impact_2_desc`
- `over_ons_impact_3_number`
- `over_ons_impact_3_desc`

Repeat the same six deletions in `lang/fr/pages.php`.

Keep `over_ons_impact_1_label`, `over_ons_impact_2_label`, `over_ons_impact_3_label` in both files — these remain in code per the spec.

- [ ] **Step 6: Run all OverOnsPageTest tests**

Run: `php artisan test --compact tests/Feature/OverOnsPageTest.php`
Expected: PASS — all existing tests still pass and the two new ones pass.

- [ ] **Step 7: Lint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/PageController.php \
        resources/views/pages/over-ons.blade.php \
        lang/nl/pages.php lang/fr/pages.php \
        tests/Feature/OverOnsPageTest.php
git commit -m "feat(over-ons): impact stats render from OverOnsContent

Replace the hardcoded over_ons_impact_*_number / _desc translation
keys with values read from the singleton model, so the team can edit
the numbers and bilingual descriptions through the CMS."
```

---

## Task 3: Conditional jaarverslag link with year + size

**Files:**
- Modify: `resources/views/pages/over-ons.blade.php` (the jaarverslag aside, lines 95-111)
- Modify: `lang/nl/pages.php` and `lang/fr/pages.php` (drop link/size keys)
- Test: `tests/Feature/OverOnsPageTest.php` (extend)

- [ ] **Step 1: Add failing tests for the conditional jaarverslag block**

Append to `tests/Feature/OverOnsPageTest.php`. At the top of the file, add `use Illuminate\Http\UploadedFile;` and `use Illuminate\Support\Facades\Storage;`.

```php
public function test_jaarverslag_card_is_hidden_when_no_pdf_is_uploaded(): void
{
    OverOnsContent::factory()->create(['jaarverslag_jaar' => 2025]);

    $response = $this->get(route('nl.over-ons'));

    $response->assertStatus(200);
    $response->assertDontSee('over-ons-jaarverslag-link');
    $response->assertDontSee('Jaarverslag 2025');
}

public function test_jaarverslag_card_renders_year_and_pdf_link_when_uploaded(): void
{
    Storage::fake('public');

    $content = OverOnsContent::factory()->create(['jaarverslag_jaar' => 2026]);
    $content->addMedia(UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'))
        ->toMediaCollection('jaarverslag');

    $response = $this->get(route('nl.over-ons'));

    $response->assertStatus(200);
    $response->assertSee('Jaarverslag 2026');
    $response->assertSee('over-ons-jaarverslag-link');
    $response->assertSee($content->fresh()->getJaarverslagUrl(), false);
}

public function test_jaarverslag_card_uses_french_label_in_fr_locale(): void
{
    Storage::fake('public');

    $content = OverOnsContent::factory()->create(['jaarverslag_jaar' => 2026]);
    $content->addMedia(UploadedFile::fake()->create('report.pdf', 100, 'application/pdf'))
        ->toMediaCollection('jaarverslag');

    $response = $this->get(route('fr.over-ons'));

    $response->assertStatus(200);
    $response->assertSee('Rapport annuel 2026');
}
```

- [ ] **Step 2: Run the failing tests**

Run: `php artisan test --compact --filter='OverOnsPageTest::test_jaarverslag'`
Expected: FAIL — the page currently shows "Jaarverslag 2025" hardcoded for any content state, and the URL points to `docs/jaarverslag-2025.pdf` not the media URL.

- [ ] **Step 3: Update the Blade view's jaarverslag aside**

Edit `resources/views/pages/over-ons.blade.php`. Replace the entire `{{-- Right: jaarverslag --}}` block (lines 94-112 — from the comment through the closing `</div>`) with:

```blade
{{-- Right: jaarverslag --}}
@if ($content->getJaarverslagUrl())
    <div class="over-ons-visie-aside">
        <a href="{{ $content->getJaarverslagUrl() }}" target="_blank" rel="noopener noreferrer" style="display: flex; align-items: center; gap: 1rem; text-decoration: none; background: white; border-radius: 10px; padding: 1.25rem 1.5rem; box-shadow: 0 2px 12px rgba(44,40,38,0.07);" class="over-ons-jaarverslag-link">
            <div style="flex-shrink: 0; width: 44px; height: 44px; border-radius: 8px; background: #eef2f8; display: flex; align-items: center; justify-content: center; color: var(--color-brand-blue);">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div>
                <div class="over-ons-jaarverslag-title" style="font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); line-height: 1.3;">{{ __('pages.over_ons_visie_jaarverslag_label') }} {{ $content->jaarverslag_jaar }}</div>
                <div style="font-size: 0.8125rem; color: var(--color-brand-muted); margin-top: 0.2rem;">pdf, {{ $content->getJaarverslagSize() }}</div>
            </div>
        </a>
    </div>
@endif
```

The two changes vs the current code:
1. Wrap the whole `<div class="over-ons-visie-aside">…</div>` in `@if ($content->getJaarverslagUrl()) … @endif`.
2. The `<a href>` becomes `{{ $content->getJaarverslagUrl() }}`, the title becomes `{{ __('pages.over_ons_visie_jaarverslag_label') }} {{ $content->jaarverslag_jaar }}`, and the size line becomes `pdf, {{ $content->getJaarverslagSize() }}`.

- [ ] **Step 4: Drop the now-unused translation keys**

In `lang/nl/pages.php`, delete the lines defining:

- `over_ons_visie_jaarverslag_link`
- `over_ons_visie_jaarverslag_size`

Same two deletions in `lang/fr/pages.php`. Keep `over_ons_visie_jaarverslag_label` in both files.

- [ ] **Step 5: Run all OverOnsPageTest tests**

Run: `php artisan test --compact tests/Feature/OverOnsPageTest.php`
Expected: PASS — all existing tests + 5 new tests pass.

- [ ] **Step 6: Lint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/pages/over-ons.blade.php \
        lang/nl/pages.php lang/fr/pages.php \
        tests/Feature/OverOnsPageTest.php
git commit -m "feat(over-ons): conditional jaarverslag block driven by media

The jaarverslag aside now renders only when a PDF has been uploaded
to the OverOnsContent media collection. The label combines the
locale-specific 'Jaarverslag' / 'Rapport annuel' string with the
configured year, and the file size is computed from the media."
```

---

## Task 4: Filament admin page (loads with current record)

**Files:**
- Create: `app/Filament/Pages/ManageOverOnsContent.php`
- Create: `resources/views/filament/pages/manage-over-ons-content.blade.php`
- Test: `tests/Feature/Filament/ManageOverOnsContentTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Filament/ManageOverOnsContentTest.php`:

```php
<?php

namespace Tests\Feature\Filament;

use App\Filament\Pages\ManageOverOnsContent;
use App\Models\OverOnsContent;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageOverOnsContentTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        return User::where('email', config('auth.admin_email'))->firstOrFail();
    }

    public function test_page_renders_for_authenticated_admin(): void
    {
        $this->seed(AdminUserSeeder::class);

        $response = $this->actingAs($this->adminUser())->get('/admin/over-ons');

        $response->assertStatus(200);
    }

    public function test_page_redirects_guest_to_login(): void
    {
        $response = $this->get('/admin/over-ons');

        $response->assertRedirect('/admin/login');
    }

    public function test_form_is_prefilled_with_existing_record(): void
    {
        $this->seed(AdminUserSeeder::class);
        OverOnsContent::factory()->create([
            'jaarverslag_jaar' => 2026,
            'impact_1_aantal' => '321',
        ]);

        Livewire::actingAs($this->adminUser())
            ->test(ManageOverOnsContent::class)
            ->assertFormSet([
                'jaarverslag_jaar' => 2026,
                'impact_1_aantal' => '321',
            ]);
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact tests/Feature/Filament/ManageOverOnsContentTest.php`
Expected: FAIL — class `App\Filament\Pages\ManageOverOnsContent` does not exist.

- [ ] **Step 3: Generate the Filament page scaffold**

```bash
php artisan make:filament-page ManageOverOnsContent --no-interaction
```

This creates the page class and a Blade view. Replace the page class entirely with the implementation in step 4. The generated view will be replaced in step 5.

- [ ] **Step 4: Implement the page class (form schema added in Task 5)**

Replace `app/Filament/Pages/ManageOverOnsContent.php` with:

```php
<?php

namespace App\Filament\Pages;

use App\Models\OverOnsContent;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * @property-read Schema $form
 */
class ManageOverOnsContent extends Page
{
    protected string $view = 'filament.pages.manage-over-ons-content';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $navigationLabel = 'Over ons';

    protected static string | UnitEnum | null $navigationGroup = 'Inhoud';

    protected static ?string $title = 'Over ons-pagina';

    protected static ?string $slug = 'over-ons';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sections will be added in Task 5.
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    public function getRecord(): OverOnsContent
    {
        return OverOnsContent::current();
    }
}
```

- [ ] **Step 5: Replace the generated Blade view**

Replace `resources/views/filament/pages/manage-over-ons-content.blade.php` with:

```blade
<x-filament::page>
    {{ $this->form }}
</x-filament::page>
```

- [ ] **Step 6: Run the tests**

Run: `php artisan test --compact tests/Feature/Filament/ManageOverOnsContentTest.php`
Expected: PASS — 3 tests. The `assertFormSet` test passes because `mount()` fills the form from `getRecord()->attributesToArray()`.

- [ ] **Step 7: Lint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Pages/ManageOverOnsContent.php \
        resources/views/filament/pages/manage-over-ons-content.blade.php \
        tests/Feature/Filament/ManageOverOnsContentTest.php
git commit -m "feat(admin): scaffold Filament page for Over ons content

Custom Filament 4 page bound to the OverOnsContent singleton, served
at /admin/over-ons under a new 'Inhoud' navigation group. Form
schema is added in the next commit."
```

---

## Task 5: Filament form schema + save action

**Files:**
- Modify: `app/Filament/Pages/ManageOverOnsContent.php` (add form schema + save method)
- Modify: `tests/Feature/Filament/ManageOverOnsContentTest.php` (add save tests)

- [ ] **Step 1: Add failing tests for save behavior**

Append to `tests/Feature/Filament/ManageOverOnsContentTest.php`:

```php
public function test_admin_can_update_impact_stats_and_jaarverslag_year(): void
{
    $this->seed(AdminUserSeeder::class);
    OverOnsContent::factory()->create();

    Livewire::actingAs($this->adminUser())
        ->test(ManageOverOnsContent::class)
        ->fillForm([
            'jaarverslag_jaar' => 2027,
            'impact_1_aantal' => '300',
            'impact_1_omschrijving_nl' => 'NL stat 1',
            'impact_1_omschrijving_fr' => 'FR stat 1',
            'impact_2_aantal' => '5000',
            'impact_2_omschrijving_nl' => 'NL stat 2',
            'impact_2_omschrijving_fr' => 'FR stat 2',
            'impact_3_aantal' => '70+',
            'impact_3_omschrijving_nl' => 'NL stat 3',
            'impact_3_omschrijving_fr' => 'FR stat 3',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('over_ons_content', [
        'id' => 1,
        'jaarverslag_jaar' => 2027,
        'impact_1_aantal' => '300',
        'impact_2_aantal' => '5000',
        'impact_3_aantal' => '70+',
        'impact_3_omschrijving_fr' => 'FR stat 3',
    ]);
}

public function test_required_fields_block_save_when_blank(): void
{
    $this->seed(AdminUserSeeder::class);
    OverOnsContent::factory()->create();

    Livewire::actingAs($this->adminUser())
        ->test(ManageOverOnsContent::class)
        ->fillForm([
            'impact_1_aantal' => '',
            'impact_1_omschrijving_nl' => '',
        ])
        ->call('save')
        ->assertHasFormErrors([
            'impact_1_aantal' => 'required',
            'impact_1_omschrijving_nl' => 'required',
        ]);
}
```

- [ ] **Step 2: Run them to verify they fail**

Run: `php artisan test --compact --filter='ManageOverOnsContentTest::test_admin_can_update|ManageOverOnsContentTest::test_required_fields'`
Expected: FAIL — `save()` is not defined on the page (and the form has no fields).

- [ ] **Step 3: Add the form schema and `save()` method**

Replace the `form()` method in `app/Filament/Pages/ManageOverOnsContent.php` and add `save()`. Add these `use` statements at the top of the file:

```php
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
```

Replace the placeholder `form()` method body with:

```php
public function form(Schema $schema): Schema
{
    return $schema
        ->components([
            Form::make([
                Section::make('Jaarverslag')
                    ->description('Laat het PDF-veld leeg om het jaarverslag-blok op de Over ons-pagina te verbergen.')
                    ->schema([
                        TextInput::make('jaarverslag_jaar')
                            ->label('Jaar')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100),
                        // SpatieMediaLibraryFileUpload added in Task 6.
                    ]),
                Section::make('Impactcijfers')
                    ->schema([
                        Grid::make(3)->schema([
                            $this->impactColumn(1, 'Bezoekers'),
                            $this->impactColumn(2, 'Maaltijden'),
                            $this->impactColumn(3, 'Activiteiten'),
                        ]),
                    ]),
            ])
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')
                            ->label('Opslaan')
                            ->submit('save')
                            ->keyBindings(['mod+s']),
                    ]),
                ]),
        ])
        ->record($this->getRecord())
        ->statePath('data');
}

protected function impactColumn(int $n, string $label): Group
{
    return Group::make([
        Placeholder::make("label_{$n}")
            ->label('Categorie')
            ->content($label),
        TextInput::make("impact_{$n}_aantal")
            ->label('Aantal')
            ->required()
            ->maxLength(20),
        TextInput::make("impact_{$n}_omschrijving_nl")
            ->label('Omschrijving (NL)')
            ->required()
            ->maxLength(120),
        TextInput::make("impact_{$n}_omschrijving_fr")
            ->label('Omschrijving (FR)')
            ->required()
            ->maxLength(120),
    ]);
}

public function save(): void
{
    $data = $this->form->getState();

    $record = $this->getRecord();
    $record->fill($data);
    $record->save();

    Notification::make()
        ->success()
        ->title('Over ons-pagina bijgewerkt')
        ->send();
}
```

- [ ] **Step 4: Run the new tests + the existing ones**

Run: `php artisan test --compact tests/Feature/Filament/ManageOverOnsContentTest.php`
Expected: PASS — all 5 tests (3 from Task 4 + 2 new).

- [ ] **Step 5: Lint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Pages/ManageOverOnsContent.php \
        tests/Feature/Filament/ManageOverOnsContentTest.php
git commit -m "feat(admin): form and save action for Over ons-pagina

Two sections — Jaarverslag (year input) and Impactcijfers (3 columns,
each with aantal + bilingual omschrijving). Save validates required
fields and shows a success notification."
```

---

## Task 6: Filament PDF upload (Spatie Media Library)

**Files:**
- Modify: `app/Filament/Pages/ManageOverOnsContent.php` (add upload field)
- Modify: `tests/Feature/Filament/ManageOverOnsContentTest.php` (add upload tests)

- [ ] **Step 1: Add failing tests for PDF upload**

At the top of `tests/Feature/Filament/ManageOverOnsContentTest.php`, add:

```php
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
```

Append these methods:

```php
public function test_admin_can_upload_pdf_to_jaarverslag_collection(): void
{
    Storage::fake('public');
    $this->seed(AdminUserSeeder::class);
    OverOnsContent::factory()->create();

    Livewire::actingAs($this->adminUser())
        ->test(ManageOverOnsContent::class)
        ->fillForm([
            'jaarverslag' => [UploadedFile::fake()->create('verslag.pdf', 100, 'application/pdf')],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $this->assertSame(1, OverOnsContent::current()->getMedia('jaarverslag')->count());
    $this->assertNotNull(OverOnsContent::current()->getJaarverslagUrl());
}

public function test_uploading_a_new_pdf_replaces_the_previous_one(): void
{
    Storage::fake('public');
    $this->seed(AdminUserSeeder::class);
    $content = OverOnsContent::factory()->create();
    $content->addMedia(UploadedFile::fake()->create('first.pdf', 100, 'application/pdf'))
        ->toMediaCollection('jaarverslag');
    $this->assertSame(1, $content->fresh()->getMedia('jaarverslag')->count());

    Livewire::actingAs($this->adminUser())
        ->test(ManageOverOnsContent::class)
        ->fillForm([
            'jaarverslag' => [UploadedFile::fake()->create('second.pdf', 100, 'application/pdf')],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $media = OverOnsContent::current()->getMedia('jaarverslag');
    $this->assertSame(1, $media->count());
    $this->assertSame('second', $media->first()->name);
}

public function test_validation_rejects_non_pdf_uploads(): void
{
    Storage::fake('public');
    $this->seed(AdminUserSeeder::class);
    OverOnsContent::factory()->create();

    Livewire::actingAs($this->adminUser())
        ->test(ManageOverOnsContent::class)
        ->fillForm([
            'jaarverslag' => [UploadedFile::fake()->image('not-a-pdf.jpg')],
        ])
        ->call('save')
        ->assertHasFormErrors(['jaarverslag']);
}
```

- [ ] **Step 2: Run the failing tests**

Run: `php artisan test --compact --filter='ManageOverOnsContentTest::test_admin_can_upload|ManageOverOnsContentTest::test_uploading|ManageOverOnsContentTest::test_validation_rejects'`
Expected: FAIL — the form has no `jaarverslag` upload field.

- [ ] **Step 3: Add the SpatieMediaLibraryFileUpload to the Jaarverslag section**

In `app/Filament/Pages/ManageOverOnsContent.php`:

(a) add to the `use` block at the top:

```php
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
```

(b) replace the `// SpatieMediaLibraryFileUpload added in Task 6.` comment inside the `Jaarverslag` Section's schema with:

```php
SpatieMediaLibraryFileUpload::make('jaarverslag')
    ->label('PDF-bestand')
    ->collection('jaarverslag')
    ->acceptedFileTypes(['application/pdf'])
    ->maxSize(20480),
```

- [ ] **Step 4: Run all tests on this file**

Run: `php artisan test --compact tests/Feature/Filament/ManageOverOnsContentTest.php`
Expected: PASS — all 8 tests (5 from earlier + 3 new).

- [ ] **Step 5: Lint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Pages/ManageOverOnsContent.php \
        tests/Feature/Filament/ManageOverOnsContentTest.php
git commit -m "feat(admin): PDF upload for jaarverslag (Spatie Media Library)

PDF-only, max 20 MB, single-file collection — uploading a new file
replaces the previous one."
```

---

## Task 7: Initial data migration (seed singleton + copy existing PDF)

**Files:**
- Create: `database/migrations/2026_05_07_120100_seed_over_ons_content.php`
- Test: `tests/Feature/Migrations/SeedOverOnsContentTest.php`

This migration mirrors the existing `2026_04_22_150240_sync_activiteit_and_weekmenu_data` pattern: it short-circuits in unit tests, then on a real environment writes baseline data. The test invokes the migration directly to verify behavior.

- [ ] **Step 1: Create the migration file**

```bash
php artisan make:migration seed_over_ons_content --no-interaction
```

Replace its contents with:

```php
<?php

use App\Models\OverOnsContent;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $this->seedContent();
    }

    public function down(): void
    {
        // Data migration: leave the row in place if rolled back.
    }

    public function seedContent(): void
    {
        $content = OverOnsContent::firstOrCreate(['id' => 1], [
            'jaarverslag_jaar' => 2025,
            'impact_1_aantal' => '250',
            'impact_1_omschrijving_nl' => 'wekelijks bij ons over de vloer',
            'impact_1_omschrijving_fr' => 'chaque semaine chez nous',
            'impact_2_aantal' => '4500',
            'impact_2_omschrijving_nl' => 'maaltijden per maand',
            'impact_2_omschrijving_fr' => 'repas par mois',
            'impact_3_aantal' => '60+',
            'impact_3_omschrijving_nl' => 'activiteiten per jaar',
            'impact_3_omschrijving_fr' => 'activités par an',
        ]);

        $sourcePdf = public_path('docs/jaarverslag-2025.pdf');

        if (file_exists($sourcePdf) && $content->getMedia('jaarverslag')->isEmpty()) {
            $content->addMedia($sourcePdf)
                ->preservingOriginal()
                ->toMediaCollection('jaarverslag');

            @unlink($sourcePdf);
        }
    }
};
```

The `seedContent()` method is exposed (not `protected`) so the test in step 2 can call it directly without bypassing the `runningUnitTests()` guard at the migration level. The NL/FR strings above are the values from `lang/nl/pages.php` and `lang/fr/pages.php` as they stood before Task 2 deleted those keys — they reproduce the page exactly as it was on first deploy.

- [ ] **Step 2: Write the failing test**

Create `tests/Feature/Migrations/SeedOverOnsContentTest.php`:

```php
<?php

namespace Tests\Feature\Migrations;

use App\Models\OverOnsContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeedOverOnsContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_seed_content_inserts_singleton_with_baseline_values(): void
    {
        Storage::fake('public');
        $migration = $this->loadMigration();

        $migration->seedContent();

        $content = OverOnsContent::current();
        $this->assertSame(2025, $content->jaarverslag_jaar);
        $this->assertSame('250', $content->impact_1_aantal);
        $this->assertSame('4500', $content->impact_2_aantal);
        $this->assertSame('60+', $content->impact_3_aantal);
    }

    public function test_seed_content_is_idempotent(): void
    {
        Storage::fake('public');
        $migration = $this->loadMigration();

        $migration->seedContent();
        $migration->seedContent();

        $this->assertSame(1, OverOnsContent::count());
    }

    public function test_seed_content_copies_existing_pdf_into_media_collection(): void
    {
        Storage::fake('public');
        $sourcePath = public_path('docs/jaarverslag-2025.pdf');

        if (! is_dir(dirname($sourcePath))) {
            mkdir(dirname($sourcePath), 0755, true);
        }
        file_put_contents($sourcePath, '%PDF-1.4 fake');

        try {
            $migration = $this->loadMigration();
            $migration->seedContent();

            $media = OverOnsContent::current()->getMedia('jaarverslag');
            $this->assertSame(1, $media->count());
            $this->assertFileDoesNotExist($sourcePath);
        } finally {
            if (file_exists($sourcePath)) {
                unlink($sourcePath);
            }
        }
    }

    private function loadMigration(): object
    {
        $files = glob(database_path('migrations/*_seed_over_ons_content.php'));
        $this->assertNotEmpty($files, 'seed_over_ons_content migration not found');

        return require $files[0];
    }
}
```

- [ ] **Step 3: Run the test to verify it passes**

Run: `php artisan test --compact tests/Feature/Migrations/SeedOverOnsContentTest.php`
Expected: PASS — 3 tests. The migration short-circuits its `up()` in unit tests but `seedContent()` is callable directly.

If the test for "copies existing PDF" leaves a stray file in `public/docs/` (because the test created and the migration moved it into the storage fake but the test file path is real), the `try/finally` block cleans up.

- [ ] **Step 4: Verify the migration runs cleanly outside tests**

```bash
php artisan migrate:status
php artisan migrate
php artisan tinker --execute 'echo App\Models\OverOnsContent::current()->jaarverslag_jaar;'
```

Expected: prints `2025`. The PDF is now in the media library; `public/docs/jaarverslag-2025.pdf` is gone.

If you need to re-run the migration locally for dev, `php artisan migrate:fresh --seed` is safe — the migration is idempotent.

- [ ] **Step 5: Lint and commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/migrations/2026_05_07_120100_seed_over_ons_content.php \
        tests/Feature/Migrations/SeedOverOnsContentTest.php
git commit -m "feat(over-ons): one-off migration to seed content + import PDF

Inserts the OverOnsContent singleton with current production values
and copies public/docs/jaarverslag-2025.pdf into the media library,
removing the loose file. Idempotent, skipped in unit tests."
```

---

## Final verification

- [ ] **Step 1: Run the full test suite**

Run: `php artisan test --compact`
Expected: full suite green, including all new tests and the existing OverOnsPageTest assertions about static page content.

- [ ] **Step 2: Manual sanity check in the browser**

In Laravel Herd:

1. Open `https://deharmonie.test/over-ons` — the impact stats and jaarverslag link should render exactly as before (because the migration restored the same values + PDF).
2. Open `https://deharmonie.test/admin/over-ons` (log in as admin) — the form should be prefilled.
3. Change `impact_1_aantal` to `999`, save, refresh the public page — the new value appears.
4. Delete the PDF in the form, save, refresh the public page — the jaarverslag aside disappears.
5. Re-upload a PDF, save, refresh — the aside reappears with the configured year and the new file size.

- [ ] **Step 3: Final commit if any UI/lint adjustments were needed**

If steps 1-2 found anything that needed a tweak, commit it with a focused message. Otherwise, move on.

---

## Done

The team can now update the three impact-stat numbers, their bilingual descriptions, and the linked yearly report (PDF + year) directly from the Filament admin at `/admin/over-ons`. The public page reads from the singleton and silently hides the jaarverslag block whenever no PDF is uploaded.
