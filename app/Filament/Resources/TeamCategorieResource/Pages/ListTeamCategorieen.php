<?php

namespace App\Filament\Resources\TeamCategorieResource\Pages;

use App\Filament\Resources\TeamCategorieResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeamCategorieen extends ListRecords
{
    protected static string $resource = TeamCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
