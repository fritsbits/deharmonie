<?php

namespace App\Filament\Resources\ActiviteitResource\Pages;

use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActiviteit extends CreateRecord
{
    protected static string $resource = ActiviteitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $soortFromQuery = request()->query('soort');
        $data['soort'] = in_array($soortFromQuery, ['vast', 'speciaal'], true)
            ? $soortFromQuery
            : Soort::Speciaal->value;

        return $data;
    }
}
