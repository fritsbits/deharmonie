<?php

namespace App\Filament\Resources\TeamLidResource\Pages;

use App\Filament\Resources\TeamLidResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamLeden extends ListRecords
{
    protected static string $resource = TeamLidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
