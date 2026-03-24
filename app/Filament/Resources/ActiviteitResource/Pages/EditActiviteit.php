<?php

namespace App\Filament\Resources\ActiviteitResource\Pages;

use App\Filament\Resources\ActiviteitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActiviteit extends EditRecord
{
    protected static string $resource = ActiviteitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
