<?php

namespace App\Filament\Resources;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource\Pages;
use App\Models\Activiteit;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ActiviteitResource extends Resource
{
    protected static ?string $model = Activiteit::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Activiteiten';

    protected static ?string $modelLabel = 'Activiteit';

    protected static ?string $pluralModelLabel = 'Activiteiten';

    protected static ?string $slug = 'activiteiten';

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
                        ->label('Opmerking / Annuleringsmelding (NL)'),
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
                        ->label('Remarque / Message d\'annulation (FR)'),
                ]),
            ])->columnSpanFull(),

            DatePicker::make('datum')
                ->label('Datum')
                ->required(),

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

            Select::make('categorie')
                ->label('Categorie')
                ->options(collect(Categorie::cases())->mapWithKeys(fn ($c) => [$c->value => $c->getLabel()])->all())
                ->required(),

            Select::make('status')
                ->label('Status')
                ->options(ActiviteitStatus::class)
                ->default(ActiviteitStatus::Concept)
                ->required(),

            Hidden::make('soort_query')
                ->default(fn (): string => request()->query('soort', '')),

            Toggle::make('herhaal_wekelijks')
                ->label('Plan automatisch in: elke week tot...')
                ->live()
                ->dehydrated(false)
                ->visible(fn (Get $get, string $operation): bool => $operation === 'create' && $get('soort_query') === 'vast'),

            DatePicker::make('herhaal_t_m')
                ->label('Tot en met')
                ->dehydrated(false)
                ->required(fn (Get $get): bool => (bool) $get('herhaal_wekelijks'))
                ->visible(fn (Get $get, string $operation): bool => $operation === 'create' && (bool) $get('herhaal_wekelijks')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup(
                Group::make('week_start')
                    ->titlePrefixedWithLabel(false)
                    ->getKeyFromRecordUsing(fn (Activiteit $a) => $a->datum->copy()->startOfWeek()->toDateString())
                    ->getTitleFromRecordUsing(function (Activiteit $a): string {
                        $start = $a->datum->copy()->startOfWeek()->locale('nl');
                        $end = $a->datum->copy()->endOfWeek()->locale('nl');

                        return $start->isoFormat('D MMMM').' – '.$end->isoFormat('D MMMM');
                    })
                    ->orderQueryUsing(fn ($query, $direction) => $query->orderBy('datum', $direction))
                    ->scopeQueryByKeyUsing(fn ($query, $key) => $query->whereBetween('datum', [
                        $key,
                        Carbon::parse($key)->endOfWeek()->toDateString(),
                    ]))
                    ->collapsible()
            )
            ->defaultSort('datum', 'asc')
            ->defaultPaginationPageOption(50)
            ->columns([
                TextColumn::make('datum')
                    ->label('Dag')
                    ->formatStateUsing(fn (Carbon $state) => strtoupper($state->locale('nl')->isoFormat('ddd D/MM')))
                    ->sortable()
                    ->width('110px'),
                ViewColumn::make('rich')
                    ->label('Activiteit')
                    ->view('filament.tables.columns.activiteit-rich-cell'),
                TextColumn::make('startuur')
                    ->label('Tijd')
                    ->formatStateUsing(fn (?string $state) => $state ? substr($state, 0, 5) : '—')
                    ->width('80px'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->width('110px'),
            ])
            ->filters([
                SelectFilter::make('categorie')
                    ->label('Categorie')
                    ->options(collect(Categorie::cases())->mapWithKeys(fn ($c) => [$c->value => $c->getLabel()])->all()),
                SelectFilter::make('soort')
                    ->options(collect(Soort::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])->all()),
                SelectFilter::make('status')
                    ->options(collect(ActiviteitStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->getLabel()])->all()),
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('kopieer')
                        ->label('Kopieer naar...')
                        ->icon('heroicon-o-document-duplicate')
                        ->modalHeading(fn (Activiteit $record) => 'Kopieer "'.$record->titel_nl.'"')
                        ->form([
                            Radio::make('mode')
                                ->label('Naar welke datums?')
                                ->options([
                                    'wekelijks' => 'Wekelijks (elke week tot een einddatum)',
                                    'specifiek' => 'Specifieke datums',
                                ])
                                ->default('wekelijks')
                                ->required()
                                ->live(),
                            DatePicker::make('start')
                                ->label('Vanaf')
                                ->required()
                                ->visible(fn (Get $get): bool => $get('mode') === 'wekelijks'),
                            DatePicker::make('einde')
                                ->label('Tot en met')
                                ->required()
                                ->visible(fn (Get $get): bool => $get('mode') === 'wekelijks')
                                ->after('start'),
                            Repeater::make('datums')
                                ->label('Datums')
                                ->schema([
                                    DatePicker::make('datum'),
                                ])
                                ->minItems(1)
                                ->visible(fn (Get $get): bool => $get('mode') === 'specifiek'),
                        ])
                        ->action(function (Activiteit $record, array $data): void {
                            $datums = $data['mode'] === 'wekelijks'
                                ? self::buildWeeklyDates($record->datum, $data['start'], $data['einde'])
                                : array_values(array_map(
                                    fn ($d) => Carbon::parse($d['datum']),
                                    array_filter($data['datums'] ?? [], fn ($d) => filled($d['datum'] ?? null)),
                                ));

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
                                    'status' => ActiviteitStatus::Concept,
                                    'soort' => $record->soort,
                                    'categorie' => $record->categorie,
                                ]);
                            }
                        }),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publiceer geselecteerde')
                        ->action(fn ($records) => $records->each->update(['status' => ActiviteitStatus::Gepubliceerd]))
                        ->icon('heroicon-o-check'),
                    BulkAction::make('cancel')
                        ->label('Annuleer geselecteerde')
                        ->action(fn ($records) => $records->each->update(['status' => ActiviteitStatus::Geannuleerd]))
                        ->icon('heroicon-o-x-mark')
                        ->color('danger'),
                    BulkAction::make('bulk_edit')
                        ->label('Bewerk gemeenschappelijke velden')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Textarea::make('beschrijving_nl')->label('Beschrijving (NL) — leeg laten = niet wijzigen'),
                            Textarea::make('beschrijving_fr')->label('Beschrijving (FR) — leeg laten = niet wijzigen'),
                            TextInput::make('locatie')->label('Locatie — leeg laten = niet wijzigen'),
                            TextInput::make('prijs')->numeric()->label('Prijs — leeg laten = niet wijzigen'),
                        ])
                        ->action(function ($records, array $data): void {
                            $update = array_filter($data, fn ($v) => $v !== null && $v !== '');
                            if (empty($update)) {
                                return;
                            }
                            $records->each(fn ($r) => $r->update($update));
                        }),
                    DeleteBulkAction::make(),
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

    /**
     * Returns dates on the same weekday as $anchor between $start and $einde inclusive.
     *
     * @return array<Carbon>
     */
    private static function buildWeeklyDates(Carbon $anchor, string $start, string $einde): array
    {
        $weekday = $anchor->dayOfWeek;
        $cursor = Carbon::parse($start);
        if ($cursor->dayOfWeek !== $weekday) {
            $cursor = $cursor->next($weekday);
        }
        $end = Carbon::parse($einde);
        $out = [];
        while ($cursor->lte($end)) {
            $out[] = $cursor->copy();
            $cursor->addWeek();
        }

        return $out;
    }
}
