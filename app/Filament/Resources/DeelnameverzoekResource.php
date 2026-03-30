<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeelnameverzoekResource\Pages;
use App\Models\Deelnameverzoek;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DeelnameverzoekResource extends Resource
{
    protected static ?string $model = Deelnameverzoek::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Inschrijvingen';

    protected static ?string $modelLabel = 'Inschrijving';

    protected static ?string $pluralModelLabel = 'Inschrijvingen';

    protected static ?string $slug = 'deelnameverzoeken';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Persoon')->schema([
                TextEntry::make('naam')->label('Naam'),
                TextEntry::make('email')->label('E-mail'),
                TextEntry::make('telefoon')->label('Telefoon')->placeholder('—'),
                TextEntry::make('bericht')->label('Bericht')->placeholder('—')->columnSpanFull(),
            ])->columns(2),
            Section::make('Activiteit')->schema([
                TextEntry::make('activiteit.titel_nl')->label('Activiteit'),
                TextEntry::make('activiteit.datum')
                    ->label('Datum')
                    ->date('d/m/Y'),
                TextEntry::make('activiteit.startuur')
                    ->label('Tijdstip')
                    ->formatStateUsing(fn (string $state): string => substr($state, 0, 5)),
                TextEntry::make('created_at')->label('Ontvangen')->dateTime('d/m/Y H:i'),
            ])->columns(2),
        ]);
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
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('activiteit')
                    ->relationship('activiteit', 'titel_nl'),
            ])
            ->actions([
                ViewAction::make(),
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
        return false;
    }

    public static function canDelete(mixed $record): bool
    {
        return false;
    }
}
