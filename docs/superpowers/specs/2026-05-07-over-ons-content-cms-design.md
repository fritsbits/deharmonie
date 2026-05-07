# Over ons content + Jaarverslag CMS — Design

## Goal

Make the year-specific content on the Over ons page editable through the Filament admin, so the team can:

1. Replace the linked yearly report (PDF) without a developer.
2. Update the three impact-stat numbers and their bilingual descriptions.
3. Bump the year shown in the jaarverslag link label.

History is explicitly out of scope: only the current report is kept. Uploading a new PDF replaces the previous one.

## Scope

In scope:

- A singleton Filament page (`Over ons`) under a new `Inhoud` navigation group.
- One singleton Eloquent model `OverOnsContent` backing the page.
- Spatie Media Library single-file collection `jaarverslag` for the PDF (PDF only, max 20 MB).
- Locale-aware rendering of impact descriptions and conditional rendering of the jaarverslag link card on the public Over ons page.
- A one-off data migration that seeds the singleton with the current production values and copies the existing `public/docs/jaarverslag-2025.pdf` into the media library, then deletes the loose PDF file.

Out of scope (deliberate, per Q1 / Q4):

- CMS editing of the rest of the Over ons page (story, vision, service cards, quotes, team blurb, CTA) — these stay in Blade and language files.
- Stat *labels* (Bezoekers / Maaltijden / Activiteiten) — these stay in the language files; only numbers and descriptions are editable.
- Multiple yearly reports / archive listing.
- Per-locale label overrides for the jaarverslag link — the label is `{{ __('pages.over_ons_visie_jaarverslag_label') }} {{ year }}` (e.g. `Jaarverslag 2026` / `Rapport annuel 2026`).

## Data model

New table `over_ons_content`:

| Column                    | Type                  | Notes                                |
| ------------------------- | --------------------- | ------------------------------------ |
| `id`                      | bigIncrements         | Always `1` in practice (singleton).  |
| `jaarverslag_jaar`        | smallInteger nullable | Year shown next to the link label.   |
| `impact_1_aantal`         | string(20)            | E.g. `250`, `60+`. String, not int.  |
| `impact_1_omschrijving_nl`| string(120)           |                                      |
| `impact_1_omschrijving_fr`| string(120)           |                                      |
| `impact_2_aantal`         | string(20)            |                                      |
| `impact_2_omschrijving_nl`| string(120)           |                                      |
| `impact_2_omschrijving_fr`| string(120)           |                                      |
| `impact_3_aantal`         | string(20)            |                                      |
| `impact_3_omschrijving_nl`| string(120)           |                                      |
| `impact_3_omschrijving_fr`| string(120)           |                                      |
| `created_at`              | timestamp             |                                      |
| `updated_at`              | timestamp             |                                      |

The PDF is stored via Spatie Media Library on the model (no column on this table). The `media` table already exists.

## Model: `App\Models\OverOnsContent`

```php
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
        return static::firstOrCreate(['id' => 1]);
    }

    public function impactOmschrijving(int $stat): string
    {
        $key = "impact_{$stat}_omschrijving_" . app()->getLocale();
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

The model follows the existing `Activiteit` pattern (`HasMedia` + `InteractsWithMedia`) verbatim.

## Filament page

`App\Filament\Pages\OverOnsContent` — a custom Filament 4 page (not a resource), using the official "Singular resource via custom page" pattern.

Key properties:

```php
protected string $view = 'filament.pages.over-ons-content';

protected static string | UnitEnum | null $navigationGroup = 'Inhoud';

protected static ?string $navigationLabel = 'Over ons';

protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

public ?array $data = [];
```

`mount()` fills the form from `OverOnsContent::current()`. `save()` validates via `$this->form->getState()`, fills the same singleton record, persists, and shows a success notification. Spatie Media Library uploads are handled by the `SpatieMediaLibraryFileUpload` field — no manual save needed.

Form schema (`Filament\Schemas\Schema`):

```php
return $schema
    ->components([
        Form::make([
            Section::make('Jaarverslag')
                ->description('Laat het PDF-veld leeg om het jaarverslag-blok op de Over ons-pagina te verbergen.')
                ->schema([
                    TextInput::make('jaarverslag_jaar')
                        ->numeric()
                        ->minValue(2000)
                        ->maxValue(2100)
                        ->label('Jaar'),
                    SpatieMediaLibraryFileUpload::make('jaarverslag')
                        ->collection('jaarverslag')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(20480)
                        ->label('PDF-bestand'),
                ]),
            Section::make('Impactcijfers')
                ->schema([
                    Grid::make(3)->schema([
                        Group::make([
                            Placeholder::make('label_1')
                                ->label('Categorie')
                                ->content('Bezoekers'),
                            TextInput::make('impact_1_aantal')->required()->maxLength(20)->label('Aantal'),
                            TextInput::make('impact_1_omschrijving_nl')->required()->maxLength(120)->label('Omschrijving (NL)'),
                            TextInput::make('impact_1_omschrijving_fr')->required()->maxLength(120)->label('Omschrijving (FR)'),
                        ]),
                        Group::make([/* impact_2, label "Maaltijden" */]),
                        Group::make([/* impact_3, label "Activiteiten" */]),
                    ]),
                ]),
        ])
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make([
                    Action::make('save')->submit('save')->keyBindings(['mod+s']),
                ]),
            ]),
    ])
    ->record($this->getRecord())
    ->statePath('data');
```

Page Blade view (`resources/views/filament/pages/over-ons-content.blade.php`):

```blade
<x-filament::page>
    {{ $this->form }}
</x-filament::page>
```

## Frontend integration

`PageController::overOns()` passes the singleton to the view:

```php
public function overOns(): View
{
    return view('pages.over-ons', [
        'content' => OverOnsContent::current(),
    ]);
}
```

In `resources/views/pages/over-ons.blade.php`:

- Each impact stat block: replace `{{ __('pages.over_ons_impact_N_number') }}` with `{{ $content->{"impact_{$n}_aantal"} }}` (or three explicit references), and replace `{{ __('pages.over_ons_impact_N_desc') }}` with `{{ $content->impactOmschrijving($n) }}`. Stat labels stay on `{{ __('pages.over_ons_impact_N_label') }}`.

- Jaarverslag link card becomes conditional:

  ```blade
  @if ($content->getJaarverslagUrl())
      <div class="over-ons-visie-aside">
          <a href="{{ $content->getJaarverslagUrl() }}" target="_blank" rel="noopener noreferrer" ...>
              <div>
                  <div class="over-ons-jaarverslag-title" ...>
                      {{ __('pages.over_ons_visie_jaarverslag_label') }} {{ $content->jaarverslag_jaar }}
                  </div>
                  <div ...>pdf, {{ $content->getJaarverslagSize() }}</div>
              </div>
          </a>
      </div>
  @endif
  ```

  When the card is omitted, the surrounding `over-ons-visie-layout` flex container lets the left column fill the row naturally — no extra layout work.

Translations in `lang/{nl,fr}/pages.php`:

- Remove keys driven by the DB: `over_ons_impact_{1,2,3}_number`, `over_ons_impact_{1,2,3}_desc`, `over_ons_visie_jaarverslag_link`, `over_ons_visie_jaarverslag_size`.
- Keep `over_ons_impact_{1,2,3}_label` (stat label stays in code per Q4).
- Keep `over_ons_visie_jaarverslag_label` (`Jaarverslag` / `Rapport annuel`) — concatenated with `$content->jaarverslag_jaar` at render time.

## Initial data migration

A one-off migration (mirrors `2026_04_22_150240_sync_activiteit_and_weekmenu_data`):

1. Short-circuit when `app()->runningUnitTests()` so the test suite starts clean.
2. Insert the singleton row with values matching the current production translations:
   - `jaarverslag_jaar = 2025`
   - `impact_1_aantal = '250'`, `impact_1_omschrijving_nl = 'wekelijks bij ons over de vloer'`, `impact_1_omschrijving_fr = 'chaque semaine chez nous'`
   - `impact_2_aantal = '4500'`, `impact_2_omschrijving_nl = 'maaltijden per maand'`, `impact_2_omschrijving_fr = 'repas par mois'`
   - `impact_3_aantal = '60+'`, `impact_3_omschrijving_nl = 'activiteiten per jaar'`, `impact_3_omschrijving_fr = 'activités par an'`
   - The exact NL/FR strings will be lifted verbatim from `lang/nl/pages.php` and `lang/fr/pages.php` at implementation time, so the user-visible page is unchanged on first deploy.
3. If `public/docs/jaarverslag-2025.pdf` exists, copy it into the singleton's media library (`addMedia(...)->preservingOriginal()->toMediaCollection('jaarverslag')`), then delete the loose file.
4. Use `firstOrCreate` and `getMedia('jaarverslag')->isEmpty()` guards so the migration is idempotent if rerun.

## Tests (PHPUnit feature tests)

`tests/Feature/OverOnsPageTest.php`:

- `test_impact_stats_render_from_database` — seed an `OverOnsContent` factory state; assert numbers and NL descriptions appear on `/over-ons`.
- `test_locale_specific_omschrijving_renders_in_nl_and_fr` — same record, hit `/over-ons` and `/fr/a-propos`, assert the right language strings.
- `test_jaarverslag_card_hidden_when_no_pdf_uploaded` — no media attached → the link block does not render; the rest of the page does.
- `test_jaarverslag_card_renders_year_label_and_links_to_uploaded_pdf` — attach a fake PDF to the media collection; assert the rendered anchor `href` is the media URL and the label includes `Jaarverslag 2025`.

`tests/Feature/Filament/ManageOverOnsContentTest.php` (using `livewire(\App\Filament\Pages\OverOnsContent::class)`):

- `test_admin_can_load_page_with_existing_record`
- `test_admin_can_update_impact_stats_and_jaarverslag_year` — fill form, call `save`, assert DB values.
- `test_admin_can_upload_pdf_to_jaarverslag_collection` — `Storage::fake`; upload via Filament; assert media exists.
- `test_uploading_a_new_pdf_replaces_the_previous_one` — verifies the `singleFile()` behavior.
- `test_validation_rejects_non_pdf_uploads`

A `OverOnsContentFactory` covers default values for tests.

## Open questions

None — all answered (Q1–Q5). Implementation can proceed.
