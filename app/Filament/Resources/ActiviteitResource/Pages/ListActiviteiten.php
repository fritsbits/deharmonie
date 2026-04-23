<?php

namespace App\Filament\Resources\ActiviteitResource\Pages;

use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListActiviteiten extends ListRecords
{
    protected static string $resource = ActiviteitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createVast')
                ->label('+ Vaste activiteit')
                ->color('primary')
                ->url(fn (): string => ActiviteitResource::getUrl('create', ['soort' => Soort::Vast->value])),
            Action::make('createSpeciaal')
                ->label('+ Speciaal moment')
                ->color('gray')
                ->url(fn (): string => ActiviteitResource::getUrl('create', ['soort' => Soort::Speciaal->value])),
        ];
    }
}
