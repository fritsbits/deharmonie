<?php

namespace App\Filament\Resources\TeamCategorieResource\Pages;

use App\Filament\Resources\TeamCategorieResource;
use App\Models\TeamCategorie;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamCategorie extends CreateRecord
{
    protected static string $resource = TeamCategorieResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['volgorde'] = (int) TeamCategorie::max('volgorde') + 1;

        return $data;
    }
}
