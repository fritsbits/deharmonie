# De Harmonie Laravel Rebuild — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rebuild www.deharmonie.be as a local Laravel 11 application with Filament admin, bilingual NL/FR public frontend, activity registration forms, and Mailtrap email.

**Architecture:** Blade templates for all static pages with Livewire islands for the activity filter, registration form, and language switch. Filament 3 powers the admin panel. Single NL-based slug used across both locales.

**Tech Stack:** Laravel 11, Filament 3, Livewire 3, Tailwind CSS v4, MySQL, Spatie Media Library, Mailtrap

**Spec:** `docs/superpowers/specs/2026-03-24-harmonie-rebuild-design.md`

**Local URL:** `https://harmonie.test` (Laravel Herd — no `php artisan serve` needed)

---

## File Map

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ActivityController.php     # index, show, print actions
│   │   └── PageController.php         # diensten, weekmenu, contact
│   └── Middleware/
│       └── SetLocale.php              # reads /fr/ prefix, sets app locale
├── Livewire/
│   ├── ActivityFilter.php             # month selector + filtered list
│   ├── RegistrationForm.php           # validated form, capacity check, emails
│   └── LanguageSwitch.php             # NL/FR toggle
├── Models/
│   ├── Activiteit.php                 # isBeschikbaar(), getPrijsLabel(), HasMedia
│   └── Deelnameverzoek.php            # belongs to Activiteit
├── Filament/
│   └── Resources/
│       ├── ActiviteitResource.php     # CRUD, NL/FR tabs, status, image
│       └── DeelnameverzoekResource.php # read-only table, status toggle
├── Mail/
│   ├── RegistratieNotificatie.php     # to animatie@deharmonie.be, always NL
│   └── RegistratieBevestiging.php     # to submitter, in submission locale
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php     # dashboard widgets registered here

database/
├── migrations/
│   ├── xxxx_create_activiteiten_table.php
│   └── xxxx_create_deelnameverzoeken_table.php
└── seeders/
    ├── DatabaseSeeder.php
    ├── AdminUserSeeder.php
    └── ActiviteitSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php              # main layout: nav, fonts, Tailwind
│   │   └── print.blade.php            # A4 print layout, no nav
│   ├── components/
│   │   ├── nav.blade.php              # top nav with language switch
│   │   └── footer.blade.php           # contact info, opening hours
│   ├── activiteiten/
│   │   ├── index.blade.php            # homepage: ActivityFilter component
│   │   ├── show.blade.php             # detail: info + RegistrationForm
│   │   └── print.blade.php            # A4 print view
│   ├── pages/
│   │   ├── diensten.blade.php
│   │   ├── weekmenu.blade.php         # Google Doc iframe embed
│   │   └── contact.blade.php
│   ├── livewire/
│   │   ├── activity-filter.blade.php
│   │   ├── registration-form.blade.php
│   │   └── language-switch.blade.php
│   └── mail/
│       ├── registratie-notificatie.blade.php
│       └── registratie-bevestiging.blade.php
└── lang/
    ├── nl/
    │   ├── nav.php
    │   ├── activities.php
    │   └── forms.php
    └── fr/
        ├── nav.php
        ├── activities.php
        └── forms.php

routes/
└── web.php                            # all NL + FR public routes

tests/
├── Feature/
│   ├── ActivityControllerTest.php
│   ├── RegistrationFormTest.php
│   └── BilingualRoutingTest.php
└── Unit/
    ├── ActiviteitTest.php
    └── DeelnameverzoekTest.php
```

---

## Task 1: Laravel Project Setup

**Files:**
- Modify: `.env`
- Modify: `config/app.php`

- [ ] **Step 1: Scaffold Laravel project**

```bash
cd /Users/frederikvincx/Herd
composer create-project laravel/laravel harmonie
cd harmonie
```

- [ ] **Step 2: Configure `.env`**

```env
APP_NAME="De Harmonie"
APP_URL=https://harmonie.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=harmonie
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=<your_mailtrap_user>
MAIL_PASSWORD=<your_mailtrap_pass>
MAIL_FROM_ADDRESS=noreply@deharmonie.be
MAIL_FROM_NAME="De Harmonie"

WEEKLY_MENU_GOOGLE_DOC_URL=
ADMIN_EMAIL=animatie@deharmonie.be
```

- [ ] **Step 3: Create MySQL database**

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS harmonie CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

- [ ] **Step 4: Set default locale in `config/app.php`**

```php
'locale' => 'nl',
'fallback_locale' => 'nl',
'available_locales' => ['nl', 'fr'],
```

- [ ] **Step 5: Verify app boots**

```bash
php artisan key:generate
php artisan about
```

Expected: App info printed, no errors.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: initial Laravel 11 project setup"
```

---

## Task 2: Install Dependencies

**Files:**
- Modify: `composer.json`
- Modify: `package.json`

- [ ] **Step 1: Install PHP packages**

```bash
composer require filament/filament:"^3.0" -W
composer require livewire/livewire:"^3.0"
composer require spatie/laravel-medialibrary:"^11.0"
```

- [ ] **Step 2: Install Filament panel**

```bash
php artisan filament:install --panels
```

When prompted: panel ID = `admin`, path = `admin`.

- [ ] **Step 3: Install Node packages and Tailwind v4**

```bash
npm install
npm install tailwindcss @tailwindcss/vite
```

- [ ] **Step 4: Configure Vite for Tailwind v4**

Replace `vite.config.js`:

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
})
```

- [ ] **Step 5: Set up `resources/css/app.css`**

```css
@import 'tailwindcss';

@theme {
    --font-sans: 'Nunito Sans', sans-serif;
    --font-body: 'Source Sans 3', sans-serif;
    /* Colors extracted from www.deharmonie.be */
    --color-brand-green: #4a7c59;
    --color-brand-green-light: #6a9e79;
    --color-brand-cream: #f5f0e8;
    --color-brand-dark: #2c2c2c;
}
```

- [ ] **Step 6: Publish Spatie Media Library migration**

```bash
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
```

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: install Filament 3, Livewire 3, Spatie Media Library, Tailwind v4"
```

---

## Task 3: Migrations

**Files:**
- Create: `database/migrations/xxxx_create_activiteiten_table.php`
- Create: `database/migrations/xxxx_create_deelnameverzoeken_table.php`

- [ ] **Step 1: Create activiteiten migration**

```bash
php artisan make:migration create_activiteiten_table
```

Edit the generated file:

```php
public function up(): void
{
    Schema::create('activiteiten', function (Blueprint $table) {
        $table->id();
        $table->string('slug')->unique();
        $table->string('titel_nl');
        $table->string('titel_fr');
        $table->text('beschrijving_nl')->nullable();
        $table->text('beschrijving_fr')->nullable();
        $table->text('notice_nl')->nullable();
        $table->text('notice_fr')->nullable();
        $table->date('datum');
        $table->time('startuur');
        $table->time('einduur')->nullable();
        $table->string('locatie')->default('De Harmonie');
        $table->decimal('prijs', 8, 2)->nullable();
        $table->integer('max_deelnemers')->nullable();
        $table->enum('status', ['concept', 'gepubliceerd', 'geannuleerd'])->default('concept');
        $table->timestamps();
    });
}
```

- [ ] **Step 2: Create deelnameverzoeken migration**

```bash
php artisan make:migration create_deelnameverzoeken_table
```

```php
public function up(): void
{
    Schema::create('deelnameverzoeken', function (Blueprint $table) {
        $table->id();
        $table->foreignId('activiteit_id')->constrained('activiteiten')->cascadeOnDelete();
        $table->string('naam');
        $table->string('email');
        $table->string('telefoon')->nullable();
        $table->text('bericht')->nullable();
        $table->enum('status', ['te_contacteren', 'afgehandeld'])->default('te_contacteren');
        $table->timestamps();
    });
}
```

- [ ] **Step 3: Run migrations**

```bash
php artisan migrate
```

Expected: Both tables created plus Spatie media tables.

- [ ] **Step 4: Commit**

```bash
git add -A
git commit -m "feat: add activiteiten and deelnameverzoeken migrations"
```

---

## Task 4: Models

**Files:**
- Create: `app/Models/Activiteit.php`
- Create: `app/Models/Deelnameverzoek.php`
- Create: `tests/Unit/ActiviteitTest.php`

- [ ] **Step 1: Write unit tests for Activiteit**

Create `tests/Unit/ActiviteitTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActiviteitTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_beschikbaar_when_no_max(): void
    {
        $activiteit = Activiteit::factory()->create(['max_deelnemers' => null]);
        $this->assertTrue($activiteit->isBeschikbaar());
    }

    public function test_is_beschikbaar_when_under_max(): void
    {
        $activiteit = Activiteit::factory()->create(['max_deelnemers' => 5]);
        Deelnameverzoek::factory()->count(3)->create(['activiteit_id' => $activiteit->id]);
        $this->assertTrue($activiteit->isBeschikbaar());
    }

    public function test_is_not_beschikbaar_when_at_max(): void
    {
        $activiteit = Activiteit::factory()->create(['max_deelnemers' => 2]);
        Deelnameverzoek::factory()->count(2)->create(['activiteit_id' => $activiteit->id]);
        $this->assertFalse($activiteit->isBeschikbaar());
    }

    public function test_prijs_label_free_when_null(): void
    {
        $activiteit = Activiteit::factory()->make(['prijs' => null]);
        $this->assertEquals('Gratis', $activiteit->getPrijsLabel('nl'));
        $this->assertEquals('Gratuit', $activiteit->getPrijsLabel('fr'));
    }

    public function test_prijs_label_free_when_zero(): void
    {
        $activiteit = Activiteit::factory()->make(['prijs' => 0.00]);
        $this->assertEquals('Gratis', $activiteit->getPrijsLabel('nl'));
    }

    public function test_prijs_label_formatted_when_paid(): void
    {
        $activiteit = Activiteit::factory()->make(['prijs' => 5.00]);
        $this->assertEquals('€ 5,00', $activiteit->getPrijsLabel('nl'));
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test tests/Unit/ActiviteitTest.php
```

Expected: FAIL — class not found.

- [ ] **Step 3: Create Activiteit model**

```bash
php artisan make:model Activiteit --factory
```

`app/Models/Activiteit.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Activiteit extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'slug', 'titel_nl', 'titel_fr',
        'beschrijving_nl', 'beschrijving_fr',
        'notice_nl', 'notice_fr',
        'datum', 'startuur', 'einduur',
        'locatie', 'prijs', 'max_deelnemers', 'status',
    ];

    protected $casts = [
        'datum' => 'date',
        'prijs' => 'decimal:2',
    ];

    public function deelnameverzoeken(): HasMany
    {
        return $this->hasMany(Deelnameverzoek::class);
    }

    public function isBeschikbaar(): bool
    {
        if ($this->max_deelnemers === null) {
            return true;
        }
        $count = $this->deelnameverzoeken()
            ->whereIn('status', ['te_contacteren', 'afgehandeld'])
            ->count();
        return $count < $this->max_deelnemers;
    }

    public function getPrijsLabel(string $locale = 'nl'): string
    {
        if ($this->prijs === null || (float) $this->prijs === 0.0) {
            return $locale === 'fr' ? 'Gratuit' : 'Gratis';
        }
        return '€ ' . number_format((float) $this->prijs, 2, ',', '.');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('afbeelding')->singleFile();
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
}
```

- [ ] **Step 4: Create Activiteit factory**

`database/factories/ActiviteitFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActiviteitFactory extends Factory
{
    public function definition(): array
    {
        $titleNl = $this->faker->sentence(3);
        return [
            'slug' => Str::slug($titleNl) . '-' . $this->faker->unique()->numberBetween(1, 9999),
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
            'status' => 'gepubliceerd',
        ];
    }
}
```

- [ ] **Step 5: Create Deelnameverzoek model and factory**

```bash
php artisan make:model Deelnameverzoek --factory
```

`app/Models/Deelnameverzoek.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deelnameverzoek extends Model
{
    use HasFactory;

    protected $fillable = [
        'activiteit_id', 'naam', 'email', 'telefoon', 'bericht', 'status',
    ];

    public function activiteit(): BelongsTo
    {
        return $this->belongsTo(Activiteit::class);
    }
}
```

`database/factories/DeelnameverzoekFactory.php`:

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
            'naam' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'telefoon' => $this->faker->phoneNumber(),
            'bericht' => $this->faker->sentence(),
            'status' => 'te_contacteren',
        ];
    }
}
```

- [ ] **Step 6: Run tests to verify they pass**

```bash
php artisan test tests/Unit/ActiviteitTest.php
```

Expected: 5 tests pass.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: add Activiteit and Deelnameverzoek models with factories"
```

---

## Task 5: Filament Admin Panel — Activiteiten Resource

**Files:**
- Create: `app/Filament/Resources/ActiviteitResource.php`
- Create: `app/Filament/Resources/ActiviteitResource/Pages/ListActiviteiten.php`
- Create: `app/Filament/Resources/ActiviteitResource/Pages/CreateActiviteit.php`
- Create: `app/Filament/Resources/ActiviteitResource/Pages/EditActiviteit.php`

- [ ] **Step 1: Create admin user seeder**

`database/seeders/AdminUserSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_LOGIN_EMAIL', 'admin@deharmonie.be')],
            [
                'name' => 'Admin',
                'password' => Hash::make(env('ADMIN_LOGIN_PASSWORD', 'secret')),
            ]
        );
    }
}
```

Add to `.env`:
```env
ADMIN_LOGIN_EMAIL=admin@deharmonie.be
ADMIN_LOGIN_PASSWORD=secret
```

Run seeder:
```bash
php artisan db:seed --class=AdminUserSeeder
```

- [ ] **Step 2: Generate Filament resource**

```bash
php artisan make:filament-resource Activiteit --generate
```

- [ ] **Step 3: Replace resource with full implementation**

`app/Filament/Resources/ActiviteitResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActiviteitResource\Pages;
use App\Models\Activiteit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActiviteitResource extends Resource
{
    protected static ?string $model = Activiteit::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Activiteiten';
    protected static ?string $modelLabel = 'Activiteit';
    protected static ?string $pluralModelLabel = 'Activiteiten';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Talen')->tabs([
                Forms\Components\Tabs\Tab::make('Nederlands')->schema([
                    Forms\Components\TextInput::make('titel_nl')
                        ->label('Titel (NL)')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set, $operation) =>
                            $operation === 'create'
                                ? $set('slug', Str::slug($state))
                                : null
                        ),
                    Forms\Components\RichEditor::make('beschrijving_nl')
                        ->label('Beschrijving (NL)'),
                    Forms\Components\Textarea::make('notice_nl')
                        ->label('Opmerking / Annuleringsmelding (NL)'),
                ]),
                Forms\Components\Tabs\Tab::make('Français')->schema([
                    Forms\Components\TextInput::make('titel_fr')
                        ->label('Titre (FR)')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\RichEditor::make('beschrijving_fr')
                        ->label('Description (FR)'),
                    Forms\Components\Textarea::make('notice_fr')
                        ->label('Remarque / Message d\'annulation (FR)'),
                ]),
            ])->columnSpanFull(),

            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\DatePicker::make('datum')
                ->label('Datum')
                ->required(),

            Forms\Components\TimePicker::make('startuur')
                ->label('Startuur')
                ->required()
                ->seconds(false),

            Forms\Components\TimePicker::make('einduur')
                ->label('Einduur')
                ->seconds(false),

            Forms\Components\TextInput::make('locatie')
                ->label('Locatie')
                ->default('De Harmonie')
                ->required(),

            Forms\Components\TextInput::make('prijs')
                ->label('Prijs (€, leeg = gratis)')
                ->numeric()
                ->nullable()
                ->prefix('€'),

            Forms\Components\TextInput::make('max_deelnemers')
                ->label('Max deelnemers (leeg = onbeperkt)')
                ->integer()
                ->nullable(),

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'concept' => 'Concept',
                    'gepubliceerd' => 'Gepubliceerd',
                    'geannuleerd' => 'Geannuleerd',
                ])
                ->default('concept')
                ->required(),

            Forms\Components\SpatieMediaLibraryFileUpload::make('afbeelding')
                ->label('Afbeelding')
                ->collection('afbeelding')
                ->image()
                ->imageEditor()
                ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('datum')
                    ->label('Datum')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'concept' => 'gray',
                        'gepubliceerd' => 'success',
                        'geannuleerd' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('deelnameverzoeken_count')
                    ->label('Inschrijvingen')
                    ->counts('deelnameverzoeken')
                    ->suffix(fn ($record) => $record->max_deelnemers ? ' / ' . $record->max_deelnemers : ''),
            ])
            ->defaultSort('datum', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'concept' => 'Concept',
                        'gepubliceerd' => 'Gepubliceerd',
                        'geannuleerd' => 'Geannuleerd',
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')
                        ->label('Publiceer geselecteerde')
                        ->action(fn ($records) => $records->each->update(['status' => 'gepubliceerd']))
                        ->icon('heroicon-o-check'),
                    Tables\Actions\BulkAction::make('cancel')
                        ->label('Annuleer geselecteerde')
                        ->action(fn ($records) => $records->each->update(['status' => 'geannuleerd']))
                        ->icon('heroicon-o-x-mark')
                        ->color('danger'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActiviteiten::route('/'),
            'create' => Pages\CreateActiviteit::route('/create'),
            'edit' => Pages\EditActiviteit::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 4: Verify admin loads at `/admin`**

Visit `https://harmonie.test/admin` and log in with `admin@deharmonie.be` / `secret`.
Expected: Filament dashboard visible, "Activiteiten" in sidebar.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat: Filament Activiteiten resource with NL/FR tabs and status management"
```

---

## Task 6: Filament Admin Panel — Deelnameverzoeken Resource

**Files:**
- Create: `app/Filament/Resources/DeelnameverzoekResource.php`

- [ ] **Step 1: Generate resource**

```bash
php artisan make:filament-resource Deelnameverzoek --generate
```

- [ ] **Step 2: Replace with full implementation**

`app/Filament/Resources/DeelnameverzoekResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeelnameverzoekResource\Pages;
use App\Models\Deelnameverzoek;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeelnameverzoekResource extends Resource
{
    protected static ?string $model = Deelnameverzoek::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Inschrijvingen';
    protected static ?string $modelLabel = 'Inschrijving';
    protected static ?string $pluralModelLabel = 'Inschrijvingen';

    public static function form(Form $form): Form
    {
        return $form->schema([]);  // view-only, no editing via form
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('naam')
                    ->label('Naam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefoon')
                    ->label('Telefoon')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('activiteit.titel_nl')
                    ->label('Activiteit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangevraagd')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'te_contacteren' => 'warning',
                        'afgehandeld' => 'success',
                        default => 'gray',
                    })
                    ->action(
                        Tables\Actions\Action::make('toggle_status')
                            ->action(function (Deelnameverzoek $record): void {
                                $record->update([
                                    'status' => $record->status === 'te_contacteren'
                                        ? 'afgehandeld'
                                        : 'te_contacteren',
                                ]);
                            })
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'te_contacteren' => 'Te contacteren',
                        'afgehandeld' => 'Afgehandeld',
                    ]),
                Tables\Filters\SelectFilter::make('activiteit')
                    ->relationship('activiteit', 'titel_nl'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeelnameverzoeken::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;  // registrations come from public form only
    }

    public static function canDelete(mixed $record): bool
    {
        return false;  // audit trail — no deletion
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add -A
git commit -m "feat: Filament Deelnameverzoeken resource with status toggle"
```

---

## Task 7: Filament Dashboard Widgets

**Files:**
- Create: `app/Filament/Widgets/UpcomingActivitiesWidget.php`
- Create: `app/Filament/Widgets/OpenRequestsWidget.php`
- Modify: `app/Providers/Filament/AdminPanelProvider.php`

- [ ] **Step 1: Create widget**

```bash
php artisan make:filament-widget UpcomingActivitiesWidget --stats-overview
```

- [ ] **Step 2: Implement UpcomingActivitiesWidget**

`app/Filament/Widgets/UpcomingActivitiesWidget.php`:

```php
<?php

namespace App\Filament\Widgets;

use App\Models\Activiteit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UpcomingActivitiesWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $upcoming = Activiteit::where('status', 'gepubliceerd')
            ->where('datum', '>=', today())
            ->where('datum', '<=', today()->addDays(30))
            ->count();

        $open = \App\Models\Deelnameverzoek::where('status', 'te_contacteren')->count();

        return [
            Stat::make('Komende activiteiten (30 dagen)', $upcoming)
                ->icon('heroicon-o-calendar')
                ->color('success'),
            Stat::make('Openstaande inschrijvingen', $open)
                ->icon('heroicon-o-user-group')
                ->color($open > 0 ? 'warning' : 'gray'),
        ];
    }
}
```

- [ ] **Step 3: Register widgets in AdminPanelProvider**

In `app/Providers/Filament/AdminPanelProvider.php`, add to `->widgets([...]`:

```php
->widgets([
    \App\Filament\Widgets\UpcomingActivitiesWidget::class,
])
```

- [ ] **Step 4: Verify dashboard shows stats**

Visit `https://harmonie.test/admin`. Expected: two stat cards visible.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat: Filament dashboard with upcoming activities and open requests stats"
```

---

## Task 8: SetLocale Middleware and Routes

**Files:**
- Create: `app/Http/Middleware/SetLocale.php`
- Create: `app/Http/Controllers/ActivityController.php`
- Create: `app/Http/Controllers/PageController.php`
- Modify: `routes/web.php`
- Modify: `bootstrap/app.php`

- [ ] **Step 1: Write feature test for bilingual routing**

Create `tests/Feature/BilingualRoutingTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BilingualRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_in_nl(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Activiteiten');
    }

    public function test_homepage_loads_in_fr(): void
    {
        $response = $this->get('/fr/activites');
        $response->assertStatus(200);
        $response->assertSee('Activités');
    }

    public function test_activity_detail_resolves_by_slug(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);
        $this->get('/activiteiten/' . $activiteit->slug)->assertStatus(200);
        $this->get('/fr/activites/' . $activiteit->slug)->assertStatus(200);
    }

    public function test_nl_locale_set_on_default_routes(): void
    {
        $this->get('/');
        $this->assertEquals('nl', app()->getLocale());
    }

    public function test_fr_locale_set_on_fr_routes(): void
    {
        $this->get('/fr/activites');
        $this->assertEquals('fr', app()->getLocale());
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test tests/Feature/BilingualRoutingTest.php
```

Expected: FAIL — routes not defined.

- [ ] **Step 3: Create SetLocale middleware**

```bash
php artisan make:middleware SetLocale
```

`app/Http/Middleware/SetLocale.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next, string $locale = 'nl')
    {
        app()->setLocale($locale);
        return $next($request);
    }
}
```

- [ ] **Step 4: Create stub controllers**

```bash
php artisan make:controller ActivityController
php artisan make:controller PageController
```

`app/Http/Controllers/ActivityController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Activiteit;

class ActivityController extends Controller
{
    public function index()
    {
        return view('activiteiten.index');
    }

    public function show(string $slug)
    {
        $activiteit = Activiteit::where('slug', $slug)->firstOrFail();
        return view('activiteiten.show', compact('activiteit'));
    }

    public function print(string $slug)
    {
        $activiteit = Activiteit::where('slug', $slug)->firstOrFail();
        return view('activiteiten.print', compact('activiteit'));
    }
}
```

`app/Http/Controllers/PageController.php`:

```php
<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function diensten() { return view('pages.diensten'); }
    public function weekmenu() { return view('pages.weekmenu'); }
    public function contact() { return view('pages.contact'); }
}
```

- [ ] **Step 5: Define routes**

`routes/web.php`:

```php
<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// NL routes (default, no prefix)
Route::middleware('locale:nl')->group(function () {
    Route::get('/', [ActivityController::class, 'index'])->name('nl.activiteiten.index');
    Route::get('/activiteiten/{slug}', [ActivityController::class, 'show'])->name('nl.activiteiten.show');
    Route::get('/activiteiten/{slug}/print', [ActivityController::class, 'print'])->name('nl.activiteiten.print');
    Route::get('/diensten', [PageController::class, 'diensten'])->name('nl.diensten');
    Route::get('/weekmenu', [PageController::class, 'weekmenu'])->name('nl.weekmenu');
    Route::get('/contact', [PageController::class, 'contact'])->name('nl.contact');
});

// FR routes
Route::prefix('fr')->middleware('locale:fr')->group(function () {
    Route::get('/activites', [ActivityController::class, 'index'])->name('fr.activiteiten.index');
    Route::get('/activites/{slug}', [ActivityController::class, 'show'])->name('fr.activiteiten.show');
    Route::get('/activites/{slug}/imprimer', [ActivityController::class, 'print'])->name('fr.activiteiten.print');
    Route::get('/services', [PageController::class, 'diensten'])->name('fr.diensten');
    Route::get('/menu-semaine', [PageController::class, 'weekmenu'])->name('fr.weekmenu');
    Route::get('/contact', [PageController::class, 'contact'])->name('fr.contact');
});
```

- [ ] **Step 6: Register middleware in `bootstrap/app.php`**

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'locale' => \App\Http\Middleware\SetLocale::class,
    ]);
})
```

- [ ] **Step 7: Create stub views (enough to make tests pass)**

```bash
mkdir -p resources/views/activiteiten resources/views/pages resources/views/layouts
```

Create `resources/views/activiteiten/index.blade.php`:
```html
<!DOCTYPE html><html><body>Activiteiten / Activités</body></html>
```

Create `resources/views/activiteiten/show.blade.php`:
```html
<!DOCTYPE html><html><body>{{ $activiteit->titel_nl }}</body></html>
```

Create `resources/views/activiteiten/print.blade.php`:
```html
<!DOCTYPE html><html><body>Print: {{ $activiteit->titel_nl }}</body></html>
```

Create `resources/views/layouts/print.blade.php`:
```html
<!DOCTYPE html><html><body>@yield('content')</body></html>
```

Create `resources/views/pages/diensten.blade.php`:
```html
<!DOCTYPE html><html><body>Diensten</body></html>
```

Create `resources/views/pages/weekmenu.blade.php`:
```html
<!DOCTYPE html><html><body>Weekmenu</body></html>
```

Create `resources/views/pages/contact.blade.php`:
```html
<!DOCTYPE html><html><body>Contact</body></html>
```

- [ ] **Step 8: Run tests to verify they pass**

```bash
php artisan test tests/Feature/BilingualRoutingTest.php
```

Expected: 5 tests pass.

- [ ] **Step 9: Commit**

```bash
git add -A
git commit -m "feat: bilingual NL/FR routing with SetLocale middleware"
```

---

## Task 9: Language Files

**Files:**
- Create: `lang/nl/nav.php`, `lang/nl/activities.php`, `lang/nl/forms.php`
- Create: `lang/fr/nav.php`, `lang/fr/activities.php`, `lang/fr/forms.php`

- [ ] **Step 1: Create NL language files**

`lang/nl/nav.php`:
```php
<?php
return [
    'activities' => 'Activiteiten',
    'services' => 'Diensten',
    'menu' => 'Weekmenu',
    'contact' => 'Contact',
    'language_switch' => 'Français',
];
```

`lang/nl/activities.php`:
```php
<?php
return [
    'upcoming' => 'Komende activiteiten',
    'no_activities' => 'Geen activiteiten in :month',
    'cancelled' => 'Geannuleerd',
    'free' => 'Gratis',
    'full' => 'Volzet',
    'register' => 'Inschrijven',
    'location' => 'Locatie',
    'date' => 'Datum',
    'time' => 'Uur',
    'price' => 'Prijs',
    'previous_month' => 'Vorige maand',
    'next_month' => 'Volgende maand',
    'print' => 'Afdrukken',
    'back' => '← Terug naar alle activiteiten',
    'detail' => 'Meer info',
    'cancellation_notice' => 'Deze activiteit is geannuleerd.',
    'registration_closed' => 'Inschrijving gesloten (activiteit geannuleerd).',
];
```

`lang/nl/forms.php`:
```php
<?php
return [
    'name' => 'Naam',
    'email' => 'E-mailadres',
    'phone' => 'Telefoonnummer (optioneel)',
    'message' => 'Bericht (optioneel)',
    'submit' => 'Inschrijven',
    'success' => 'Je inschrijving is ontvangen. We nemen snel contact op.',
    'required' => 'Dit veld is verplicht.',
    'invalid_email' => 'Vul een geldig e-mailadres in.',
    'rate_limit' => 'Je hebt te veel inschrijvingen verstuurd. Probeer later opnieuw.',
];
```

- [ ] **Step 2: Create FR language files**

`lang/fr/nav.php`:
```php
<?php
return [
    'activities' => 'Activités',
    'services' => 'Services',
    'menu' => 'Menu de la semaine',
    'contact' => 'Contact',
    'language_switch' => 'Nederlands',
];
```

`lang/fr/activities.php`:
```php
<?php
return [
    'upcoming' => 'Activités à venir',
    'no_activities' => 'Pas d\'activités en :month',
    'cancelled' => 'Annulé',
    'free' => 'Gratuit',
    'full' => 'Complet',
    'register' => 'S\'inscrire',
    'location' => 'Lieu',
    'date' => 'Date',
    'time' => 'Heure',
    'price' => 'Prix',
    'previous_month' => 'Mois précédent',
    'next_month' => 'Mois suivant',
    'print' => 'Imprimer',
    'back' => '← Retour aux activités',
    'detail' => 'Plus d\'info',
    'cancellation_notice' => 'Cette activité est annulée.',
    'registration_closed' => 'Inscription fermée (activité annulée).',
];
```

`lang/fr/forms.php`:
```php
<?php
return [
    'name' => 'Nom',
    'email' => 'Adresse e-mail',
    'phone' => 'Numéro de téléphone (optionnel)',
    'message' => 'Message (optionnel)',
    'submit' => 'S\'inscrire',
    'success' => 'Votre inscription a été reçue. Nous vous contacterons bientôt.',
    'required' => 'Ce champ est obligatoire.',
    'invalid_email' => 'Veuillez entrer une adresse e-mail valide.',
    'rate_limit' => 'Trop d\'inscriptions envoyées. Veuillez réessayer plus tard.',
];
```

- [ ] **Step 3: Commit**

```bash
git add -A
git commit -m "feat: NL/FR language files for navigation, activities, and forms"
```

---

## Task 10: Main Layout and Design

**Files:**
- Modify: `resources/views/layouts/app.blade.php`
- Create: `resources/views/components/nav.blade.php`
- Create: `resources/views/components/footer.blade.php`
- Modify: `resources/views/layouts/print.blade.php`

- [ ] **Step 1: Build main app layout**

`resources/views/layouts/app.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'De Harmonie') — De Harmonie</title>
    <meta name="description" content="@yield('description', 'Lokaal dienstencentrum en sociaal restaurant in de Noordwijk, Brussel.')">
    @if(View::hasSection('og_title'))
    <meta property="og:title" content="@yield('og_title')">
    <meta property="og:description" content="@yield('og_description')">
    <meta property="og:type" content="website">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700;800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-brand-cream font-body text-brand-dark min-h-screen flex flex-col">
    <x-nav />
    <main class="flex-1">
        @yield('content')
    </main>
    <x-footer />
    @livewireScripts
</body>
</html>
```

- [ ] **Step 2: Build nav component**

Inspect the original site's nav at https://www.deharmonie.be to match colors/structure. Then build:

`resources/views/components/nav.blade.php`:

```blade
<header class="bg-white shadow-sm">
    <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="flex items-center gap-3">
            <img src="https://www.deharmonie.be/assets/images/logo.png"
                 alt="De Harmonie"
                 class="h-12 w-auto"
                 onerror="this.style.display='none'">
            <span class="font-sans font-bold text-xl text-brand-green">De Harmonie</span>
        </a>
        <nav class="hidden md:flex items-center gap-6 text-sm font-semibold">
            <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
               class="hover:text-brand-green transition-colors">
               {{ __('nav.activities') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.diensten') }}"
               class="hover:text-brand-green transition-colors">
               {{ __('nav.services') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.weekmenu') }}"
               class="hover:text-brand-green transition-colors">
               {{ __('nav.menu') }}
            </a>
            <a href="{{ route(app()->getLocale() . '.contact') }}"
               class="hover:text-brand-green transition-colors">
               {{ __('nav.contact') }}
            </a>
            <livewire:language-switch />
        </nav>
        <!-- Mobile menu button (Alpine.js) -->
        <button x-data @click="$dispatch('toggle-menu')" class="md:hidden p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <!-- Mobile nav -->
    <div x-data="{ open: false }" @toggle-menu.window="open = !open" x-show="open" class="md:hidden bg-white border-t px-4 py-3 space-y-3">
        <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}" class="block font-semibold">{{ __('nav.activities') }}</a>
        <a href="{{ route(app()->getLocale() . '.diensten') }}" class="block font-semibold">{{ __('nav.services') }}</a>
        <a href="{{ route(app()->getLocale() . '.weekmenu') }}" class="block font-semibold">{{ __('nav.menu') }}</a>
        <a href="{{ route(app()->getLocale() . '.contact') }}" class="block font-semibold">{{ __('nav.contact') }}</a>
    </div>
</header>
```

- [ ] **Step 3: Build footer component**

`resources/views/components/footer.blade.php`:

```blade
<footer class="bg-brand-green text-white mt-12">
    <div class="max-w-5xl mx-auto px-4 py-8 grid md:grid-cols-3 gap-6 text-sm">
        <div>
            <h3 class="font-sans font-bold text-lg mb-2">De Harmonie</h3>
            <p>VZW Buurtwerk Noordwijk</p>
            <p>Antwerpsesteenweg 24</p>
            <p>1000 Brussel</p>
        </div>
        <div>
            <h3 class="font-sans font-bold text-lg mb-2">{{ app()->getLocale() === 'fr' ? 'Heures d\'ouverture' : 'Openingsuren' }}</h3>
            <p>{{ app()->getLocale() === 'fr' ? 'Lun–Ven' : 'Ma–Vr' }}: 9:30–16:30</p>
            <p>{{ app()->getLocale() === 'fr' ? 'Sam' : 'Za' }}: 10:00–14:00</p>
        </div>
        <div>
            <h3 class="font-sans font-bold text-lg mb-2">Contact</h3>
            <p><a href="tel:0220328048" class="underline">02 203 28 48</a></p>
            <p><a href="mailto:info@deharmonie.be" class="underline">info@deharmonie.be</a></p>
        </div>
    </div>
    <div class="border-t border-white/20 text-center py-3 text-xs opacity-70">
        © {{ date('Y') }} VZW Buurtwerk Noordwijk
    </div>
</footer>
```

- [ ] **Step 4: Build print layout**

`resources/views/layouts/print.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Activiteit') — De Harmonie</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { font-size: 12pt; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="p-8 font-body text-brand-dark">
    <div class="no-print mb-4">
        <a href="javascript:window.print()" class="bg-brand-green text-white px-4 py-2 rounded">{{ __('activities.print') }}</a>
        <a href="javascript:history.back()" class="ml-4 underline">{{ __('activities.back') }}</a>
    </div>
    @yield('content')
</body>
</html>
```

- [ ] **Step 5: Build npm and verify layout**

```bash
npm run dev
```

Visit `https://harmonie.test`. Expected: Layout visible with nav and footer (stub content page).

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: main app layout, nav, footer, and print layout matching original design"
```

---

## Task 11: ActivityFilter Livewire Component

**Files:**
- Create: `app/Livewire/ActivityFilter.php`
- Create: `resources/views/livewire/activity-filter.blade.php`

- [ ] **Step 1: Write Livewire feature test**

Create `tests/Feature/ActivityControllerTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_shows_published_activities(): void
    {
        $gepubliceerd = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $concept = Activiteit::factory()->create(['status' => 'concept']);

        Livewire::test(\App\Livewire\ActivityFilter::class)
            ->assertSee($gepubliceerd->titel_nl)
            ->assertDontSee($concept->titel_nl);
    }

    public function test_cancelled_activity_shows_badge(): void
    {
        $geannuleerd = Activiteit::factory()->create([
            'status' => 'geannuleerd',
            'datum' => now()->format('Y-m-d'),
        ]);

        Livewire::test(\App\Livewire\ActivityFilter::class)
            ->assertSee($geannuleerd->titel_nl)
            ->assertSee('Geannuleerd');
    }

    public function test_empty_state_shown_when_no_activities(): void
    {
        Livewire::test(\App\Livewire\ActivityFilter::class)
            ->assertSee('Geen activiteiten');
    }

    public function test_month_filter_changes_results(): void
    {
        $thisMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->format('Y-m-d'),
        ]);
        $nextMonth = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'datum' => now()->addMonth()->format('Y-m-d'),
        ]);

        Livewire::test(\App\Livewire\ActivityFilter::class)
            ->assertSee($thisMonth->titel_nl)
            ->assertDontSee($nextMonth->titel_nl)
            ->call('nextMonth')
            ->assertSee($nextMonth->titel_nl)
            ->assertDontSee($thisMonth->titel_nl);
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test tests/Feature/ActivityControllerTest.php
```

Expected: FAIL — class not found.

- [ ] **Step 3: Create ActivityFilter component**

```bash
php artisan make:livewire ActivityFilter
```

`app/Livewire/ActivityFilter.php`:

```php
<?php

namespace App\Livewire;

use App\Models\Activiteit;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Collection;

class ActivityFilter extends Component
{
    public int $year;
    public int $month;

    public function mount(): void
    {
        $this->year = now()->year;
        $this->month = now()->month;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $date->year;
        $this->month = $date->month;
    }

    public function getActiviteitenProperty(): Collection
    {
        return Activiteit::whereIn('status', ['gepubliceerd', 'geannuleerd'])
            ->whereYear('datum', $this->year)
            ->whereMonth('datum', $this->month)
            ->orderBy('datum')
            ->orderBy('startuur')
            ->get();
    }

    public function getMonthLabelProperty(): string
    {
        return Carbon::create($this->year, $this->month, 1)
            ->locale(app()->getLocale())
            ->isoFormat('MMMM YYYY');
    }

    public function render()
    {
        return view('livewire.activity-filter');
    }
}
```

- [ ] **Step 4: Create ActivityFilter view**

`resources/views/livewire/activity-filter.blade.php`:

```blade
<div>
    {{-- Month navigation --}}
    <div class="flex items-center justify-between mb-6">
        <button wire:click="previousMonth"
                class="flex items-center gap-1 text-sm font-semibold text-brand-green hover:underline">
            ← {{ __('activities.previous_month') }}
        </button>
        <h2 class="font-sans font-bold text-xl capitalize">{{ $this->monthLabel }}</h2>
        <button wire:click="nextMonth"
                class="flex items-center gap-1 text-sm font-semibold text-brand-green hover:underline">
            {{ __('activities.next_month') }} →
        </button>
    </div>

    {{-- Activity list --}}
    @forelse ($this->activiteiten as $activiteit)
        <article class="bg-white rounded-lg shadow-sm p-5 mb-4 flex gap-4 {{ $activiteit->status === 'geannuleerd' ? 'opacity-60' : '' }}">
            @if ($activiteit->getFirstMediaUrl('afbeelding'))
                <img src="{{ $activiteit->getFirstMediaUrl('afbeelding', 'thumb') }}"
                     alt="{{ $activiteit->titel }}"
                     class="w-24 h-24 object-cover rounded-md flex-shrink-0">
            @endif
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="font-sans font-bold text-lg leading-tight">
                        <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                           class="hover:text-brand-green transition-colors">
                            {{ $activiteit->titel }}
                        </a>
                    </h3>
                    @if ($activiteit->status === 'geannuleerd')
                        <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded flex-shrink-0">
                            {{ __('activities.cancelled') }}
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM') }}
                    · {{ substr($activiteit->startuur, 0, 5) }}
                    @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
                    · {{ $activiteit->locatie }}
                </p>
                <p class="text-sm mt-2 text-gray-700 line-clamp-2">
                    {!! strip_tags($activiteit->beschrijving) !!}
                </p>
                <div class="mt-3 flex items-center gap-4">
                    <span class="text-sm font-semibold text-brand-green">
                        {{ $activiteit->getPrijsLabel(app()->getLocale()) }}
                    </span>
                    <a href="{{ route(app()->getLocale() . '.activiteiten.show', $activiteit->slug) }}"
                       class="text-sm font-semibold text-brand-green hover:underline">
                        {{ __('activities.detail') }} →
                    </a>
                </div>
            </div>
        </article>
    @empty
        <div class="text-center py-12 text-gray-500">
            <p class="text-lg">{{ __('activities.no_activities', ['month' => $this->monthLabel]) }}</p>
        </div>
    @endforelse
</div>
```

- [ ] **Step 5: Update homepage view**

`resources/views/activiteiten/index.blade.php`:

```blade
@extends('layouts.app')

@section('title', __('activities.upcoming'))

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-10">
        <h1 class="font-sans font-extrabold text-3xl text-brand-dark mb-8">
            {{ __('activities.upcoming') }}
        </h1>
        <livewire:activity-filter />
    </div>
@endsection
```

- [ ] **Step 6: Run tests**

```bash
php artisan test tests/Feature/ActivityControllerTest.php
```

Expected: 4 tests pass.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: ActivityFilter Livewire component with month navigation and empty state"
```

---

## Task 12: Activity Detail Page

**Files:**
- Modify: `app/Http/Controllers/ActivityController.php`
- Modify: `resources/views/activiteiten/show.blade.php`
- Modify: `resources/views/activiteiten/print.blade.php`

- [ ] **Step 1: Write feature test for detail page**

Add to `tests/Feature/ActivityControllerTest.php`:

```php
public function test_activity_detail_shows_cancellation_banner(): void
{
    $activiteit = Activiteit::factory()->create([
        'status' => 'geannuleerd',
        'notice_nl' => 'Deze activiteit gaat niet door.',
    ]);
    $response = $this->get('/activiteiten/' . $activiteit->slug);
    $response->assertSee('Deze activiteit gaat niet door.');
    $response->assertDontSee('Inschrijven');
}

public function test_activity_detail_shows_registration_form_for_published(): void
{
    $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);
    $response = $this->get('/activiteiten/' . $activiteit->slug);
    $response->assertStatus(200);
    $response->assertSee('Inschrijven');
}

public function test_activity_detail_shows_full_message_when_at_capacity(): void
{
    $activiteit = Activiteit::factory()->create([
        'status' => 'gepubliceerd',
        'max_deelnemers' => 1,
    ]);
    \App\Models\Deelnameverzoek::factory()->create(['activiteit_id' => $activiteit->id]);

    $response = $this->get('/activiteiten/' . $activiteit->slug);
    $response->assertSee('Volzet');
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test tests/Feature/ActivityControllerTest.php --filter="detail"
```

Expected: FAIL.

- [ ] **Step 3: Build detail view**

`resources/views/activiteiten/show.blade.php`:

```blade
@extends('layouts.app')

@section('title', $activiteit->titel)
@section('description', strip_tags($activiteit->beschrijving ?? ''))
@section('og_title', $activiteit->titel)
@section('og_description', strip_tags($activiteit->beschrijving ?? ''))

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <a href="{{ route(app()->getLocale() . '.activiteiten.index') }}"
       class="text-sm text-brand-green font-semibold hover:underline">
        {{ __('activities.back') }}
    </a>

    <div class="mt-6 bg-white rounded-xl shadow-sm overflow-hidden">
        @if ($activiteit->getFirstMediaUrl('afbeelding'))
            <img src="{{ $activiteit->getFirstMediaUrl('afbeelding') }}"
                 alt="{{ $activiteit->titel }}"
                 class="w-full h-64 object-cover">
        @endif

        <div class="p-6 md:p-8">
            {{-- Cancellation banner --}}
            @if ($activiteit->status === 'geannuleerd')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <p class="font-semibold text-red-700">
                        {{ $activiteit->notice ?? __('activities.cancellation_notice') }}
                    </p>
                </div>
            @endif

            <div class="flex items-start justify-between gap-4 flex-wrap">
                <h1 class="font-sans font-extrabold text-3xl text-brand-dark">
                    {{ $activiteit->titel }}
                </h1>
                <div class="flex gap-2">
                    <a href="{{ route(app()->getLocale() . '.activiteiten.print', $activiteit->slug) }}"
                       class="text-sm border border-brand-green text-brand-green px-3 py-1 rounded hover:bg-brand-green hover:text-white transition-colors">
                        {{ __('activities.print') }}
                    </a>
                </div>
            </div>

            {{-- Meta info --}}
            <dl class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <dt class="font-semibold text-gray-500 uppercase tracking-wide text-xs">{{ __('activities.date') }}</dt>
                    <dd class="mt-1 font-semibold">
                        {{ $activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY') }}
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-500 uppercase tracking-wide text-xs">{{ __('activities.time') }}</dt>
                    <dd class="mt-1 font-semibold">
                        {{ substr($activiteit->startuur, 0, 5) }}
                        @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-500 uppercase tracking-wide text-xs">{{ __('activities.location') }}</dt>
                    <dd class="mt-1 font-semibold">{{ $activiteit->locatie }}</dd>
                </div>
                <div>
                    <dt class="font-semibold text-gray-500 uppercase tracking-wide text-xs">{{ __('activities.price') }}</dt>
                    <dd class="mt-1 font-semibold text-brand-green">
                        {{ $activiteit->getPrijsLabel(app()->getLocale()) }}
                    </dd>
                </div>
            </dl>

            {{-- Description --}}
            @if ($activiteit->beschrijving)
                <div class="mt-6 prose max-w-none">
                    {!! $activiteit->beschrijving !!}
                </div>
            @endif

            {{-- Registration --}}
            @if ($activiteit->status === 'gepubliceerd')
                <div class="mt-8 border-t pt-8">
                    <livewire:registration-form :activiteit="$activiteit" />
                </div>
            @elseif ($activiteit->status === 'geannuleerd')
                <p class="mt-6 text-sm text-gray-500 italic">{{ __('activities.registration_closed') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 4: Build print view**

`resources/views/activiteiten/print.blade.php`:

```blade
@extends('layouts.print')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <p class="text-sm font-semibold text-gray-500 uppercase tracking-widest">De Harmonie</p>
        <h1 class="font-sans font-extrabold text-3xl mt-2">{{ $activiteit->titel }}</h1>
        @if ($activiteit->status === 'geannuleerd')
            <p class="mt-2 text-red-600 font-bold">{{ __('activities.cancelled') }}</p>
        @endif
    </div>

    <table class="w-full text-sm mb-6 border-collapse">
        <tr class="border-b">
            <th class="text-left py-2 pr-4 font-semibold w-32">{{ __('activities.date') }}</th>
            <td class="py-2">{{ $activiteit->datum->locale(app()->getLocale())->isoFormat('dddd D MMMM YYYY') }}</td>
        </tr>
        <tr class="border-b">
            <th class="text-left py-2 pr-4 font-semibold">{{ __('activities.time') }}</th>
            <td class="py-2">
                {{ substr($activiteit->startuur, 0, 5) }}
                @if ($activiteit->einduur) – {{ substr($activiteit->einduur, 0, 5) }} @endif
            </td>
        </tr>
        <tr class="border-b">
            <th class="text-left py-2 pr-4 font-semibold">{{ __('activities.location') }}</th>
            <td class="py-2">{{ $activiteit->locatie }}</td>
        </tr>
        <tr>
            <th class="text-left py-2 pr-4 font-semibold">{{ __('activities.price') }}</th>
            <td class="py-2">{{ $activiteit->getPrijsLabel(app()->getLocale()) }}</td>
        </tr>
    </table>

    @if ($activiteit->beschrijving)
        <div class="prose text-sm">
            {!! $activiteit->beschrijving !!}
        </div>
    @endif

    @if ($activiteit->notice)
        <div class="mt-6 border border-red-300 rounded p-3 text-sm text-red-700">
            {{ $activiteit->notice }}
        </div>
    @endif

    <div class="mt-8 text-xs text-gray-400 border-t pt-4">
        De Harmonie · Antwerpsesteenweg 24, 1000 Brussel · 02 203 28 48 · info@deharmonie.be
    </div>
</div>
@endsection
```

- [ ] **Step 5: Run tests**

```bash
php artisan test tests/Feature/ActivityControllerTest.php
```

Expected: All tests pass.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: activity detail and print views with cancellation banner and capacity display"
```

---

## Task 13: RegistrationForm Livewire Component

**Files:**
- Create: `app/Livewire/RegistrationForm.php`
- Create: `resources/views/livewire/registration-form.blade.php`
- Create: `app/Mail/RegistratieNotificatie.php`
- Create: `app/Mail/RegistratieBevestiging.php`
- Create: `resources/views/mail/registratie-notificatie.blade.php`
- Create: `resources/views/mail/registratie-bevestiging.blade.php`

- [ ] **Step 1: Write registration form tests**

Create `tests/Feature/RegistrationFormTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_registration_creates_record(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Jan Janssen')
            ->set('email', 'jan@example.com')
            ->set('telefoon', '0471234567')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('deelnameverzoeken', [
            'activiteit_id' => $activiteit->id,
            'naam' => 'Jan Janssen',
            'email' => 'jan@example.com',
            'status' => 'te_contacteren',
        ]);
    }

    public function test_registration_sends_two_emails(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Jan Janssen')
            ->set('email', 'jan@example.com')
            ->call('submit');

        Mail::assertSent(\App\Mail\RegistratieNotificatie::class);
        Mail::assertSent(\App\Mail\RegistratieBevestiging::class);
    }

    public function test_form_requires_naam_and_email(): void
    {
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->call('submit')
            ->assertHasErrors(['naam', 'email']);
    }

    public function test_form_shows_full_when_at_capacity(): void
    {
        $activiteit = Activiteit::factory()->create([
            'status' => 'gepubliceerd',
            'max_deelnemers' => 1,
        ]);
        Deelnameverzoek::factory()->create(['activiteit_id' => $activiteit->id]);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->assertSee('Volzet');
    }

    public function test_honeypot_spam_field_blocks_submission(): void
    {
        Mail::fake();
        $activiteit = Activiteit::factory()->create(['status' => 'gepubliceerd']);

        Livewire::test(\App\Livewire\RegistrationForm::class, ['activiteit' => $activiteit])
            ->set('naam', 'Spammer')
            ->set('email', 'spam@example.com')
            ->set('honeypot', 'filled-by-bot')
            ->call('submit');

        $this->assertDatabaseCount('deelnameverzoeken', 0);
        Mail::assertNothingSent();
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

```bash
php artisan test tests/Feature/RegistrationFormTest.php
```

Expected: FAIL.

- [ ] **Step 3: Create mail classes**

```bash
php artisan make:mail RegistratieNotificatie --markdown=mail.registratie-notificatie
php artisan make:mail RegistratieBevestiging --markdown=mail.registratie-bevestiging
```

`app/Mail/RegistratieNotificatie.php`:

```php
<?php

namespace App\Mail;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistratieNotificatie extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Deelnameverzoek $verzoek,
        public readonly Activiteit $activiteit,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to: config('mail.admin_address', 'animatie@deharmonie.be'),
            subject: 'Nieuwe inschrijving: ' . $this->activiteit->titel_nl,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.registratie-notificatie');
    }
}
```

`app/Mail/RegistratieBevestiging.php`:

```php
<?php

namespace App\Mail;

use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistratieBevestiging extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Deelnameverzoek $verzoek,
        public readonly Activiteit $activiteit,
        public readonly string $locale,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->locale === 'fr'
            ? 'Confirmation d\'inscription : ' . $this->activiteit->titel_fr
            : 'Bevestiging inschrijving: ' . $this->activiteit->titel_nl;

        return new Envelope(
            to: $this->verzoek->email,
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.registratie-bevestiging');
    }
}
```

Add to `.env`:
```env
MAIL_ADMIN_ADDRESS=animatie@deharmonie.be
```

Add to `config/mail.php`:
```php
'admin_address' => env('MAIL_ADMIN_ADDRESS', 'animatie@deharmonie.be'),
```

- [ ] **Step 4: Create mail views**

`resources/views/mail/registratie-notificatie.blade.php`:

```blade
<x-mail::message>
# Nieuwe inschrijving

Er is een nieuwe inschrijving ontvangen voor **{{ $activiteit->titel_nl }}**.

**Activiteit:** {{ $activiteit->titel_nl }}
**Datum:** {{ $activiteit->datum->format('d/m/Y') }} om {{ substr($activiteit->startuur, 0, 5) }}

---

**Naam:** {{ $verzoek->naam }}
**E-mail:** {{ $verzoek->email }}
**Telefoon:** {{ $verzoek->telefoon ?? '—' }}
**Bericht:** {{ $verzoek->bericht ?? '—' }}

<x-mail::button url="{{ config('app.url') }}/admin/deelnameverzoeken">
Bekijk in admin
</x-mail::button>

Met vriendelijke groeten,<br>
De Harmonie
</x-mail::message>
```

`resources/views/mail/registratie-bevestiging.blade.php`:

```blade
<x-mail::message>
@if ($locale === 'fr')
# Confirmation d'inscription

Bonjour {{ $verzoek->naam }},

Nous avons bien reçu votre inscription pour **{{ $activiteit->titel_fr }}**.

**Date :** {{ $activiteit->datum->locale('fr')->isoFormat('dddd D MMMM YYYY') }}
**Heure :** {{ substr($activiteit->startuur, 0, 5) }}
**Lieu :** {{ $activiteit->locatie }}

Nous vous contacterons bientôt pour confirmer votre participation.

Cordialement,<br>
De Harmonie
@else
# Bevestiging inschrijving

Hallo {{ $verzoek->naam }},

We hebben je inschrijving ontvangen voor **{{ $activiteit->titel_nl }}**.

**Datum:** {{ $activiteit->datum->locale('nl')->isoFormat('dddd D MMMM YYYY') }}
**Uur:** {{ substr($activiteit->startuur, 0, 5) }}
**Locatie:** {{ $activiteit->locatie }}

We nemen snel contact met je op om je deelname te bevestigen.

Met vriendelijke groeten,<br>
De Harmonie
@endif
</x-mail::message>
```

- [ ] **Step 5: Create RegistrationForm component**

```bash
php artisan make:livewire RegistrationForm
```

`app/Livewire/RegistrationForm.php`:

```php
<?php

namespace App\Livewire;

use App\Mail\RegistratieBevestiging;
use App\Mail\RegistratieNotificatie;
use App\Models\Activiteit;
use App\Models\Deelnameverzoek;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RegistrationForm extends Component
{
    public Activiteit $activiteit;

    #[Validate('required|min:2|max:255')]
    public string $naam = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|max:50')]
    public string $telefoon = '';

    #[Validate('nullable|max:1000')]
    public string $bericht = '';

    public string $honeypot = '';  // must remain empty

    public bool $submitted = false;

    public function submit(): void
    {
        // Honeypot check — silently abort for bots
        if ($this->honeypot !== '') {
            return;
        }

        // Rate limiting
        $key = 'registration:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->addError('email', __('forms.rate_limit'));
            return;
        }
        RateLimiter::hit($key, 60);

        $this->validate();

        // Capacity check
        if (! $this->activiteit->isBeschikbaar()) {
            return;
        }

        $verzoek = Deelnameverzoek::create([
            'activiteit_id' => $this->activiteit->id,
            'naam' => $this->naam,
            'email' => $this->email,
            'telefoon' => $this->telefoon ?: null,
            'bericht' => $this->bericht ?: null,
            'status' => 'te_contacteren',
        ]);

        $locale = app()->getLocale();

        Mail::send(new RegistratieNotificatie($verzoek, $this->activiteit));
        Mail::send(new RegistratieBevestiging($verzoek, $this->activiteit, $locale));

        $this->submitted = true;
    }

    public function render()
    {
        return view('livewire.registration-form');
    }
}
```

- [ ] **Step 6: Create RegistrationForm view**

`resources/views/livewire/registration-form.blade.php`:

```blade
<div>
    @if ($submitted)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center">
            <svg class="w-10 h-10 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="font-semibold text-green-700">{{ __('forms.success') }}</p>
        </div>
    @elseif (! $activiteit->isBeschikbaar())
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-amber-700 font-semibold">
            {{ __('activities.full') }}
        </div>
    @else
        <h2 class="font-sans font-bold text-xl mb-4">{{ __('activities.register') }}</h2>
        <form wire:submit="submit" class="space-y-4">
            {{-- Honeypot --}}
            <div class="hidden" aria-hidden="true">
                <input type="text" wire:model="honeypot" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('forms.name') }} *</label>
                <input type="text" wire:model="naam"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-green @error('naam') border-red-400 @enderror">
                @error('naam') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('forms.email') }} *</label>
                <input type="email" wire:model="email"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-green @error('email') border-red-400 @enderror">
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('forms.phone') }}</label>
                <input type="tel" wire:model="telefoon"
                       class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-green">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('forms.message') }}</label>
                <textarea wire:model="bericht" rows="3"
                          class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-brand-green"></textarea>
            </div>

            <button type="submit"
                    class="w-full bg-brand-green text-white font-bold py-3 rounded-lg hover:bg-brand-green-light transition-colors"
                    wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('forms.submit') }}</span>
                <span wire:loading>...</span>
            </button>
        </form>
    @endif
</div>
```

- [ ] **Step 7: Run all tests**

```bash
php artisan test tests/Feature/RegistrationFormTest.php
```

Expected: 5 tests pass.

- [ ] **Step 8: Commit**

```bash
git add -A
git commit -m "feat: RegistrationForm with honeypot, rate limiting, capacity check, and email notifications"
```

---

## Task 14: LanguageSwitch Component and Static Pages

**Files:**
- Create: `app/Livewire/LanguageSwitch.php`
- Create: `resources/views/livewire/language-switch.blade.php`
- Modify: `resources/views/pages/diensten.blade.php`
- Modify: `resources/views/pages/weekmenu.blade.php`
- Modify: `resources/views/pages/contact.blade.php`

- [ ] **Step 1: Create LanguageSwitch component**

```bash
php artisan make:livewire LanguageSwitch
```

`app/Livewire/LanguageSwitch.php`:

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class LanguageSwitch extends Component
{
    public function switchLocale(string $locale): void
    {
        $current = request()->path();
        $targetUrl = $locale === 'fr'
            ? $this->toFrench($current)
            : $this->toDutch($current);

        $this->redirect($targetUrl);
    }

    private function toFrench(string $path): string
    {
        if (str_starts_with($path, 'fr/')) {
            return '/' . $path;
        }
        $map = [
            'activiteiten' => 'activites',
            'diensten' => 'services',
            'weekmenu' => 'menu-semaine',
            'contact' => 'contact',
            'print' => 'imprimer',
        ];
        $segments = explode('/', trim($path, '/'));
        $translated = array_map(fn($s) => $map[$s] ?? $s, $segments);
        return '/fr/' . implode('/', $translated);
    }

    private function toDutch(string $path): string
    {
        $path = preg_replace('#^fr/#', '', ltrim($path, '/'));
        $map = [
            'activites' => 'activiteiten',
            'services' => 'diensten',
            'menu-semaine' => 'weekmenu',
            'contact' => 'contact',
            'imprimer' => 'print',
        ];
        $segments = explode('/', $path);
        $translated = array_map(fn($s) => $map[$s] ?? $s, $segments);
        return '/' . implode('/', $translated);
    }

    public function render()
    {
        return view('livewire.language-switch');
    }
}
```

`resources/views/livewire/language-switch.blade.php`:

```blade
<div>
    <button wire:click="switchLocale('{{ app()->getLocale() === 'nl' ? 'fr' : 'nl' }}')"
            class="text-sm font-semibold border border-brand-green text-brand-green px-3 py-1 rounded hover:bg-brand-green hover:text-white transition-colors">
        {{ __('nav.language_switch') }}
    </button>
</div>
```

- [ ] **Step 2: Build static pages**

`resources/views/pages/diensten.blade.php`:

```blade
@extends('layouts.app')
@section('title', __('nav.services'))
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-sans font-extrabold text-3xl mb-8">{{ __('nav.services') }}</h1>
    <div class="grid md:grid-cols-2 gap-6">
        @foreach ([
            ['icon' => '🤝', 'nl' => 'Sociale begeleiding', 'fr' => 'Accompagnement social',
             'desc_nl' => 'Navigatie in het Brussels socioculturele landschap. Integratie in het eerstelijnsnetwerk.',
             'desc_fr' => 'Navigation dans le paysage socioculturel bruxellois. Intégration dans le réseau de première ligne.'],
            ['icon' => '🍽️', 'nl' => 'Sociaal restaurant', 'fr' => 'Restaurant social',
             'desc_nl' => 'Dagschotel aan verminderd tarief voor senioren. Afhaal en thuisbezorging mogelijk.',
             'desc_fr' => 'Plat du jour à tarif réduit pour seniors. Emporter et livraison à domicile possibles.'],
            ['icon' => '🧹', 'nl' => 'Praktische hulp', 'fr' => 'Aide pratique',
             'desc_nl' => 'Boodschappen, vervoer, poetshulp, kleine herstellingen en kledingwinkel.',
             'desc_fr' => 'Courses, transport, aide ménagère, petites réparations et magasin de vêtements.'],
            ['icon' => '🏠', 'nl' => 'Zaalverhuur & catering', 'fr' => 'Location de salle & traiteur',
             'desc_nl' => 'Voor buurtbewoners en lokale organisaties.',
             'desc_fr' => 'Pour les riverains et les organisations locales.'],
        ] as $service)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="text-3xl mb-3">{{ $service['icon'] }}</div>
                <h2 class="font-sans font-bold text-lg mb-2">
                    {{ app()->getLocale() === 'fr' ? $service['fr'] : $service['nl'] }}
                </h2>
                <p class="text-sm text-gray-600">
                    {{ app()->getLocale() === 'fr' ? $service['desc_fr'] : $service['desc_nl'] }}
                </p>
            </div>
        @endforeach
    </div>
    <div class="mt-8 bg-brand-green text-white rounded-xl p-6">
        <h2 class="font-sans font-bold text-xl mb-2">
            {{ app()->getLocale() === 'fr' ? 'Grand Nettoyage (Grote Kuis)' : 'Grote Kuis' }}
        </h2>
        <p class="text-sm">
            {{ app()->getLocale() === 'fr'
                ? 'Service combiné de nettoyage, petites réparations et aide administrative. Exemples : nettoyage du four, robinetterie, vitres, tapis et peinture.'
                : 'Gecombineerde dienst: poetsen, kleine herstellingen en administratieve hulp. Voorbeelden: oven reinigen, kranen, ramen, tapijten en schilderwerk.' }}
        </p>
        <p class="mt-3 text-sm font-semibold">
            <a href="mailto:diensten@deharmonie.be" class="underline">diensten@deharmonie.be</a>
            · 02 203 28 48
        </p>
    </div>
</div>
@endsection
```

`resources/views/pages/weekmenu.blade.php`:

```blade
@extends('layouts.app')
@section('title', __('nav.menu'))
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-sans font-extrabold text-3xl mb-6">{{ __('nav.menu') }}</h1>
    @if (config('app.weekly_menu_url'))
        <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="height: 800px;">
            <iframe src="{{ config('app.weekly_menu_url') }}"
                    class="w-full h-full border-0"
                    title="{{ __('nav.menu') }}">
            </iframe>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-8 text-center text-gray-500">
            <p>{{ app()->getLocale() === 'fr' ? 'Le menu de la semaine n\'est pas encore disponible.' : 'Het weekmenu is nog niet beschikbaar.' }}</p>
        </div>
    @endif
</div>
@endsection
```

Add to `config/app.php`:
```php
'weekly_menu_url' => env('WEEKLY_MENU_GOOGLE_DOC_URL'),
```

`resources/views/pages/contact.blade.php`:

```blade
@extends('layouts.app')
@section('title', 'Contact')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">
    <h1 class="font-sans font-extrabold text-3xl mb-8">Contact</h1>
    <div class="grid md:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
            <div>
                <h2 class="font-sans font-bold text-lg mb-1">{{ app()->getLocale() === 'fr' ? 'Adresse' : 'Adres' }}</h2>
                <p>Antwerpsesteenweg 24<br>1000 Brussel</p>
            </div>
            <div>
                <h2 class="font-sans font-bold text-lg mb-1">{{ app()->getLocale() === 'fr' ? 'Heures d\'ouverture' : 'Openingsuren' }}</h2>
                <p>{{ app()->getLocale() === 'fr' ? 'Lun–Ven' : 'Ma–Vr' }}: 9:30–16:30</p>
                <p>{{ app()->getLocale() === 'fr' ? 'Sam' : 'Za' }}: 10:00–14:00</p>
            </div>
            <div>
                <h2 class="font-sans font-bold text-lg mb-1">{{ app()->getLocale() === 'fr' ? 'Téléphone' : 'Telefoon' }}</h2>
                <p><a href="tel:0220328048" class="text-brand-green font-semibold underline">02 203 28 48</a></p>
            </div>
            <div>
                <h2 class="font-sans font-bold text-lg mb-1">Email</h2>
                <p><a href="mailto:info@deharmonie.be" class="text-brand-green font-semibold underline">info@deharmonie.be</a></p>
                <p class="text-sm text-gray-500 mt-1">{{ app()->getLocale() === 'fr' ? 'Activités :' : 'Activiteiten:' }} <a href="mailto:animatie@deharmonie.be" class="underline">animatie@deharmonie.be</a></p>
                <p class="text-sm text-gray-500">{{ app()->getLocale() === 'fr' ? 'Services :' : 'Diensten:' }} <a href="mailto:diensten@deharmonie.be" class="underline">diensten@deharmonie.be</a></p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2519.5386387793987!2d4.352!3d50.8520!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTDCsDUxJzA3LjIiTiA0wrAyMScwNy4yIkU!5e0!3m2!1snl!2sbe!4v1234567890"
                class="w-full h-full min-h-64 border-0"
                allowfullscreen
                loading="lazy"
                title="Kaart">
            </iframe>
        </div>
    </div>
</div>
@endsection
```

- [ ] **Step 3: Commit**

```bash
git add -A
git commit -m "feat: LanguageSwitch component and static pages (diensten, weekmenu, contact)"
```

---

## Task 15: Seeders with Initial Activity Data

**Files:**
- Modify: `database/seeders/DatabaseSeeder.php`
- Create: `database/seeders/ActiviteitSeeder.php`

- [ ] **Step 1: Create ActiviteitSeeder with live site data**

`database/seeders/ActiviteitSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\Activiteit;
use Illuminate\Database\Seeder;

class ActiviteitSeeder extends Seeder
{
    public function run(): void
    {
        $activiteiten = [
            [
                'slug' => 'engelse-conversatietafel-2026-04',
                'titel_nl' => 'Engelse conversatietafel',
                'titel_fr' => 'Table de conversation anglaise',
                'beschrijving_nl' => '<p>Oefen je Engels in een gezellige groep. Voor alle niveaus.</p>',
                'beschrijving_fr' => '<p>Pratiquez votre anglais dans un groupe convivial. Tous niveaux.</p>',
                'datum' => '2026-04-07',
                'startuur' => '10:30:00',
                'einduur' => '12:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'spaanse-conversatietafel-2026-04',
                'titel_nl' => 'Spaanse conversatietafel',
                'titel_fr' => 'Table de conversation espagnole',
                'beschrijving_nl' => '<p>Oefen je Spaans met andere enthousiastelingen.</p>',
                'beschrijving_fr' => '<p>Pratiquez votre espagnol avec d\'autres passionnés.</p>',
                'datum' => '2026-04-09',
                'startuur' => '10:00:00',
                'einduur' => '12:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'country-line-dance-2026-04',
                'titel_nl' => 'Country Line Dance',
                'titel_fr' => 'Country Line Dance',
                'beschrijving_nl' => '<p>Dansen voor iedereen! Geen ervaring nodig.</p>',
                'beschrijving_fr' => '<p>La danse pour tous ! Aucune expérience requise.</p>',
                'datum' => '2026-04-09',
                'startuur' => '14:00:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'naaiworkshop-2026-04',
                'titel_nl' => 'Naaiworkshop',
                'titel_fr' => 'Atelier couture',
                'beschrijving_nl' => '<p>Creatief naaien voor beginners en gevorderden.</p>',
                'beschrijving_fr' => '<p>Couture créative pour débutants et confirmés.</p>',
                'datum' => '2026-04-15',
                'startuur' => '13:30:00',
                'einduur' => '16:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'zumba-2026-04',
                'titel_nl' => 'Zumba',
                'titel_fr' => 'Zumba',
                'beschrijving_nl' => '<p>Beweeg mee op Latijns-Amerikaansemuziek. Lekker energiek!</p>',
                'beschrijving_fr' => '<p>Bougez sur des rythmes latino-américains. Super énergisant!</p>',
                'datum' => '2026-04-17',
                'startuur' => '14:00:00',
                'einduur' => '15:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
            [
                'slug' => 'sociaal-infopunt-2026-04',
                'titel_nl' => 'Sociaal infopunt',
                'titel_fr' => 'Point d\'information social',
                'beschrijving_nl' => '<p>Vragen over rechten, uitkeringen of administratie? Kom langs!</p>',
                'beschrijving_fr' => '<p>Questions sur vos droits, allocations ou démarches administratives ? Venez!</p>',
                'datum' => '2026-04-22',
                'startuur' => '11:00:00',
                'einduur' => '14:00:00',
                'locatie' => 'De Harmonie',
                'prijs' => null,
                'status' => 'gepubliceerd',
            ],
        ];

        foreach ($activiteiten as $data) {
            Activiteit::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
```

- [ ] **Step 2: Wire up DatabaseSeeder**

`database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ActiviteitSeeder::class,
        ]);
    }
}
```

- [ ] **Step 3: Run fresh migration with seeders**

```bash
php artisan migrate:fresh --seed
```

Expected: Tables created, admin user created, 6 activities seeded.

- [ ] **Step 4: Verify in browser**

Visit `https://harmonie.test`. Expected: 6 activities visible for April 2026 (or adjust month selector).

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat: seeders with admin user and initial activity data from live site"
```

---

## Task 16: Run Full Test Suite

- [ ] **Step 1: Run all tests**

```bash
php artisan test
```

Expected: All tests pass, zero failures.

- [ ] **Step 2: Fix any failures**

Address any remaining test failures before proceeding.

- [ ] **Step 3: Build production assets**

```bash
npm run build
```

Expected: No build errors.

- [ ] **Step 4: Final smoke test in browser**

Check the following manually:
- [ ] `https://harmonie.test/` — activity list with month filter
- [ ] `https://harmonie.test/fr/activites` — same in French
- [ ] Click an activity — detail page loads
- [ ] `/fr/activites/{slug}` — French detail page
- [ ] Submit registration form — success message + check Mailtrap inbox
- [ ] `https://harmonie.test/activiteiten/{slug}/print` — print-ready layout
- [ ] `https://harmonie.test/diensten` and `/fr/services`
- [ ] `https://harmonie.test/weekmenu` — iframe placeholder or Google Doc
- [ ] `https://harmonie.test/contact`
- [ ] `https://harmonie.test/admin` — Filament dashboard, all resources working
- [ ] Language switch toggle — navigates to correct FR/NL equivalent

- [ ] **Step 5: Final commit**

```bash
git add -A
git commit -m "feat: complete De Harmonie Laravel rebuild — all features implemented and tested"
```

---

## Quick Reference

```bash
# Fresh start
php artisan migrate:fresh --seed

# Run tests
php artisan test

# Dev server (not needed with Herd — but for asset compilation)
npm run dev

# Admin login
URL: https://harmonie.test/admin
Email: admin@deharmonie.be  (set via ADMIN_LOGIN_EMAIL in .env)
Password: secret            (set via ADMIN_LOGIN_PASSWORD in .env)
```
