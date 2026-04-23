<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamCategorieResource\Pages;
use App\Models\TeamCategorie;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TeamCategorieResource extends Resource
{
    protected static ?string $model = TeamCategorie::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Teamcategorieën';

    protected static ?string $modelLabel = 'Teamcategorie';

    protected static ?string $pluralModelLabel = 'Teamcategorieën';

    protected static ?string $slug = 'teamcategorieen';

    protected static \UnitEnum|string|null $navigationGroup = 'Instellingen';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('naam_nl')
                ->label('Naam (NL)')
                ->required()
                ->maxLength(255),

            TextInput::make('naam_fr')
                ->label('Nom (FR)')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('naam_nl')
                    ->label('Naam (NL)')
                    ->searchable(),
                TextColumn::make('naam_fr')
                    ->label('Nom (FR)')
                    ->searchable(),
                TextColumn::make('leden_count')
                    ->label('Teamleden')
                    ->counts('leden')
                    ->badge(),
            ])
            ->defaultSort('volgorde')
            ->reorderable('volgorde')
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, TeamCategorie $record) {
                        if ($record->leden()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('Kan niet verwijderen')
                                ->body('Deze categorie heeft '.$record->leden()->count().' teamleden. Verplaats ze eerst naar een andere categorie.')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, Collection $records) {
                            $blocked = $records->filter(fn (TeamCategorie $record) => $record->leden()->exists());

                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->warning()
                                    ->title($blocked->count().' van '.$records->count().' categorieën overgeslagen')
                                    ->body('Categorieën met teamleden zijn niet verwijderd: '.$blocked->pluck('naam_nl')->implode(', '))
                                    ->send();
                            }

                            $deletable = $records->reject(fn (TeamCategorie $record) => $record->leden()->exists());
                            $deletable->each->delete();

                            $action->cancel();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeamCategorieen::route('/'),
            'create' => Pages\CreateTeamCategorie::route('/create'),
            'edit' => Pages\EditTeamCategorie::route('/{record}/edit'),
        ];
    }
}
