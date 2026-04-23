<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamLidResource\Pages;
use App\Models\TeamCategorie;
use App\Models\TeamLid;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TeamLidResource extends Resource
{
    protected static ?string $model = TeamLid::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Wie is wie';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Teamlid';

    protected static ?string $pluralModelLabel = 'Teamleden';

    protected static ?string $slug = 'teamleden';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('naam')
                ->label('Naam')
                ->required()
                ->columnSpanFull(),

            TextInput::make('titel_nl')
                ->label('Titel (NL)'),

            TextInput::make('titel_fr')
                ->label('Titre (FR)'),

            Select::make('team_categorie_id')
                ->label('Categorie')
                ->options(
                    TeamCategorie::orderBy('volgorde')->pluck('naam_nl', 'id')
                )
                ->required()
                ->columnSpanFull(),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('naam')
                    ->label('Naam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('titel_nl')
                    ->label('Titel (NL)')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('categorie.naam_nl')
                    ->label('Categorie')
                    ->sortable(),
            ])
            ->defaultSort('naam')
            ->filters([
                Tables\Filters\SelectFilter::make('team_categorie_id')
                    ->label('Categorie')
                    ->options(
                        TeamCategorie::orderBy('volgorde')->pluck('naam_nl', 'id')
                    ),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamLeden::route('/'),
            'create' => Pages\CreateTeamLid::route('/create'),
            'edit' => Pages\EditTeamLid::route('/{record}/edit'),
        ];
    }
}
