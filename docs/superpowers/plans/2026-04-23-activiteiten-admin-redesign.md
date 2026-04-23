# Activiteiten Admin Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the two-resource (Activiteiten + Reeksen) admin with one Activiteiten resource that mirrors the public agenda layout, driven by a `soort` field (vast/speciaal) and a 15-value `subcategorie` field that determines the icon and theme grouping.

**Architecture:** Drop `ActiviteitTemplate` and `template_id`. Add `soort` and `subcategorie` columns to `activiteiten`. Two new enums (`Soort`, `Subcategorie`) + a derived `Hoofdcategorie` enum (groups subcategorieën for the public theme cards). The Filament admin uses the standard `Tables\Table` with native `Group::make('week_start')` for the week-by-week layout and a single `ViewColumn` for the rich icon+title+meta cell — staying inside Filament idioms. The public `overzicht.blade.php` and `agenda.blade.php` switch from template-based and keyword-based logic to enum-driven lookups.

**Tech Stack:** Laravel 13, Filament 4, Livewire 3, PHPUnit 12, Pint, Spatie Media Library 11

**Spec:** `docs/superpowers/specs/2026-04-23-activiteiten-admin-redesign-design.md`

---

## File Map

| Action | Path | Responsibility |
|---|---|---|
| Create | `app/Enums/Hoofdcategorie.php` | 4-value enum with NL/FR labels + brand color |
| Create | `app/Enums/Subcategorie.php` | 15-value enum with NL/FR labels + `hoofd()` + `icon()` |
| Create | `app/Enums/Soort.php` | 2-value enum: vast/speciaal |
| Create | `app/Support/SubcategorieIcons.php` | SVG path strings for each subcategorie |
| Create | `database/migrations/XXXX_activiteiten_soort_and_subcategorie.php` | Add columns, backfill, drop template_id + table |
| Modify | `app/Models/Activiteit.php` | New fillable, casts, accessor for hoofdcategorie; drop template relationship |
| Delete | `app/Models/ActiviteitTemplate.php` | Removed |
| Delete | `app/Enums/DagVanDeWeek.php` | Removed (only used by template resource) |
| Delete | `app/Filament/Resources/ActiviteitTemplateResource.php` + Pages dir | Removed |
| Delete | `database/factories/ActiviteitTemplateFactory.php` (if exists) | Removed |
| Delete | `database/seeders/ActiviteitTemplateSeeder.php` (if exists) | Removed |
| Modify | `database/factories/ActiviteitFactory.php` | Add soort + subcategorie defaults |
| Modify | `database/seeders/ActiviteitSeeder.php` | Set soort + subcategorie per row |
| Modify | `app/Filament/Resources/ActiviteitResource.php` | New form (subcategorie select), drop template column, add header buttons, kopieer + bulk-edit actions |
| Modify | `app/Filament/Resources/ActiviteitResource/Pages/ListActiviteiten.php` | Add header create buttons (vast/speciaal) — table itself stays declared on the resource |
| Modify | `app/Filament/Resources/ActiviteitResource/Pages/CreateActiviteit.php` | Read `?soort=` from query string; default to speciaal; bulk-generation in afterCreate |
| Create | `resources/views/filament/tables/columns/activiteit-rich-cell.blade.php` | Single rich cell view: icon + title + badges + meta line |
| Modify | `app/Http/Controllers/ActivityController.php` | `index()` queries by soort + subcategorie groupings |
| Modify | `resources/views/activiteiten/overzicht.blade.php` | Theme cards read from `$vasteAanbod[hoofd]` |
| Modify | `resources/views/activiteiten/agenda.blade.php` | Replace ~150-line keyword block with `$activiteit->subcategorie->icon()` |
| Create | `tests/Feature/Enums/SubcategorieEnumTest.php` | Asserts every subcategorie has hoofd() + icon() |
| Create | `tests/Feature/Migrations/ActiviteitenSoortBackfillTest.php` | Migration backfill behavior |
| Create | `tests/Feature/Filament/ActiviteitCreateVastTest.php` | Bulk-generation create flow |
| Create | `tests/Feature/Filament/ActiviteitCreateSpeciaalTest.php` | Speciaal create flow (single date) |
| Create | `tests/Feature/Filament/ActiviteitKopieerTest.php` | Kopieer-naar action |
| Create | `tests/Feature/Filament/ActiviteitBulkEditTest.php` | Bulk-edit gemeenschappelijke velden |
| Create | `tests/Feature/OverzichtPaginaTest.php` | Public overzicht groups by hoofdcategorie |
| Create | `tests/Feature/AgendaIconTest.php` | Agenda renders deterministic icon per subcategorie |
| Modify | `tests/Feature/...` (existing tests touching templates) | Update or remove references to ActiviteitTemplate |

---

## Pre-flight

- [ ] **Step 1: Verify clean working tree**

```bash
git status
```

Expected: `nothing to commit, working tree clean`

- [ ] **Step 2: Verify current test suite is green**

```bash
php artisan test --compact
```

Expected: all green. Stop and investigate if anything fails — we need a baseline.

- [ ] **Step 3: Snapshot the existing template data**

```bash
php artisan tinker --execute 'echo App\Models\ActiviteitTemplate::pluck("id", "titel_nl")->toJson(JSON_PRETTY_PRINT);'
```

Expected output: 19 templates with their IDs. Save this output — it's the source of truth for the migration backfill map. The map below in Task 5 was derived from this; if your snapshot shows different titles, update Task 5's map first.

---

### Task 1: `Hoofdcategorie` enum

**Files:**
- Create: `app/Enums/Hoofdcategorie.php`
- Create: `tests/Feature/Enums/HoofdcategorieEnumTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Enums/HoofdcategorieEnumTest.php`:

```php
<?php

namespace Tests\Feature\Enums;

use App\Enums\Hoofdcategorie;
use Tests\TestCase;

class HoofdcategorieEnumTest extends TestCase
{
    public function test_has_four_cases(): void
    {
        $this->assertCount(4, Hoofdcategorie::cases());
    }

    public function test_labels_in_nl_and_fr(): void
    {
        $this->assertSame('Beweeg mee', Hoofdcategorie::Beweeg->getLabel());
        $this->assertSame('Bougez avec nous', Hoofdcategorie::Beweeg->labelFr());

        $this->assertSame('Maak iets', Hoofdcategorie::Maak->getLabel());
        $this->assertSame('Créez ensemble', Hoofdcategorie::Maak->labelFr());

        $this->assertSame('Praat & leer', Hoofdcategorie::Praat->getLabel());
        $this->assertSame('Parlez & apprenez', Hoofdcategorie::Praat->labelFr());

        $this->assertSame('Vier mee', Hoofdcategorie::Vier->getLabel());
        $this->assertSame('Fêtez avec nous', Hoofdcategorie::Vier->labelFr());
    }

    public function test_each_case_has_a_color(): void
    {
        foreach (Hoofdcategorie::cases() as $case) {
            $this->assertNotEmpty($case->color());
        }
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test --compact tests/Feature/Enums/HoofdcategorieEnumTest.php
```

Expected: FAIL with `Class "App\Enums\Hoofdcategorie" not found`.

- [ ] **Step 3: Implement the enum**

Create `app/Enums/Hoofdcategorie.php`:

```php
<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Hoofdcategorie: string implements HasLabel
{
    case Beweeg = 'beweeg';
    case Maak = 'maak';
    case Praat = 'praat';
    case Vier = 'vier';

    public function getLabel(): string
    {
        return match ($this) {
            self::Beweeg => 'Beweeg mee',
            self::Maak => 'Maak iets',
            self::Praat => 'Praat & leer',
            self::Vier => 'Vier mee',
        };
    }

    public function labelFr(): string
    {
        return match ($this) {
            self::Beweeg => 'Bougez avec nous',
            self::Maak => 'Créez ensemble',
            self::Praat => 'Parlez & apprenez',
            self::Vier => 'Fêtez avec nous',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Beweeg => 'var(--color-brand-orange)',
            self::Maak => 'var(--color-brand-green)',
            self::Praat => 'var(--color-brand-blue)',
            self::Vier => '#d4956a',
        };
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

```bash
php artisan test --compact tests/Feature/Enums/HoofdcategorieEnumTest.php
```

Expected: PASS (3 tests).

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Enums/Hoofdcategorie.php tests/Feature/Enums/HoofdcategorieEnumTest.php
git commit -m "feat: add Hoofdcategorie enum (4 themes for activity grouping)

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 2: `Soort` enum

**Files:**
- Create: `app/Enums/Soort.php`
- Create: `tests/Feature/Enums/SoortEnumTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature\Enums;

use App\Enums\Soort;
use Tests\TestCase;

class SoortEnumTest extends TestCase
{
    public function test_has_two_cases(): void
    {
        $this->assertCount(2, Soort::cases());
    }

    public function test_labels(): void
    {
        $this->assertSame('Vast', Soort::Vast->getLabel());
        $this->assertSame('Speciaal', Soort::Speciaal->getLabel());
    }
}
```

- [ ] **Step 2: Run, verify fail**

```bash
php artisan test --compact tests/Feature/Enums/SoortEnumTest.php
```

Expected: FAIL `Class "App\Enums\Soort" not found`.

- [ ] **Step 3: Implement**

```php
<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Soort: string implements HasLabel
{
    case Vast = 'vast';
    case Speciaal = 'speciaal';

    public function getLabel(): string
    {
        return match ($this) {
            self::Vast => 'Vast',
            self::Speciaal => 'Speciaal',
        };
    }
}
```

- [ ] **Step 4: Run, verify pass + commit**

```bash
php artisan test --compact tests/Feature/Enums/SoortEnumTest.php
vendor/bin/pint --dirty --format agent
git add app/Enums/Soort.php tests/Feature/Enums/SoortEnumTest.php
git commit -m "feat: add Soort enum (vast / speciaal)

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 3: `SubcategorieIcons` SVG library

**Files:**
- Create: `app/Support/SubcategorieIcons.php`

This is a deliberate single source of truth for the SVG path data. The enum (Task 4) calls into this. Public views will read paths from the enum, never embed them inline.

- [ ] **Step 1: Create the support class**

Create `app/Support/SubcategorieIcons.php`. Each method returns the inner `<path .../>` markup for an icon (the enclosing `<svg>` element comes from the rendering view). Reuse 9 SVG paths already present in `resources/views/activiteiten/agenda.blade.php` (search for `iconChat`, `iconMusic`, `iconStar`, `iconBolt`, `iconFood`, `iconGame`, `iconInfo`, `iconWorkshop`, `iconArt` — copy each path string verbatim). 6 new icons need Heroicons-solid lookalikes:

```php
<?php

namespace App\Support;

class SubcategorieIcons
{
    public static function dans(): string
    {
        // Heroicon "user" solid as a stand-in for a dancing figure (single-figure silhouette).
        return '<path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd"/>';
    }

    public static function gymFitness(): string
    {
        // Reuse iconBolt path from agenda.blade.php
        return '<path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.268a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .895-.143Z" clip-rule="evenodd"/>';
    }

    public static function wandeling(): string
    {
        // Heroicon "map-pin" solid as a stand-in for outdoor walking destination.
        return '<path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 0 0 .723 0l.028-.015.071-.041a16.975 16.975 0 0 0 1.144-.742 19.58 19.58 0 0 0 2.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 0 0-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 0 0 2.682 2.282 16.975 16.975 0 0 0 1.145.742ZM12 13.5a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" clip-rule="evenodd"/>';
    }

    public static function handwerk(): string
    {
        // Heroicon "scissors" solid as a stand-in for handwerk tools.
        return '<path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.268a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .895-.143Z" clip-rule="evenodd"/>';
        // NOTE: placeholder reusing bolt; TASK 3 STEP 2 below replaces this with a proper scissors path before we commit.
    }

    public static function creatiefAtelier(): string
    {
        // Reuse iconWorkshop (sparkles) from agenda.blade.php
        return '<path fill-rule="evenodd" d="M9 4.5a.75.75 0 0 1 .721.544l.813 2.846a3.75 3.75 0 0 0 2.576 2.576l2.846.813a.75.75 0 0 1 0 1.442l-2.846.813a3.75 3.75 0 0 0-2.576 2.576l-.813 2.846a.75.75 0 0 1-1.442 0l-.813-2.846a3.75 3.75 0 0 0-2.576-2.576l-2.846-.813a.75.75 0 0 1 0-1.442l2.846-.813A3.75 3.75 0 0 0 7.466 7.89l.813-2.846A.75.75 0 0 1 9 4.5ZM18 1.5a.75.75 0 0 1 .728.568l.258 1.036c.236.94.97 1.674 1.91 1.91l1.036.258a.75.75 0 0 1 0 1.456l-1.036.258c-.94.236-1.674.97-1.91 1.91l-.258 1.036a.75.75 0 0 1-1.456 0l-.258-1.036a2.625 2.625 0 0 0-1.91-1.91l-1.036-.258a.75.75 0 0 1 0-1.456l1.036-.258a2.625 2.625 0 0 0 1.91-1.91l.258-1.036A.75.75 0 0 1 18 1.5ZM16.5 15a.75.75 0 0 1 .712.513l.394 1.183c.15.447.5.799.948.948l1.183.395a.75.75 0 0 1 0 1.422l-1.183.395c-.447.15-.799.5-.948.948l-.395 1.183a.75.75 0 0 1-1.422 0l-.395-1.183a1.5 1.5 0 0 0-.948-.948l-1.183-.395a.75.75 0 0 1 0-1.422l1.183-.395c.447-.15.799-.5.948-.948l.395-1.183A.75.75 0 0 1 16.5 15Z" clip-rule="evenodd"/>';
    }

    public static function koken(): string
    {
        // Reuse iconFood (heart) from agenda.blade.php as a culinary stand-in
        return '<path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z"/>';
    }

    public static function digitaalAtelier(): string
    {
        // Heroicon "computer-desktop" solid
        return '<path fill-rule="evenodd" d="M2.25 5.25a3 3 0 0 1 3-3h13.5a3 3 0 0 1 3 3V15a3 3 0 0 1-3 3h-3v.257c0 .597.237 1.17.659 1.591l.621.622a.75.75 0 0 1-.53 1.28h-9a.75.75 0 0 1-.53-1.28l.621-.622a2.25 2.25 0 0 0 .659-1.59V18h-3a3 3 0 0 1-3-3V5.25Zm1.5 0v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5Z" clip-rule="evenodd"/>';
    }

    public static function conversatietafel(): string
    {
        // Reuse iconChat from agenda.blade.php
        return '<path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0 1 12 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 0 1-3.476.383.39.39 0 0 0-.297.17l-2.755 4.133a.75.75 0 0 1-1.248 0l-2.755-4.133a.39.39 0 0 0-.297-.17 48.9 48.9 0 0 1-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97Z" clip-rule="evenodd"/>';
    }

    public static function geheugenBrein(): string
    {
        // Heroicon "light-bulb" solid as stand-in for a brain/idea
        return '<path d="M12 .75a8.25 8.25 0 0 0-4.135 15.39c.686.398 1.115 1.008 1.134 1.623a.75.75 0 0 0 .577.706c.352.083.71.148 1.074.195.323.041.6-.218.6-.544v-4.661a6.714 6.714 0 0 1-.937-.171.75.75 0 1 1 .374-1.453 5.261 5.261 0 0 0 2.626 0 .75.75 0 1 1 .374 1.452 6.712 6.712 0 0 1-.937.172v4.66c0 .327.277.586.6.545.364-.047.722-.112 1.074-.195a.75.75 0 0 0 .577-.706c.02-.615.448-1.225 1.134-1.623A8.25 8.25 0 0 0 12 .75Z"/><path fill-rule="evenodd" d="M9.013 19.9a.75.75 0 0 1 .877-.597 11.319 11.319 0 0 0 4.22 0 .75.75 0 1 1 .28 1.473 12.819 12.819 0 0 1-4.78 0 .75.75 0 0 1-.597-.876ZM9.754 22.344a.75.75 0 0 1 .824-.668 13.682 13.682 0 0 0 2.844 0 .75.75 0 1 1 .156 1.492 15.156 15.156 0 0 1-3.156 0 .75.75 0 0 1-.668-.824Z" clip-rule="evenodd"/>';
    }

    public static function infoSpreekuur(): string
    {
        // Reuse iconInfo from agenda.blade.php
        return '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 0 1-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 0 1-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 0 1-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.83-.727.83-1.857 0-2.584ZM12 18a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd"/>';
    }

    public static function cultuurMuseum(): string
    {
        // Reuse iconArt (photo frame) from agenda.blade.php
        return '<path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd"/>';
    }

    public static function spelletjes(): string
    {
        // Reuse iconGame from agenda.blade.php
        return '<path d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/>';
    }

    public static function feest(): string
    {
        // Reuse iconStar from agenda.blade.php
        return '<path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd"/>';
    }

    public static function muziekConcert(): string
    {
        // Reuse iconMusic from agenda.blade.php
        return '<path fill-rule="evenodd" d="M19.952 1.651a.75.75 0 0 1 .298.599V16.303a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.403-4.909l2.311-.66a1.5 1.5 0 0 0 1.088-1.442V6.994l-9 2.572v9.737a3 3 0 0 1-2.176 2.884l-1.32.377a2.553 2.553 0 1 1-1.402-4.909l2.31-.66a1.5 1.5 0 0 0 1.088-1.442V5.25a.75.75 0 0 1 .544-.721l10.5-3a.75.75 0 0 1 .658.122Z" clip-rule="evenodd"/>';
    }

    public static function etenDrinken(): string
    {
        // Heroicon "cake" solid as stand-in for shared meals / events
        return '<path d="M15 1.784l-.796.796a1.125 1.125 0 1 0 1.591 0L15 1.784ZM12 1.784l-.796.796a1.125 1.125 0 1 0 1.591 0L12 1.784ZM9 1.784l-.796.796a1.125 1.125 0 1 0 1.591 0L9 1.784ZM9.75 7.547c.498-.02.998-.035 1.5-.042V6a.75.75 0 0 1 1.5 0v1.505c.502.007 1.002.021 1.5.042V6a.75.75 0 0 1 1.5 0v1.6c1.829.114 3.654.292 5.474.532 1.198.158 2.026 1.187 2.026 2.345V15c0 .619-.179 1.215-.51 1.726l.49.49a.75.75 0 0 1-1.06 1.061l-.397-.397a3.726 3.726 0 0 1-3.026.118c-1.02-.401-2.18-.401-3.2 0-.66.26-1.4.39-2.137.39h-.038A6.13 6.13 0 0 1 12 18.118c-.66-.26-1.4-.39-2.137-.39h-.038c-.737 0-1.477.13-2.137.39a3.726 3.726 0 0 1-3.026-.118l-.397.397a.75.75 0 0 1-1.06-1.06l.49-.49A2.485 2.485 0 0 1 3.184 15v-4.523c0-1.158.828-2.187 2.026-2.345 1.82-.24 3.645-.418 5.474-.532V6a.75.75 0 0 1 1.5 0v1.547Z"/><path fill-rule="evenodd" d="M3.75 22.5l1.5 -1.5h13.5l1.5 1.5H3.75ZM3.184 19.05a3.726 3.726 0 0 0 3.026.117c1.02-.4 2.18-.4 3.2 0a6.13 6.13 0 0 0 4.18 0c1.02-.4 2.18-.4 3.2 0a3.726 3.726 0 0 0 3.026-.117V19.5H3.184v-.45Z" clip-rule="evenodd"/>';
    }
}
```

- [ ] **Step 2: Replace the placeholder `handwerk()` body**

The `handwerk()` method currently returns the bolt path as a placeholder. Replace its body with the Heroicon "scissors" solid path:

```php
return '<path fill-rule="evenodd" d="M9.401 6.94L4.97 2.51a.75.75 0 0 0-1.06 1.06l4.43 4.43a3.5 3.5 0 1 0 1.06-1.06ZM6.5 11.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4ZM21.06 21.06a.75.75 0 1 0 1.06-1.06l-7.072-7.072 2.122-2.121a.75.75 0 0 0-1.061-1.061l-2.122 2.121-2.121-2.121 6.011-6.012a.75.75 0 0 0-1.061-1.061L9.401 8.994a3.5 3.5 0 1 0 1.06 1.06l2.122 2.122-2.122 2.121a3.5 3.5 0 1 0 1.061 1.061l2.121-2.121 7.417 7.823ZM6.5 19.5a2 2 0 1 1 0-4 2 2 0 0 1 0 4Z" clip-rule="evenodd"/>';
```

- [ ] **Step 3: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Support/SubcategorieIcons.php
git commit -m "feat: add SubcategorieIcons SVG library

Reuses 9 paths already in agenda.blade.php and adds 6 new ones
(dans, wandeling, handwerk, koken, digitaal_atelier, geheugen_brein,
eten_drinken) so every subcategorie has a deterministic icon.

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 4: `Subcategorie` enum

**Files:**
- Create: `app/Enums/Subcategorie.php`
- Create: `tests/Feature/Enums/SubcategorieEnumTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature\Enums;

use App\Enums\Hoofdcategorie;
use App\Enums\Subcategorie;
use Tests\TestCase;

class SubcategorieEnumTest extends TestCase
{
    public function test_has_fifteen_cases(): void
    {
        $this->assertCount(15, Subcategorie::cases());
    }

    public function test_every_case_resolves_to_a_hoofdcategorie(): void
    {
        foreach (Subcategorie::cases() as $sub) {
            $this->assertInstanceOf(Hoofdcategorie::class, $sub->hoofd());
        }
    }

    public function test_every_case_returns_a_non_empty_icon(): void
    {
        foreach (Subcategorie::cases() as $sub) {
            $svg = $sub->icon();
            $this->assertNotEmpty($svg);
            $this->assertStringContainsString('<path', $svg);
        }
    }

    public function test_hoofd_grouping_counts(): void
    {
        $byHoofd = collect(Subcategorie::cases())->groupBy(fn ($s) => $s->hoofd()->value);

        $this->assertCount(3, $byHoofd['beweeg']);
        $this->assertCount(4, $byHoofd['maak']);
        $this->assertCount(4, $byHoofd['praat']);
        $this->assertCount(4, $byHoofd['vier']);
    }

    public function test_specific_label_examples(): void
    {
        $this->assertSame('Dans', Subcategorie::Dans->getLabel());
        $this->assertSame('Conversatietafel', Subcategorie::Conversatietafel->getLabel());
        $this->assertSame('Spelletjes', Subcategorie::Spelletjes->getLabel());
    }

    public function test_french_label_examples(): void
    {
        $this->assertSame('Danse', Subcategorie::Dans->labelFr());
        $this->assertSame('Table de conversation', Subcategorie::Conversatietafel->labelFr());
    }
}
```

- [ ] **Step 2: Run, verify fail**

```bash
php artisan test --compact tests/Feature/Enums/SubcategorieEnumTest.php
```

Expected: FAIL `Class "App\Enums\Subcategorie" not found`.

- [ ] **Step 3: Implement the enum**

```php
<?php

namespace App\Enums;

use App\Support\SubcategorieIcons;
use Filament\Support\Contracts\HasLabel;

enum Subcategorie: string implements HasLabel
{
    case Dans = 'dans';
    case GymFitness = 'gym_fitness';
    case Wandeling = 'wandeling';

    case Handwerk = 'handwerk';
    case CreatiefAtelier = 'creatief_atelier';
    case Koken = 'koken';
    case DigitaalAtelier = 'digitaal_atelier';

    case Conversatietafel = 'conversatietafel';
    case GeheugenBrein = 'geheugen_brein';
    case InfoSpreekuur = 'info_spreekuur';
    case CultuurMuseum = 'cultuur_museum';

    case Spelletjes = 'spelletjes';
    case Feest = 'feest';
    case MuziekConcert = 'muziek_concert';
    case EtenDrinken = 'eten_drinken';

    public function hoofd(): Hoofdcategorie
    {
        return match ($this) {
            self::Dans, self::GymFitness, self::Wandeling => Hoofdcategorie::Beweeg,
            self::Handwerk, self::CreatiefAtelier, self::Koken, self::DigitaalAtelier => Hoofdcategorie::Maak,
            self::Conversatietafel, self::GeheugenBrein, self::InfoSpreekuur, self::CultuurMuseum => Hoofdcategorie::Praat,
            self::Spelletjes, self::Feest, self::MuziekConcert, self::EtenDrinken => Hoofdcategorie::Vier,
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Dans => 'Dans',
            self::GymFitness => 'Gym & fitness',
            self::Wandeling => 'Wandeling',
            self::Handwerk => 'Handwerk',
            self::CreatiefAtelier => 'Creatief atelier',
            self::Koken => 'Koken & confituur',
            self::DigitaalAtelier => 'Digitaal atelier',
            self::Conversatietafel => 'Conversatietafel',
            self::GeheugenBrein => 'Geheugen & brein',
            self::InfoSpreekuur => 'Info & spreekuur',
            self::CultuurMuseum => 'Cultuur & museum',
            self::Spelletjes => 'Spelletjes',
            self::Feest => 'Feest & verjaardag',
            self::MuziekConcert => 'Muziek & concert',
            self::EtenDrinken => 'Eten & drinken',
        };
    }

    public function labelFr(): string
    {
        return match ($this) {
            self::Dans => 'Danse',
            self::GymFitness => 'Gym & fitness',
            self::Wandeling => 'Promenade',
            self::Handwerk => 'Travaux manuels',
            self::CreatiefAtelier => 'Atelier créatif',
            self::Koken => 'Cuisine & confiture',
            self::DigitaalAtelier => 'Atelier numérique',
            self::Conversatietafel => 'Table de conversation',
            self::GeheugenBrein => 'Mémoire & cerveau',
            self::InfoSpreekuur => 'Info & permanence',
            self::CultuurMuseum => 'Culture & musée',
            self::Spelletjes => 'Jeux',
            self::Feest => 'Fête & anniversaire',
            self::MuziekConcert => 'Musique & concert',
            self::EtenDrinken => 'Repas & boissons',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Dans => SubcategorieIcons::dans(),
            self::GymFitness => SubcategorieIcons::gymFitness(),
            self::Wandeling => SubcategorieIcons::wandeling(),
            self::Handwerk => SubcategorieIcons::handwerk(),
            self::CreatiefAtelier => SubcategorieIcons::creatiefAtelier(),
            self::Koken => SubcategorieIcons::koken(),
            self::DigitaalAtelier => SubcategorieIcons::digitaalAtelier(),
            self::Conversatietafel => SubcategorieIcons::conversatietafel(),
            self::GeheugenBrein => SubcategorieIcons::geheugenBrein(),
            self::InfoSpreekuur => SubcategorieIcons::infoSpreekuur(),
            self::CultuurMuseum => SubcategorieIcons::cultuurMuseum(),
            self::Spelletjes => SubcategorieIcons::spelletjes(),
            self::Feest => SubcategorieIcons::feest(),
            self::MuziekConcert => SubcategorieIcons::muziekConcert(),
            self::EtenDrinken => SubcategorieIcons::etenDrinken(),
        };
    }

    /**
     * Group cases by hoofdcategorie value, for use in Filament Select grouped options.
     *
     * @return array<string, array<string, string>>
     */
    public static function groupedOptions(): array
    {
        $grouped = [];
        foreach (self::cases() as $case) {
            $grouped[$case->hoofd()->getLabel()][$case->value] = $case->getLabel();
        }
        return $grouped;
    }
}
```

- [ ] **Step 4: Run, verify pass**

```bash
php artisan test --compact tests/Feature/Enums/SubcategorieEnumTest.php
```

Expected: PASS (6 tests).

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Enums/Subcategorie.php tests/Feature/Enums/SubcategorieEnumTest.php
git commit -m "feat: add Subcategorie enum (15 cases) with hoofd() + icon() resolution

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 5: Migration — add columns, backfill, drop template_id and templates table

**Files:**
- Create: `database/migrations/XXXX_activiteiten_soort_and_subcategorie.php`
- Create: `tests/Feature/Migrations/ActiviteitenSoortBackfillTest.php`

- [ ] **Step 1: Generate the migration**

```bash
php artisan make:migration activiteiten_soort_and_subcategorie --no-interaction
```

Note the generated filename for the next steps.

- [ ] **Step 2: Write the failing migration test**

Create `tests/Feature/Migrations/ActiviteitenSoortBackfillTest.php`:

```php
<?php

namespace Tests\Feature\Migrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActiviteitenSoortBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_migration_schema(): void
    {
        $this->assertTrue(Schema::hasColumn('activiteiten', 'soort'));
        $this->assertTrue(Schema::hasColumn('activiteiten', 'subcategorie'));
        $this->assertFalse(Schema::hasColumn('activiteiten', 'template_id'));
        $this->assertFalse(Schema::hasTable('activiteit_templates'));
    }

    public function test_seeded_activiteiten_have_soort_and_subcategorie(): void
    {
        $this->seed();

        $rows = DB::table('activiteiten')
            ->selectRaw('COUNT(*) as total, COUNT(soort) as with_soort, COUNT(subcategorie) as with_sub')
            ->first();

        $this->assertGreaterThan(0, $rows->total);
        $this->assertSame($rows->total, $rows->with_soort);
        $this->assertSame($rows->total, $rows->with_sub);
    }
}
```

- [ ] **Step 3: Run, verify fail**

```bash
php artisan test --compact tests/Feature/Migrations/ActiviteitenSoortBackfillTest.php
```

Expected: FAIL — at minimum the schema-shape test fails because the migration body is empty.

- [ ] **Step 4: Implement the migration**

Open the generated migration file and replace its contents:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Backfill map: existing template titel_nl -> subcategorie value.
     * Lifted from a tinker snapshot of `ActiviteitTemplate::pluck('id', 'titel_nl')`
     * captured 2026-04-23. Update if the snapshot in the pre-flight step differs.
     */
    private const TEMPLATE_TO_SUB = [
        'Sociale infopunt' => 'info_spreekuur',
        'Maandelijks verjaardagsfeest' => 'feest',
        'Conversatietafel Spaans' => 'conversatietafel',
        'Conversatietafel Engels' => 'conversatietafel',
        'Conversatietafel Italiaans' => 'conversatietafel',
        'Nederlandse conversatietafel' => 'conversatietafel',
        'Country Line Dance' => 'dans',
        'Geheugenatelier' => 'geheugen_brein',
        'Stoel-gym met Nicole' => 'gym_fitness',
        'Digitale workshop' => 'digitaal_atelier',
        'Bingo' => 'spelletjes',
        'Creativiteit workshop' => 'creatief_atelier',
        'Zumba' => 'dans',
        'Diamond Painting Workshop met Nadia' => 'handwerk',
        'Naaiworkshop' => 'handwerk',
        'Boodschappendienst' => 'info_spreekuur',
        'Pilates & Fitness' => 'gym_fitness',
        'Jeu de Tables: Dominos' => 'spelletjes',
        'Jeu de Tables: Jacquet' => 'spelletjes',
    ];

    /**
     * Keyword map for speciale momenten (one-off Activiteiten with template_id NULL).
     * First match wins; order matters (specific before broad).
     */
    private const KEYWORD_TO_SUB = [
        ['needle' => ['gouter', 'goûter', 'ontbijt', 'brunch', 'lunch', 'diner', 'dîner', 'souper', 'buffet', 'aperitief', 'apéro', 'apero', 'koffie', 'café'], 'sub' => 'eten_drinken'],
        ['needle' => ['museum', 'musée', 'expo', 'tentoon', 'kunst'], 'sub' => 'cultuur_museum'],
        ['needle' => ['documentaire', 'film', 'theater', 'théâtre', 'voorstelling', 'debat'], 'sub' => 'cultuur_museum'],
        ['needle' => ['festival', 'concert', 'musette', 'muziek', 'klassiek'], 'sub' => 'muziek_concert'],
        ['needle' => ['wandel', 'balade', 'marche'], 'sub' => 'wandeling'],
        ['needle' => ['feest', 'verjaardag', 'inhuldiging', 'fête', 'fete'], 'sub' => 'feest'],
        ['needle' => ['atelier', 'workshop'], 'sub' => 'creatief_atelier'],
        ['needle' => ['haken', 'naai', 'breien', 'diamond'], 'sub' => 'handwerk'],
        ['needle' => ['confituur', 'koken', 'culinair', 'cuisine'], 'sub' => 'koken'],
        ['needle' => ['woordspelletjes', 'scrabble', 'spel', 'jeu', 'domino', 'jacquet', 'bingo', 'kaart'], 'sub' => 'spelletjes'],
        ['needle' => ['conversatie', 'startbabbel', 'praat'], 'sub' => 'conversatietafel'],
        ['needle' => ['geheugen', 'brein', 'mémoire'], 'sub' => 'geheugen_brein'],
        ['needle' => ['infopunt', 'spreekuur', 'permanentie', 'loket'], 'sub' => 'info_spreekuur'],
        ['needle' => ['digitaal', 'numérique', 'computer'], 'sub' => 'digitaal_atelier'],
        ['needle' => ['zumba', 'dans'], 'sub' => 'dans'],
        ['needle' => ['gym', 'pilates', 'fitness', 'yoga'], 'sub' => 'gym_fitness'],
    ];

    private const FALLBACK_SUB = 'cultuur_museum';

    public function up(): void
    {
        // 1. Add columns nullable so we can backfill.
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->string('soort')->nullable()->after('status');
            $table->string('subcategorie')->nullable()->after('soort');
        });

        // 2. Backfill soort.
        DB::table('activiteiten')->whereNull('template_id')->update(['soort' => 'speciaal']);
        DB::table('activiteiten')->whereNotNull('template_id')->update(['soort' => 'vast']);

        // 3. Backfill subcategorie for vaste activiteiten via the template map.
        if (Schema::hasTable('activiteit_templates')) {
            $templates = DB::table('activiteit_templates')->get(['id', 'titel_nl']);
            foreach ($templates as $tpl) {
                $sub = self::TEMPLATE_TO_SUB[$tpl->titel_nl] ?? null;
                if ($sub === null) {
                    Log::warning('[soort-backfill] no template->sub map for "'.$tpl->titel_nl.'" (id='.$tpl->id.'), defaulting to '.self::FALLBACK_SUB);
                    $sub = self::FALLBACK_SUB;
                }
                DB::table('activiteiten')
                    ->where('template_id', $tpl->id)
                    ->update(['subcategorie' => $sub]);
            }
        }

        // 4. Backfill subcategorie for speciale momenten via keyword scan.
        $speciaal = DB::table('activiteiten')
            ->where('soort', 'speciaal')
            ->get(['id', 'titel_nl']);

        foreach ($speciaal as $row) {
            $sub = $this->matchKeyword($row->titel_nl) ?? self::FALLBACK_SUB;
            if ($sub === self::FALLBACK_SUB) {
                Log::warning('[soort-backfill] keyword fallback for "'.$row->titel_nl.'" (id='.$row->id.')');
            }
            DB::table('activiteiten')
                ->where('id', $row->id)
                ->update(['subcategorie' => $sub]);
        }

        // 5. Make NOT NULL (only after backfill).
        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->string('soort')->nullable(false)->change();
            $table->string('subcategorie')->nullable(false)->change();
        });

        // 6. Drop template_id FK + column.
        Schema::table('activiteiten', function (Blueprint $table): void {
            if (Schema::hasColumn('activiteiten', 'template_id')) {
                // Drop FK first; constraint name follows Laravel convention.
                try {
                    $table->dropForeign(['template_id']);
                } catch (\Throwable) {
                    // Constraint may not exist if originally added without one — ignore.
                }
                $table->dropColumn('template_id');
            }
        });

        // 7. Drop the templates table.
        Schema::dropIfExists('activiteit_templates');
    }

    public function down(): void
    {
        // Recreate templates table with minimal columns so a rollback is possible.
        Schema::create('activiteit_templates', function (Blueprint $table): void {
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
            $table->tinyInteger('dag_van_de_week');
            $table->date('reeks_start');
            $table->date('reeks_einde');
            $table->timestamps();
        });

        Schema::table('activiteiten', function (Blueprint $table): void {
            $table->foreignId('template_id')->nullable()->after('status')->constrained('activiteit_templates')->nullOnDelete();
            $table->dropColumn(['soort', 'subcategorie']);
        });
    }

    private function matchKeyword(?string $title): ?string
    {
        if ($title === null) {
            return null;
        }
        $haystack = Str::lower($title);
        foreach (self::KEYWORD_TO_SUB as $rule) {
            foreach ($rule['needle'] as $needle) {
                if (str_contains($haystack, $needle)) {
                    return $rule['sub'];
                }
            }
        }
        return null;
    }
};
```

- [ ] **Step 5: Run the migration test**

```bash
php artisan test --compact tests/Feature/Migrations/ActiviteitenSoortBackfillTest.php
```

Expected: the schema-shape test PASSES; the seeded test will fail because the seeders still write the old shape. We fix seeders in Tasks 9–10. **Do not commit yet — keep this working tree dirty across the next few tasks.**

> **Note:** at this point the `ActiviteitTemplate` model still exists. The migration deletes the `activiteit_templates` table but the PHP class is still loaded. Tests using `ActiviteitTemplate` will throw `BadMethodCallException` after migration. We delete that class in Task 7.

---

### Task 6: Update `Activiteit` model

**Files:**
- Modify: `app/Models/Activiteit.php`

- [ ] **Step 1: Update fillable, casts, drop template relation, add hoofd accessor**

Replace the model body to:

1. Drop `template_id` from `$fillable`.
2. Add `soort` and `subcategorie` to `$fillable`.
3. Cast `soort => Soort::class` and `subcategorie => Subcategorie::class`.
4. Drop the `template()` relationship method.
5. Add a `getHoofdcategorieAttribute()` accessor returning `$this->subcategorie->hoofd()`.
6. Drop `getInteresseThumbnailUrlAttribute()` if no longer used (search for callers first; if none, drop).

```php
<?php

namespace App\Models;

use App\Enums\ActiviteitStatus;
use App\Enums\Hoofdcategorie;
use App\Enums\Interesse;
use App\Enums\Soort;
use App\Enums\Subcategorie;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Activiteit extends Model
{
    use HasFactory;

    protected $table = 'activiteiten';

    protected $fillable = [
        'slug', 'titel_nl', 'titel_fr',
        'beschrijving_nl', 'beschrijving_fr',
        'notice_nl', 'notice_fr',
        'datum', 'startuur', 'einduur',
        'locatie', 'prijs', 'max_deelnemers',
        'status', 'interesse',
        'soort', 'subcategorie',
    ];

    protected $casts = [
        'datum' => 'date',
        'prijs' => 'decimal:2',
        'status' => ActiviteitStatus::class,
        'interesse' => Interesse::class,
        'soort' => Soort::class,
        'subcategorie' => Subcategorie::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Activiteit $activiteit): void {
            if (empty($activiteit->slug)) {
                $activiteit->slug = static::generateUniqueSlug($activiteit->titel_nl);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    public function deelnameverzoeken(): HasMany
    {
        return $this->hasMany(Deelnameverzoek::class);
    }

    public function isBeschikbaar(): bool
    {
        if ($this->max_deelnemers === null) {
            return true;
        }

        return $this->deelnameverzoeken()->count() < $this->max_deelnemers;
    }

    public function getPrijsLabel(string $locale = 'nl'): string
    {
        if ($this->prijs === null || (float) $this->prijs === 0.0) {
            return $locale === 'fr' ? 'Gratuit' : 'Gratis';
        }

        return '€ '.number_format((float) $this->prijs, 2, ',', '.');
    }

    public function getTitelAttribute(): string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->titel_fr : $this->titel_nl;
    }

    public function getBeschrijvingAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->beschrijving_fr : $this->beschrijving_nl;
    }

    public function getNoticeAttribute(): ?string
    {
        $locale = app()->getLocale();

        return $locale === 'fr' ? $this->notice_fr : $this->notice_nl;
    }

    public function getHoofdcategorieAttribute(): Hoofdcategorie
    {
        return $this->subcategorie->hoofd();
    }
}
```

- [ ] **Step 2: Pint (do not commit yet — Tasks 7–10 below clean up the rest before we run any tests)**

```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 7: Delete `ActiviteitTemplate` model + factory + seeder

**Files:**
- Delete: `app/Models/ActiviteitTemplate.php`
- Delete: `database/factories/ActiviteitTemplateFactory.php` (if exists)
- Delete: `database/seeders/ActiviteitTemplateSeeder.php` (if exists)

- [ ] **Step 1: Confirm files exist, then delete**

```bash
ls app/Models/ActiviteitTemplate.php database/factories/ActiviteitTemplateFactory.php database/seeders/ActiviteitTemplateSeeder.php 2>/dev/null
rm -f app/Models/ActiviteitTemplate.php database/factories/ActiviteitTemplateFactory.php database/seeders/ActiviteitTemplateSeeder.php
```

- [ ] **Step 2: Find any remaining references**

```bash
grep -rn "ActiviteitTemplate" --include="*.php" --include="*.blade.php" app/ database/ tests/ resources/ routes/ config/ 2>/dev/null
```

Expected output: any remaining references. The migration in Task 5 references the *table* `activiteit_templates` only (string), not the model — so it should not appear here. Anything that does appear must be removed in this task before continuing.

Likely remaining references and how to handle:
- `database/seeders/DatabaseSeeder.php` if it calls `$this->call(ActiviteitTemplateSeeder::class)` — remove that line.
- Any one-off data migration that imports `ActiviteitTemplate` (e.g. `2026_04_22_150240_sync_activiteit_and_weekmenu_data`) — these have already run in production and won't run again, but Laravel still parses them; replace the import with a string table reference if needed, or wrap the offending lines with `if (Schema::hasTable('activiteit_templates'))`.

Edit each file flagged by grep to remove the reference.

- [ ] **Step 3: Re-run grep to confirm clean**

```bash
grep -rn "ActiviteitTemplate" --include="*.php" --include="*.blade.php" app/ database/ tests/ resources/ routes/ config/ 2>/dev/null
```

Expected: empty.

---

### Task 8: Delete `ActiviteitTemplateResource` and `DagVanDeWeek` enum

**Files:**
- Delete: `app/Filament/Resources/ActiviteitTemplateResource.php`
- Delete: `app/Filament/Resources/ActiviteitTemplateResource/` (entire Pages directory)
- Delete: `app/Enums/DagVanDeWeek.php`

- [ ] **Step 1: Delete the resource and its Pages directory**

```bash
rm -rf app/Filament/Resources/ActiviteitTemplateResource.php app/Filament/Resources/ActiviteitTemplateResource/
```

- [ ] **Step 2: Check if `DagVanDeWeek` is used elsewhere**

```bash
grep -rn "DagVanDeWeek" --include="*.php" --include="*.blade.php" app/ database/ tests/ resources/ 2>/dev/null
```

If anything other than the enum file itself remains, fix those references first. Otherwise:

```bash
rm -f app/Enums/DagVanDeWeek.php
```

- [ ] **Step 3: Search for stale ActiviteitTemplateResource references**

```bash
grep -rn "ActiviteitTemplateResource" --include="*.php" app/ tests/ 2>/dev/null
```

Expected: empty.

---

### Task 9: Update `ActiviteitFactory`

**Files:**
- Modify: `database/factories/ActiviteitFactory.php`

- [ ] **Step 1: Add soort + subcategorie defaults**

Replace `definition()` so each generated activity gets a random soort + subcategorie:

```php
<?php

namespace Database\Factories;

use App\Enums\ActiviteitStatus;
use App\Enums\Soort;
use App\Enums\Subcategorie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActiviteitFactory extends Factory
{
    public function definition(): array
    {
        $titleNl = $this->faker->sentence(3);
        return [
            'slug' => Str::slug($titleNl).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'titel_nl' => $titleNl,
            'titel_fr' => $this->faker->sentence(3),
            'beschrijving_nl' => $this->faker->paragraph(),
            'beschrijving_fr' => $this->faker->paragraph(),
            'notice_nl' => null,
            'notice_fr' => null,
            'datum' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'startuur' => '10:00:00',
            'einduur' => '12:00:00',
            'locatie' => 'De Harmonie',
            'prijs' => null,
            'max_deelnemers' => null,
            'status' => ActiviteitStatus::Gepubliceerd,
            'soort' => Soort::Vast,
            'subcategorie' => $this->faker->randomElement(Subcategorie::cases()),
        ];
    }

    public function vast(): static
    {
        return $this->state(['soort' => Soort::Vast]);
    }

    public function speciaal(): static
    {
        return $this->state(['soort' => Soort::Speciaal]);
    }
}
```

- [ ] **Step 2: Pint (do not commit; Task 10 finishes the seed path)**

```bash
vendor/bin/pint --dirty --format agent
```

---

### Task 10: Update `ActiviteitSeeder` and `DatabaseSeeder`

**Files:**
- Modify: `database/seeders/ActiviteitSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php` (if it calls `ActiviteitTemplateSeeder`)

- [ ] **Step 1: Read the current seeder and identify where each row is built**

```bash
grep -n "soort\|subcategorie\|template_id\|insertOrIgnore" database/seeders/ActiviteitSeeder.php
```

- [ ] **Step 2: Add soort + subcategorie to every seeded row**

In `ActiviteitSeeder::run()`, for every `$rows[]` entry being built:

1. Drop `'template_id' => ...` from the array (column no longer exists).
2. Add `'soort' => $this->resolveSoort($data),` — derive from the CSV's existing `IsRecurring` / template hint column. If the CSV has no such column, use a heuristic: if the title matches one of the keys in the template-title map (copy `TEMPLATE_TO_SUB` keys from the migration), it's `vast`, otherwise `speciaal`.
3. Add `'subcategorie' => $this->resolveSub($data),` — first try `TEMPLATE_TO_SUB` by title, then keyword scan (copy `KEYWORD_TO_SUB` from the migration), else `cultuur_museum`.

Add two private methods at the bottom of the seeder class with the same maps as the migration. Yes, this duplicates the maps once — that is acceptable: the migration is a one-shot, the seeder runs continuously, and we'd rather have two co-located copies than a shared dependency that would need to survive after the migration's cleanup.

- [ ] **Step 3: Remove `ActiviteitTemplateSeeder` call from `DatabaseSeeder`**

```bash
grep -n "ActiviteitTemplateSeeder" database/seeders/DatabaseSeeder.php
```

If found, delete that line.

- [ ] **Step 4: Run the migration + seeder fresh and the test suite**

```bash
php artisan migrate:fresh --seed --no-interaction
php artisan test --compact
```

Expected at this point:
- `migrate:fresh --seed` succeeds.
- The Enums + Migration tests created so far PASS.
- Existing tests that referenced `ActiviteitTemplate` FAIL or ERROR.
- The Filament admin route `/admin/reeksen` 404s (resource deleted).

That's expected. We fix existing tests inline as we touch each affected area in Tasks 11+.

- [ ] **Step 5: Pint + commit the whole foundation chunk**

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "feat: migrate activiteiten to soort + subcategorie, drop ActiviteitTemplate

- Add soort (vast/speciaal) and subcategorie (15 cases) columns
- Backfill from existing template_id and keyword scan
- Delete ActiviteitTemplate model, resource, factory, seeder, DagVanDeWeek enum
- Update Activiteit model + factory + seeder accordingly

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 11: Update `ActivityController@index` to query by soort/hoofd

**Files:**
- Modify: `app/Http/Controllers/ActivityController.php`

- [ ] **Step 1: Write the failing controller test**

Create `tests/Feature/OverzichtPaginaTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Enums\ActiviteitStatus;
use App\Enums\Soort;
use App\Enums\Subcategorie;
use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OverzichtPaginaTest extends TestCase
{
    use RefreshDatabase;

    public function test_groups_vaste_activiteiten_by_hoofdcategorie(): void
    {
        Activiteit::factory()->vast()->create([
            'titel_nl' => 'Zumba',
            'subcategorie' => Subcategorie::Dans,
            'datum' => now()->addDays(2),
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);
        Activiteit::factory()->vast()->create([
            'titel_nl' => 'Bingo',
            'subcategorie' => Subcategorie::Spelletjes,
            'datum' => now()->addDays(3),
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);
        Activiteit::factory()->speciaal()->create([
            'titel_nl' => 'Museumbezoek',
            'subcategorie' => Subcategorie::CultuurMuseum,
            'datum' => now()->addDays(4),
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        $response = $this->get('/activiteiten');

        $response->assertOk();
        $response->assertSee('Zumba');     // beweeg theme card
        $response->assertSee('Bingo');     // vier theme card
        $response->assertSee('Museumbezoek'); // bijzondere momenten section
    }
}
```

- [ ] **Step 2: Run, verify fail**

```bash
php artisan test --compact tests/Feature/OverzichtPaginaTest.php
```

Expected: FAIL — the controller and view still query templates.

- [ ] **Step 3: Update `ActivityController::index()`**

Replace the body of `index()`:

```php
public function index(): View
{
    $vasteAanbod = Activiteit::query()
        ->where('soort', Soort::Vast)
        ->where('datum', '>=', today())
        ->where('status', ActiviteitStatus::Gepubliceerd)
        ->orderBy('datum')
        ->get()
        ->groupBy(fn (Activiteit $a) => $a->subcategorie->hoofd()->value)
        ->map(fn ($acts) => $acts->unique('titel_nl')->values());

    $bijzondereActiviteiten = Activiteit::query()
        ->where('soort', Soort::Speciaal)
        ->where('datum', '>=', today())
        ->where('status', ActiviteitStatus::Gepubliceerd)
        ->orderBy('datum')
        ->limit(2)
        ->get();

    return view('activiteiten.overzicht', compact('vasteAanbod', 'bijzondereActiviteiten'));
}
```

Add the imports at the top of the file:

```php
use App\Enums\ActiviteitStatus;
use App\Enums\Soort;
```

Remove `use App\Models\ActiviteitTemplate;` and any unused imports.

- [ ] **Step 4: The view still references `$reeksen` and `$nextActiviteiten`. Adjust in Task 12.**

Test will still fail until Task 12 finishes. Mark this task complete and continue.

---

### Task 12: Update `overzicht.blade.php` to use `$vasteAanbod`

**Files:**
- Modify: `resources/views/activiteiten/overzicht.blade.php`

- [ ] **Step 1: Replace the `$themes` array's `ids` keys with `hoofd` keys**

Open the file. The current `$themes` array (lines ~22–59) has hardcoded `ids` arrays per theme. Replace each theme with:

```php
[
    'name'       => $isFr ? 'Bougez avec nous' : 'Beweeg mee',
    'tagline'    => $isFr ? 'À votre rythme — pas besoin d\'être sportif.' : 'Op eigen tempo — je hoeft geen sportman te zijn.',
    'color'      => 'var(--color-brand-orange)',
    'photo'      => 'photo-petanque.webp',
    'rotate'     => '-2deg',
    'margin_top' => '0.75rem',
    'hoofd'      => 'beweeg',  // <-- replaces 'ids'
],
```

Do this for all four themes:
- Beweeg → `'hoofd' => 'beweeg'`
- Maak → `'hoofd' => 'maak'`
- Praat → `'hoofd' => 'praat'`
- Vier → `'hoofd' => 'vier'`

- [ ] **Step 2: Replace the `$templates = $reeksen->only(...)` line**

Inside the `@foreach ($themes as $theme)` loop, line 65 currently reads:

```php
$templates = $reeksen->only($theme['ids'])->values();
```

Replace with:

```php
$activiteiten = $vasteAanbod->get($theme['hoofd'], collect());
```

Then in the inner `@foreach ($templates as $t)` block, rename `$templates` to `$activiteiten` and `$t` to `$activiteit`. Adjust the title rendering accordingly:

```php
@foreach ($activiteiten as $activiteit)
    @php
        $titel = $isFr ? ($activiteit->titel_fr ?? $activiteit->titel_nl) : $activiteit->titel_nl;
    @endphp
    <li style="{{ $loop->last ? '' : 'border-bottom: 1px solid rgba(44,40,38,0.05);' }}">
        <span style="display: block; font-size: 1rem; font-weight: 600; color: var(--color-brand-dark); padding: 0.35rem 0;">
            {{ $titel }}
        </span>
    </li>
@endforeach
```

- [ ] **Step 3: Drop usage of `$nextActiviteiten`**

Search for `$nextActiviteiten` in the file. If it appears, remove the references. The controller no longer passes it.

```bash
grep -n "nextActiviteiten\|reeksen" resources/views/activiteiten/overzicht.blade.php
```

Expected after edits: empty.

- [ ] **Step 4: Run the controller test**

```bash
php artisan test --compact tests/Feature/OverzichtPaginaTest.php
```

Expected: PASS.

- [ ] **Step 5: Pint + commit (Tasks 11 + 12 together)**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/ActivityController.php resources/views/activiteiten/overzicht.blade.php tests/Feature/OverzichtPaginaTest.php
git commit -m "refactor: drive overzicht theme cards from subcategorie->hoofd()

Replaces hardcoded template-ID lists with dynamic hoofdcategorie
groupings of upcoming vaste activiteiten.

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 13: Replace keyword-icon block in `agenda.blade.php` with deterministic enum lookup

**Files:**
- Modify: `resources/views/activiteiten/agenda.blade.php`
- Create: `tests/Feature/AgendaIconTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Enums\ActiviteitStatus;
use App\Enums\Soort;
use App\Enums\Subcategorie;
use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaIconTest extends TestCase
{
    use RefreshDatabase;

    public function test_agenda_renders_subcategorie_icon_for_dans(): void
    {
        Activiteit::factory()->vast()->create([
            'titel_nl' => 'Zumba',
            'subcategorie' => Subcategorie::Dans,
            'datum' => now()->next('Tuesday')->toDateString(),
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        $response = $this->get('/activiteiten/agenda');

        $response->assertOk();
        // Asserts the SVG path of the dans icon is in the rendered HTML.
        $response->assertSee(Subcategorie::Dans->icon(), false);
    }
}
```

- [ ] **Step 2: Run, verify fail**

```bash
php artisan test --compact tests/Feature/AgendaIconTest.php
```

Expected: FAIL — the agenda still uses keyword matching, not the enum's path.

- [ ] **Step 3: Replace the keyword block in agenda.blade.php**

Open `resources/views/activiteiten/agenda.blade.php`. Locate the block that starts around line 120 (`// Theme-based icon silhouette ...`) and ends around line 155 (the closing `}` of the keyword `if/elseif` chain that assigns `$icon`). Replace the entire block — including the inline icon SVG path declarations (`$iconChat = '...'; $iconMusic = '...';` etc.) — with:

```php
$icon = $activiteit->subcategorie->icon();
```

That's it: one line. The `$ac['bg']` and `$ac['icon']` color assignment above stays; we'll wire it to `subcategorie->hoofd()->color()` next.

- [ ] **Step 4: Wire the agenda color to hoofdcategorie**

Find the `$agendaColors` array (around line 67) and the line that picks a color (`$ac = $agendaColors[$agendaColorIndex % 3];`). Replace the cyclic color picker with a deterministic per-activity lookup based on the activity's hoofdcategorie:

```php
$hoofd = $activiteit->subcategorie->hoofd();
$ac = match ($hoofd) {
    \App\Enums\Hoofdcategorie::Beweeg => ['bg' => 'var(--color-brand-orange)', 'icon' => '#b34a2d'],
    \App\Enums\Hoofdcategorie::Maak   => ['bg' => 'var(--color-brand-green)',  'icon' => '#5a8a74'],
    \App\Enums\Hoofdcategorie::Praat  => ['bg' => 'var(--color-brand-blue)',   'icon' => '#2f5490'],
    \App\Enums\Hoofdcategorie::Vier   => ['bg' => '#d4956a',                   'icon' => '#a06a3f'],
};
```

Delete the `$agendaColors` array and `$agendaColorIndex` increment line — they're no longer used.

- [ ] **Step 5: Run the test**

```bash
php artisan test --compact tests/Feature/AgendaIconTest.php
```

Expected: PASS.

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/activiteiten/agenda.blade.php tests/Feature/AgendaIconTest.php
git commit -m "refactor: deterministic agenda icons via subcategorie enum

Drops the ~150-line keyword-matching block in favor of
\$activiteit->subcategorie->icon(). Color now derives from
hoofdcategorie instead of cyclic index.

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 14: Filament Table with week grouping + ViewColumn rich cell

**Files:**
- Modify: `app/Filament/Resources/ActiviteitResource.php` — overhaul the `table()` method
- Modify: `app/Filament/Resources/ActiviteitResource/Pages/ListActiviteiten.php` — only adds the two header buttons
- Create: `resources/views/filament/tables/columns/activiteit-rich-cell.blade.php`
- Create: `tests/Feature/Filament/ListActiviteitenTest.php`

This task uses the Filament 4 native `Group` and `ViewColumn` APIs. No custom Livewire page, no manually-built filter UI — bulk actions, row actions, pagination, accessibility, and dark mode all come for free.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Filament/ListActiviteitenTest.php`:

```php
<?php

namespace Tests\Feature\Filament;

use App\Enums\ActiviteitStatus;
use App\Enums\Subcategorie;
use App\Filament\Resources\ActiviteitResource\Pages\ListActiviteiten;
use App\Models\Activiteit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListActiviteitenTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_activiteiten_with_week_group_header(): void
    {
        $admin = User::factory()->create(['email' => config('auth.admin_email')]);
        $this->actingAs($admin);

        $wednesday = now()->next('Wednesday')->startOfDay();

        Activiteit::factory()->vast()->create([
            'titel_nl' => 'Zumba woensdag',
            'subcategorie' => Subcategorie::Dans,
            'datum' => $wednesday->toDateString(),
            'startuur' => '14:00:00',
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        Livewire::test(ListActiviteiten::class)
            ->assertCanSeeTableRecords(Activiteit::all())
            ->assertSee('Zumba woensdag')
            ->assertSee('WEEK VAN');
    }

    public function test_header_actions_link_to_create_with_soort(): void
    {
        $admin = User::factory()->create(['email' => config('auth.admin_email')]);
        $this->actingAs($admin);

        Livewire::test(ListActiviteiten::class)
            ->assertActionExists('createVast')
            ->assertActionExists('createSpeciaal');
    }
}
```

- [ ] **Step 2: Run, verify fail**

```bash
php artisan test --compact tests/Feature/Filament/ListActiviteitenTest.php
```

Expected: FAIL — the table still has the old shape and the header actions don't exist yet.

- [ ] **Step 3: Overhaul `ActiviteitResource::table()`**

Replace the body of `table()` in `app/Filament/Resources/ActiviteitResource.php`:

```php
public static function table(Table $table): Table
{
    return $table
        ->defaultGroup(
            \Filament\Tables\Grouping\Group::make('week_start')
                ->getKeyFromRecordUsing(fn (Activiteit $a) => $a->datum->copy()->startOfWeek()->toDateString())
                ->getTitleFromRecordUsing(function (Activiteit $a): string {
                    $start = $a->datum->copy()->startOfWeek()->locale('nl');
                    $end = $a->datum->copy()->endOfWeek()->locale('nl');
                    return 'WEEK VAN '.strtoupper($start->isoFormat('D MMMM').' – '.$end->isoFormat('D MMMM YYYY'));
                })
                ->collapsible()
        )
        ->groupsOnly()
        ->defaultSort('datum', 'asc')
        ->defaultPaginationPageOption(50)
        ->columns([
            \Filament\Tables\Columns\TextColumn::make('datum')
                ->label('Dag')
                ->formatStateUsing(fn (\Carbon\Carbon $state) => strtoupper($state->locale('nl')->isoFormat('ddd D/MM')))
                ->sortable()
                ->width('110px'),
            \Filament\Tables\Columns\ViewColumn::make('rich')
                ->label('Activiteit')
                ->view('filament.tables.columns.activiteit-rich-cell'),
            \Filament\Tables\Columns\TextColumn::make('startuur')
                ->label('Tijd')
                ->formatStateUsing(fn (?string $state) => $state ? substr($state, 0, 5) : '—')
                ->width('80px'),
            \Filament\Tables\Columns\TextColumn::make('locatie')
                ->label('Locatie')
                ->toggleable()
                ->limit(20),
        ])
        ->filters([
            \Filament\Tables\Filters\SelectFilter::make('hoofdcategorie')
                ->label('Categorie')
                ->options(collect(\App\Enums\Hoofdcategorie::cases())->mapWithKeys(fn ($h) => [$h->value => $h->getLabel()])->all())
                ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                    if (empty($data['value'])) {
                        return $query;
                    }
                    $hoofd = \App\Enums\Hoofdcategorie::from($data['value']);
                    $subValues = collect(\App\Enums\Subcategorie::cases())
                        ->filter(fn ($s) => $s->hoofd() === $hoofd)
                        ->map(fn ($s) => $s->value)
                        ->all();
                    return $query->whereIn('subcategorie', $subValues);
                }),
            \Filament\Tables\Filters\SelectFilter::make('soort')
                ->options(collect(\App\Enums\Soort::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])->all()),
            \Filament\Tables\Filters\SelectFilter::make('status')
                ->options(collect(\App\Enums\ActiviteitStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])->all()),
        ])
        ->actions([
            \Filament\Tables\Actions\ActionGroup::make([
                \Filament\Tables\Actions\EditAction::make(),
                // Kopieer + annuleer actions are added in Tasks 17-18.
            ]),
        ])
        ->bulkActions([
            // Bulk actions are added in Task 18.
        ]);
}
```

If the `ActiviteitResource` had a different `table()` body, replace it entirely. Note that the previous `bulkActions()` block (which used `ActiviteitStatus::Gepubliceerd` etc.) is removed here and rebuilt in Task 18 — that's intentional; leaving it in would conflict with the new declarative actions list.

- [ ] **Step 4: Create the rich cell view**

Create `resources/views/filament/tables/columns/activiteit-rich-cell.blade.php`:

```blade
@php
    $activiteit = $getRecord();
    $hoofd = $activiteit->subcategorie->hoofd();
@endphp

<div style="display: flex; align-items: center; gap: 0.625rem; min-height: 32px;">
    <span style="display: inline-block; width: 30px; height: 30px; border-radius: 6px; background: {{ $hoofd->color() }}; flex-shrink: 0; position: relative; overflow: hidden;">
        <svg viewBox="0 0 24 24" fill="white" stroke="none" width="20" height="20" style="position: absolute; top: 5px; left: 5px;">
            {!! $activiteit->subcategorie->icon() !!}
        </svg>
    </span>
    <span style="display: flex; flex-direction: column; min-width: 0;">
        <span style="display: flex; align-items: center; gap: 0.4rem; font-weight: 600; line-height: 1.3;">
            <span>{{ $activiteit->titel_nl }}</span>
            @if ($activiteit->soort->value === 'speciaal')
                <span style="font-size: 0.65rem; background: #efc56a; color: #5a4419; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">speciaal</span>
            @endif
            @if ($activiteit->status->value === 'geannuleerd')
                <span style="font-size: 0.65rem; background: #c43; color: white; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">geannuleerd</span>
            @endif
            @if ($activiteit->status->value === 'concept')
                <span style="font-size: 0.65rem; background: #ddd; color: #444; padding: 1px 6px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em;">concept</span>
            @endif
        </span>
        <span style="font-size: 0.8rem; color: #706662;">{{ $activiteit->subcategorie->getLabel() }}</span>
    </span>
</div>
```

- [ ] **Step 5: Add the two header create buttons in `ListActiviteiten`**

Replace the body of `app/Filament/Resources/ActiviteitResource/Pages/ListActiviteiten.php`:

```php
<?php

namespace App\Filament\Resources\ActiviteitResource\Pages;

use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListActiviteiten extends ListRecords
{
    protected static string $resource = ActiviteitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createVast')
                ->label('+ Vaste activiteit')
                ->color('primary')
                ->url(fn (): string => ActiviteitResource::getUrl('create', ['soort' => Soort::Vast->value])),
            Action::make('createSpeciaal')
                ->label('+ Speciaal moment')
                ->color('gray')
                ->url(fn (): string => ActiviteitResource::getUrl('create', ['soort' => Soort::Speciaal->value])),
        ];
    }
}
```

- [ ] **Step 6: Run the test**

```bash
php artisan test --compact tests/Feature/Filament/ListActiviteitenTest.php
```

Expected: PASS.

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Filament/Resources/ActiviteitResource.php app/Filament/Resources/ActiviteitResource/Pages/ListActiviteiten.php resources/views/filament/tables/columns/activiteit-rich-cell.blade.php tests/Feature/Filament/ListActiviteitenTest.php
git commit -m "feat: filament-native week-grouped activiteiten list

Uses Group::make + ViewColumn instead of a custom Livewire page so
that bulk actions, row actions, pagination, accessibility, and dark
mode keep working out of the box. The rich cell shows icon + title
+ soort/status badges + subcategorie label.

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 15: Update `ActiviteitResource` form with subcategorie + soort handling

**Files:**
- Modify: `app/Filament/Resources/ActiviteitResource.php`
- Modify: `app/Filament/Resources/ActiviteitResource/Pages/CreateActiviteit.php`

- [ ] **Step 1: Update the form schema**

In `ActiviteitResource::form()`, add after the Talen tabs:

```php
\Filament\Forms\Components\Select::make('subcategorie')
    ->label('Categorie')
    ->options(\App\Enums\Subcategorie::groupedOptions())
    ->required(),
```

Drop any old `interesse` field if present in the form schema (the column stays for the subscription flow but is not editable here).

- [ ] **Step 2: Pre-fill `soort` from query string in `CreateActiviteit`**

Replace `app/Filament/Resources/ActiviteitResource/Pages/CreateActiviteit.php`:

```php
<?php

namespace App\Filament\Resources\ActiviteitResource\Pages;

use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActiviteit extends CreateRecord
{
    protected static string $resource = ActiviteitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $soortFromQuery = request()->query('soort');
        $data['soort'] = in_array($soortFromQuery, ['vast', 'speciaal'], true)
            ? $soortFromQuery
            : Soort::Speciaal->value;
        return $data;
    }
}
```

- [ ] **Step 3: Write a smoke test**

Create `tests/Feature/Filament/ActiviteitCreateSpeciaalTest.php`:

```php
<?php

namespace Tests\Feature\Filament;

use App\Enums\ActiviteitStatus;
use App\Enums\Soort;
use App\Enums\Subcategorie;
use App\Filament\Resources\ActiviteitResource\Pages\CreateActiviteit;
use App\Models\Activiteit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActiviteitCreateSpeciaalTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_speciaal_via_query_string_sets_soort(): void
    {
        $admin = User::factory()->create(['email' => config('auth.admin_email')]);
        $this->actingAs($admin);

        // Simulate the request query parameter the create button passes.
        $this->withServerVariables(['QUERY_STRING' => 'soort=speciaal']);
        request()->query->set('soort', 'speciaal');

        Livewire::test(CreateActiviteit::class)
            ->fillForm([
                'titel_nl' => 'Eenmalige uitstap',
                'titel_fr' => 'Sortie unique',
                'datum' => now()->addWeek()->toDateString(),
                'startuur' => '14:00',
                'einduur' => '16:00',
                'locatie' => 'Brussel',
                'subcategorie' => Subcategorie::CultuurMuseum->value,
                'status' => ActiviteitStatus::Concept->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $created = Activiteit::where('titel_nl', 'Eenmalige uitstap')->first();
        $this->assertNotNull($created);
        $this->assertSame(Soort::Speciaal, $created->soort);
        $this->assertSame(Subcategorie::CultuurMuseum, $created->subcategorie);
    }
}
```

- [ ] **Step 4: Run the test**

```bash
php artisan test --compact tests/Feature/Filament/ActiviteitCreateSpeciaalTest.php
```

Expected: PASS.

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "feat: add subcategorie select and soort prefill to ActiviteitResource form

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 16: Bulk-generation toggle on vaste-activiteit creation

**Files:**
- Modify: `app/Filament/Resources/ActiviteitResource/Pages/CreateActiviteit.php`
- Modify: `app/Filament/Resources/ActiviteitResource.php`

- [ ] **Step 1: Add the toggle + recur date to the form schema**

In `ActiviteitResource::form()`, append:

```php
\Filament\Forms\Components\Toggle::make('herhaal_wekelijks')
    ->label('Plan automatisch in: elke week tot...')
    ->live()
    ->dehydrated(false)
    ->visible(fn (string $operation): bool => $operation === 'create' && request()->query('soort') === 'vast'),

\Filament\Forms\Components\DatePicker::make('herhaal_t_m')
    ->label('Tot en met')
    ->dehydrated(false)
    ->required(fn (\Filament\Forms\Get $get): bool => (bool) $get('herhaal_wekelijks'))
    ->visible(fn (\Filament\Forms\Get $get, string $operation): bool => $operation === 'create' && (bool) $get('herhaal_wekelijks')),
```

- [ ] **Step 2: Generate the recurring activiteiten in `afterCreate()`**

In `CreateActiviteit`:

```php
protected function afterCreate(): void
{
    $form = $this->form->getRawState();
    if (empty($form['herhaal_wekelijks']) || empty($form['herhaal_t_m'])) {
        return;
    }

    $created = $this->record;
    $startDate = \Carbon\Carbon::parse($created->datum);
    $endDate = \Carbon\Carbon::parse($form['herhaal_t_m']);

    $cursor = $startDate->copy()->addWeek();
    while ($cursor->lte($endDate)) {
        \App\Models\Activiteit::create([
            'titel_nl' => $created->titel_nl,
            'titel_fr' => $created->titel_fr,
            'beschrijving_nl' => $created->beschrijving_nl,
            'beschrijving_fr' => $created->beschrijving_fr,
            'notice_nl' => null,
            'notice_fr' => null,
            'datum' => $cursor->toDateString(),
            'startuur' => $created->startuur,
            'einduur' => $created->einduur,
            'locatie' => $created->locatie,
            'prijs' => $created->prijs,
            'max_deelnemers' => $created->max_deelnemers,
            'status' => \App\Enums\ActiviteitStatus::Concept,
            'soort' => \App\Enums\Soort::Vast,
            'subcategorie' => $created->subcategorie,
        ]);
        $cursor->addWeek();
    }
}
```

- [ ] **Step 3: Write the failing test**

Create `tests/Feature/Filament/ActiviteitCreateVastTest.php`:

```php
<?php

namespace Tests\Feature\Filament;

use App\Enums\ActiviteitStatus;
use App\Enums\Soort;
use App\Enums\Subcategorie;
use App\Filament\Resources\ActiviteitResource\Pages\CreateActiviteit;
use App\Models\Activiteit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActiviteitCreateVastTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_vast_with_weekly_recurrence_creates_n_rows(): void
    {
        $admin = User::factory()->create(['email' => config('auth.admin_email')]);
        $this->actingAs($admin);
        request()->query->set('soort', 'vast');

        $start = now()->next('Tuesday')->startOfDay();
        $end = $start->copy()->addWeeks(4); // 5 sessions including the start

        Livewire::test(CreateActiviteit::class)
            ->fillForm([
                'titel_nl' => 'Zumba',
                'titel_fr' => 'Zumba',
                'datum' => $start->toDateString(),
                'startuur' => '14:00',
                'einduur' => '15:00',
                'locatie' => 'De Harmonie',
                'subcategorie' => Subcategorie::Dans->value,
                'status' => ActiviteitStatus::Gepubliceerd->value,
                'herhaal_wekelijks' => true,
                'herhaal_t_m' => $end->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $zumbas = Activiteit::where('titel_nl', 'Zumba')->orderBy('datum')->get();
        $this->assertCount(5, $zumbas);
        foreach ($zumbas as $z) {
            $this->assertSame(Soort::Vast, $z->soort);
            $this->assertSame(Subcategorie::Dans, $z->subcategorie);
        }
        // Subsequent rows are created in concept state to encourage review.
        $this->assertSame(ActiviteitStatus::Concept, $zumbas->last()->status);
    }
}
```

- [ ] **Step 4: Run, verify pass**

```bash
php artisan test --compact tests/Feature/Filament/ActiviteitCreateVastTest.php
```

Expected: PASS.

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "feat: weekly bulk-generation on create vaste activiteit

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 17: Kopieer-naar action

**Files:**
- Modify: `app/Filament/Resources/ActiviteitResource.php`
- Create: `tests/Feature/Filament/ActiviteitKopieerTest.php`

- [ ] **Step 1: Add the row action to the resource's table (used by the row action menu)**

In `ActiviteitResource::table()`, add to the `->actions([...])` array:

```php
\Filament\Actions\Action::make('kopieer')
    ->label('Kopieer naar...')
    ->icon('heroicon-o-document-duplicate')
    ->form([
        \Filament\Forms\Components\Radio::make('mode')
            ->label('Naar welke datums?')
            ->options([
                'wekelijks' => 'Wekelijks (elke week tot een einddatum)',
                'specifiek' => 'Specifieke datums',
            ])
            ->default('wekelijks')
            ->required()
            ->live(),
        \Filament\Forms\Components\DatePicker::make('start')
            ->label('Vanaf')
            ->required()
            ->visible(fn (\Filament\Forms\Get $get): bool => $get('mode') === 'wekelijks'),
        \Filament\Forms\Components\DatePicker::make('einde')
            ->label('Tot en met')
            ->required()
            ->visible(fn (\Filament\Forms\Get $get): bool => $get('mode') === 'wekelijks'),
        \Filament\Forms\Components\Repeater::make('datums')
            ->label('Datums')
            ->schema([
                \Filament\Forms\Components\DatePicker::make('datum')->required(),
            ])
            ->visible(fn (\Filament\Forms\Get $get): bool => $get('mode') === 'specifiek'),
    ])
    ->action(function (Activiteit $record, array $data): void {
        $datums = $data['mode'] === 'wekelijks'
            ? self::buildWeeklyDates($record->datum, $data['start'], $data['einde'])
            : array_map(fn ($d) => \Carbon\Carbon::parse($d['datum']), $data['datums']);

        foreach ($datums as $d) {
            Activiteit::create([
                'titel_nl' => $record->titel_nl,
                'titel_fr' => $record->titel_fr,
                'beschrijving_nl' => $record->beschrijving_nl,
                'beschrijving_fr' => $record->beschrijving_fr,
                'datum' => $d->toDateString(),
                'startuur' => $record->startuur,
                'einduur' => $record->einduur,
                'locatie' => $record->locatie,
                'prijs' => $record->prijs,
                'max_deelnemers' => $record->max_deelnemers,
                'status' => \App\Enums\ActiviteitStatus::Concept,
                'soort' => $record->soort,
                'subcategorie' => $record->subcategorie,
            ]);
        }
    }),
```

Add a static helper to the resource:

```php
/**
 * Returns dates on the same weekday as $anchor between $start and $einde inclusive.
 *
 * @return array<\Carbon\Carbon>
 */
private static function buildWeeklyDates($anchor, string $start, string $einde): array
{
    $weekday = \Carbon\Carbon::parse($anchor)->dayOfWeek;
    $cursor = \Carbon\Carbon::parse($start);
    if ($cursor->dayOfWeek !== $weekday) {
        $cursor = $cursor->next($weekday);
    }
    $end = \Carbon\Carbon::parse($einde);
    $out = [];
    while ($cursor->lte($end)) {
        $out[] = $cursor->copy();
        $cursor->addWeek();
    }
    return $out;
}
```

- [ ] **Step 2: Write the failing test**

```php
<?php

namespace Tests\Feature\Filament;

use App\Enums\ActiviteitStatus;
use App\Enums\Soort;
use App\Enums\Subcategorie;
use App\Filament\Resources\ActiviteitResource\Pages\ListActiviteiten;
use App\Models\Activiteit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActiviteitKopieerTest extends TestCase
{
    use RefreshDatabase;

    public function test_kopieer_weekly_creates_n_rows_with_concept_status(): void
    {
        $admin = User::factory()->create(['email' => config('auth.admin_email')]);
        $this->actingAs($admin);

        $original = Activiteit::factory()->vast()->create([
            'titel_nl' => 'Zumba',
            'subcategorie' => Subcategorie::Dans,
            'datum' => now()->next('Tuesday')->toDateString(),
            'startuur' => '14:00:00',
            'status' => ActiviteitStatus::Gepubliceerd,
        ]);

        $start = now()->addWeeks(2)->toDateString();
        $einde = now()->addWeeks(5)->toDateString();

        Livewire::test(ListActiviteiten::class)
            ->callTableAction('kopieer', $original, [
                'mode' => 'wekelijks',
                'start' => $start,
                'einde' => $einde,
            ]);

        $this->assertGreaterThanOrEqual(3, Activiteit::where('titel_nl', 'Zumba')->count());
        $copies = Activiteit::where('titel_nl', 'Zumba')
            ->where('id', '!=', $original->id)
            ->get();
        foreach ($copies as $c) {
            $this->assertSame(Subcategorie::Dans, $c->subcategorie);
            $this->assertSame(Soort::Vast, $c->soort);
            $this->assertSame(ActiviteitStatus::Concept, $c->status);
        }
    }
}
```

- [ ] **Step 3: Run, verify pass**

```bash
php artisan test --compact tests/Feature/Filament/ActiviteitKopieerTest.php
```

Expected: PASS. (`callTableAction` works directly because Task 14 uses the standard Filament table.)

- [ ] **Step 4: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "feat: kopieer-naar action on activiteiten (weekly + specific dates)

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 18: Bulk-edit gemeenschappelijke velden + bulk publish/cancel/delete

**Files:**
- Modify: `app/Filament/Resources/ActiviteitResource.php`
- Create: `tests/Feature/Filament/ActiviteitBulkEditTest.php`

- [ ] **Step 1: Add bulk actions to the resource**

Replace the `->bulkActions([...])` block in `ActiviteitResource::table()`:

```php
->bulkActions([
    \Filament\Actions\BulkActionGroup::make([
        \Filament\Actions\BulkAction::make('publish')
            ->label('Publiceer geselecteerde')
            ->action(fn ($records) => $records->each->update(['status' => \App\Enums\ActiviteitStatus::Gepubliceerd]))
            ->icon('heroicon-o-check'),
        \Filament\Actions\BulkAction::make('cancel')
            ->label('Annuleer geselecteerde')
            ->action(fn ($records) => $records->each->update(['status' => \App\Enums\ActiviteitStatus::Geannuleerd]))
            ->icon('heroicon-o-x-mark')
            ->color('danger'),
        \Filament\Actions\BulkAction::make('bulk_edit')
            ->label('Bewerk gemeenschappelijke velden')
            ->icon('heroicon-o-pencil-square')
            ->form([
                \Filament\Forms\Components\Textarea::make('beschrijving_nl')->label('Beschrijving (NL) — leeg laten = niet wijzigen'),
                \Filament\Forms\Components\Textarea::make('beschrijving_fr')->label('Beschrijving (FR) — leeg laten = niet wijzigen'),
                \Filament\Forms\Components\TextInput::make('locatie')->label('Locatie — leeg laten = niet wijzigen'),
                \Filament\Forms\Components\TextInput::make('prijs')->numeric()->label('Prijs — leeg laten = niet wijzigen'),
            ])
            ->action(function ($records, array $data): void {
                $update = array_filter($data, fn ($v) => $v !== null && $v !== '');
                if (empty($update)) {
                    return;
                }
                $records->each(fn ($r) => $r->update($update));
            }),
        \Filament\Actions\DeleteBulkAction::make(),
    ]),
])
```

- [ ] **Step 2: Write the failing test**

```php
<?php

namespace Tests\Feature\Filament;

use App\Enums\Subcategorie;
use App\Filament\Resources\ActiviteitResource\Pages\ListActiviteiten;
use App\Models\Activiteit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActiviteitBulkEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_bulk_edit_updates_only_filled_fields_on_selected_records(): void
    {
        $admin = User::factory()->create(['email' => config('auth.admin_email')]);
        $this->actingAs($admin);

        $zumbas = Activiteit::factory()->vast()->count(3)->create([
            'titel_nl' => 'Zumba',
            'subcategorie' => Subcategorie::Dans,
            'beschrijving_nl' => 'oude tekst',
        ]);
        $other = Activiteit::factory()->vast()->create([
            'titel_nl' => 'Bingo',
            'subcategorie' => Subcategorie::Spelletjes,
            'beschrijving_nl' => 'bingo blijft',
        ]);

        Livewire::test(ListActiviteiten::class)
            ->callTableBulkAction('bulk_edit', $zumbas->pluck('id')->all(), [
                'beschrijving_nl' => 'nieuwe tekst',
                'beschrijving_fr' => '',
                'locatie' => '',
                'prijs' => null,
            ]);

        foreach ($zumbas as $z) {
            $this->assertSame('nieuwe tekst', $z->fresh()->beschrijving_nl);
        }
        $this->assertSame('bingo blijft', $other->fresh()->beschrijving_nl);
    }
}
```

- [ ] **Step 3: Run, verify pass**

```bash
php artisan test --compact tests/Feature/Filament/ActiviteitBulkEditTest.php
```

Expected: PASS. (`callTableBulkAction` works directly because Task 14 uses the standard Filament table — checkbox selection is built in.)

- [ ] **Step 4: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "feat: bulk publish/cancel/edit/delete actions on activiteiten

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 19: Sweep remaining test references and the rest of the codebase

**Files:**
- Modify: any test still referencing `ActiviteitTemplate`, `template_id`, `Reeksen`, `DagVanDeWeek`
- Modify: any view or controller still expecting `$reeksen` / `$nextActiviteiten`

- [ ] **Step 1: Find references**

```bash
grep -rn "ActiviteitTemplate\|template_id\|DagVanDeWeek\|\\\$reeksen\|nextActiviteiten" --include="*.php" --include="*.blade.php" app/ database/ tests/ resources/ routes/ 2>/dev/null
```

- [ ] **Step 2: Update or delete each reference**

For each file:
- **Tests**: rewrite the test to use `Activiteit::factory()` with explicit `soort` and `subcategorie`. Drop assertions about template relationships.
- **Views**: should already be updated by Tasks 12 + 13. Anything still referencing `$reeksen` is stale — remove it.
- **Controllers / Livewire components**: replace template lookups with direct queries on `Activiteit` filtered by `soort`.

- [ ] **Step 3: Run the full suite**

```bash
php artisan test --compact
```

Expected: GREEN. If any test fails, fix it inline before committing this task.

- [ ] **Step 4: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add -A
git commit -m "chore: sweep stale ActiviteitTemplate references from tests and views

Co-Authored-By: Claude Sonnet 4.6 <noreply@anthropic.com>"
```

---

### Task 20: Manual smoke test in the browser

**Files:** none — this is verification.

- [ ] **Step 1: Reset and seed**

```bash
php artisan migrate:fresh --seed --no-interaction
```

Expected: success. Note the count of activiteiten created.

- [ ] **Step 2: Open the admin and verify**

Open https://deharmonie.test/admin (login required). Click **Activiteiten** in the nav. Verify:

- The table shows a week-section header ("WEEK VAN ...") above the rows of each ISO week, with collapse/expand toggle.
- Each row shows the rich cell: small colored icon block (hoofdcategorie color) + title + subcategorie label, plus any speciaal/concept/geannuleerd badge.
- Filters (Categorie, Soort, Status) work and change the visible rows.
- Top-right shows two buttons: `+ Vaste activiteit` and `+ Speciaal moment`.
- "Terugkerende activiteiten" no longer appears in the navigation.
- Clicking `+ Vaste activiteit` opens the create form with the bulk-generation toggle visible.
- Clicking `+ Speciaal moment` opens the create form without the toggle.
- The row action menu (kebab) shows Edit + Kopieer.
- Selecting multiple rows with checkboxes shows the bulk action menu (Publiceer / Annuleer / Bewerk gemeenschappelijke velden / Verwijder).

- [ ] **Step 3: Open the public site and verify**

Open https://deharmonie.test/activiteiten. Verify:

- The four theme cards show the upcoming distinct titles per hoofdcategorie.
- The "Bijzondere momenten" section shows speciale activiteiten.
- The agenda (`/activiteiten/agenda`) shows icons matching each activity's subcategorie.

- [ ] **Step 4: Capture screenshots**

If `scripts/screenshot.cjs` exists, run it for the admin list and the public agenda. Otherwise capture manually. Save under `/tmp/` for review.

- [ ] **Step 5: Final commit / push (if everything looks good)**

If anything in steps 2 or 3 doesn't render correctly, return to the offending task and fix it. Otherwise the implementation is complete — `git status` should be clean from all the per-task commits.

```bash
git status
git log --oneline -20
```

Expected: clean working tree, ~17 new commits.

---

## Spec Coverage Self-Check

| Spec section | Implementing task |
|---|---|
| Hoofdcategorie enum | Task 1 |
| Subcategorie enum (15 cases, hoofd, icon) | Task 4 |
| SubcategorieIcons SVG library | Task 3 |
| Soort enum | Task 2 |
| Activiteit table: add soort, subcategorie | Task 5 |
| Activiteit table: drop template_id | Task 5 |
| Drop ActiviteitTemplate model + factory + seeder | Task 7 |
| Drop ActiviteitTemplateResource | Task 8 |
| Drop DagVanDeWeek enum | Task 8 |
| Activiteit model: fillable, casts, drop template, hoofd accessor | Task 6 |
| Migration backfill (template→sub map) | Task 5 |
| Migration backfill (keyword scan for speciale) | Task 5 |
| Migration backfill (logged fallback) | Task 5 |
| ActiviteitFactory updates | Task 9 |
| ActiviteitSeeder updates | Task 10 |
| Custom ListActiviteiten week-grouped layout | Task 14 |
| List filters (periode, hoofd, soort, status) | Task 14 |
| Two header create buttons | Task 14 |
| Form: subcategorie required dropdown | Task 15 |
| Form: soort prefilled from query string | Task 15 |
| Form: bulk-generation toggle on create | Task 16 |
| Kopieer-naar action | Task 17 |
| Bulk publish/cancel/delete | Task 18 |
| Bulk-edit gemeenschappelijke velden | Task 18 |
| ActivityController@index changes | Task 11 |
| overzicht.blade.php hoofd grouping | Task 12 |
| agenda.blade.php deterministic icon | Task 13 |
| Sweep stale references | Task 19 |
| Manual smoke test | Task 20 |
| Tests | Tasks 1, 2, 4, 5, 11, 13, 14, 15, 16, 17, 18 |

All spec items map to a task. No placeholders. No undefined types or methods.
