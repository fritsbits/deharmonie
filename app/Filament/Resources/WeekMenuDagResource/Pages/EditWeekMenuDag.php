<?php

namespace App\Filament\Resources\WeekMenuDagResource\Pages;

use App\Filament\Resources\WeekMenuDagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeekMenuDag extends EditRecord
{
    protected static string $resource = WeekMenuDagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
