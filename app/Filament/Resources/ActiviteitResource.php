<?php

namespace App\Filament\Resources;

use App\Enums\ActiviteitStatus;
use App\Filament\Resources\ActiviteitResource\Pages;
use App\Models\Activiteit;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
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

class ActiviteitResource extends Resource
{
    protected static ?string $model = Activiteit::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Activiteiten';

    protected static ?int $navigationSort = 1;

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

            Select::make('status')
                ->label('Status')
                ->options(ActiviteitStatus::class)
                ->default(ActiviteitStatus::Concept)
                ->required(),

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
                    ->badge(),
                Tables\Columns\TextColumn::make('template.titel_nl')
                    ->label('Reeks')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('datum', 'desc')
            ->defaultPaginationPageOption(25)
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(ActiviteitStatus::class),
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
