<?php

namespace App\Filament\Resources\DeelnameverzoekResource\Pages;

use App\Filament\Resources\DeelnameverzoekResource;
use Filament\Resources\Pages\ListRecords;

class ListDeelnameverzoeken extends ListRecords
{
    protected static string $resource = DeelnameverzoekResource::class;

    protected function getHeaderActions(): array
    {
        return [];  // no create action — registrations come from public form only
    }
}
