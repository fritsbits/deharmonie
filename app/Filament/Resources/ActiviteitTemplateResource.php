<?php

namespace App\Filament\Resources;

use App\Enums\DagVanDeWeek;
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
                ->options(DagVanDeWeek::class)
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
                    ->formatStateUsing(fn (int $state): string => DagVanDeWeek::from($state)->getLabel()),
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
            'index' => Pages\ListActiviteitTemplates::route('/'),
            'create' => Pages\CreateActiviteitTemplate::route('/create'),
            'edit' => Pages\EditActiviteitTemplate::route('/{record}/edit'),
        ];
    }
}
