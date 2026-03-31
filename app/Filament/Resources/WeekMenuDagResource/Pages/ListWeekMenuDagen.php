<?php

namespace App\Filament\Resources\WeekMenuDagResource\Pages;

use App\Filament\Resources\WeekMenuDagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWeekMenuDagen extends ListRecords
{
    protected static string $resource = WeekMenuDagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
