<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeekMenuDagResource\Pages;
use App\Models\WeekMenuDag;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
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

    protected static ?string $navigationLabel = 'Restaurant & Menu';

    protected static \UnitEnum|string|null $navigationGroup = 'Inhoud';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Menudag';

    protected static ?string $pluralModelLabel = 'Menudagen';

    protected static ?string $slug = 'weekmenu';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // What day — full width
            DatePicker::make('date')
                ->label('Datum')
                ->required()
                ->unique(ignoreRecord: true)
                ->columnSpanFull(),

            // What kind of day — toggles side by side
            Toggle::make('closed')
                ->label('Gesloten')
                ->live()
                ->default(false),

            Toggle::make('special_event')
                ->label('Speciaal menu')
                ->live()
                ->default(false)
                ->hidden(fn (Get $get): bool => (bool) $get('closed')),

            // Normal day — NL/FR pair side by side
            TextInput::make('main_nl')
                ->label('Gerecht (NL)')
                ->required()
                ->hidden(fn (Get $get): bool => (bool) $get('closed') || (bool) $get('special_event')),

            TextInput::make('main_fr')
                ->label('Plat (FR)')
                ->required()
                ->hidden(fn (Get $get): bool => (bool) $get('closed') || (bool) $get('special_event')),

            // Special event — NL/FR pair side by side
            TextInput::make('event_label_nl')
                ->label('Naam speciaal menu (NL)')
                ->required()
                ->hidden(fn (Get $get): bool => ! (bool) $get('special_event')),

            TextInput::make('event_label_fr')
                ->label('Nom menu spécial (FR)')
                ->required()
                ->hidden(fn (Get $get): bool => ! (bool) $get('special_event')),

            // Price — below content, half width
            TextInput::make('price')
                ->label('Prijs (€)')
                ->numeric()
                ->required()
                ->prefix('€')
                ->hidden(fn (Get $get): bool => (bool) $get('closed')),

            // Courses — full width at the bottom
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
                Tables\Columns\TextColumn::make('main_nl')
                    ->label('Gerecht (NL)')
                    ->limit(50)
                    ->placeholder('—'),
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
            ])
            ->defaultSort('date', 'desc')
            ->defaultPaginationPageOption(25)
            ->filters([
                Tables\Filters\Filter::make('week')
                    ->form([
                        Select::make('week_range')
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
