<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeelnameverzoekResource\Pages;
use App\Models\Deelnameverzoek;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

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
        return $schema->components([]);  // view-only, no editing via form
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
                    }),
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
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (Deelnameverzoek $record) => $record->status === 'te_contacteren' ? 'Afgehandeld' : 'Heropenen')
                    ->action(function (Deelnameverzoek $record): void {
                        $record->update([
                            'status' => $record->status === 'te_contacteren' ? 'afgehandeld' : 'te_contacteren',
                        ]);
                    })
                    ->icon('heroicon-o-check-circle'),
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
