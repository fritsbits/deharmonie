<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActiviteitResource\Pages;
use App\Models\Activiteit;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Set $set, string $operation) =>
                            $operation === 'create'
                                ? $set('slug', Str::slug($state))
                                : null
                        ),
                    RichEditor::make('beschrijving_nl')
                        ->label('Beschrijving (NL)'),
                    Textarea::make('notice_nl')
                        ->label('Opmerking / Annuleringsmelding (NL)'),
                ]),
                Tab::make('Français')->schema([
                    TextInput::make('titel_fr')
                        ->label('Titre (FR)')
                        ->required()
                        ->maxLength(255),
                    RichEditor::make('beschrijving_fr')
                        ->label('Description (FR)'),
                    Textarea::make('notice_fr')
                        ->label('Remarque / Message d\'annulation (FR)'),
                ]),
            ])->columnSpanFull(),

            TextInput::make('slug')
                ->label('Slug')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

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
                ->options([
                    'concept' => 'Concept',
                    'gepubliceerd' => 'Gepubliceerd',
                    'geannuleerd' => 'Geannuleerd',
                ])
                ->default('concept')
                ->required(),

            SpatieMediaLibraryFileUpload::make('afbeelding')
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
                    ->counts('deelnameverzoeken'),
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
