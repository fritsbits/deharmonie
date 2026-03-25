<?php

namespace App\Filament\Resources\ActiviteitTemplateResource\Pages;

use App\Filament\Resources\ActiviteitTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActiviteitTemplates extends ListRecords
{
    protected static string $resource = ActiviteitTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
