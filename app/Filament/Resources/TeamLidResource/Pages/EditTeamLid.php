<?php

namespace App\Filament\Resources\TeamLidResource\Pages;

use App\Filament\Resources\TeamLidResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamLid extends EditRecord
{
    protected static string $resource = TeamLidResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
