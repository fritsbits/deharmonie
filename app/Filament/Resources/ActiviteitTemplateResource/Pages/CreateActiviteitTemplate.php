<?php

namespace App\Filament\Resources\ActiviteitTemplateResource\Pages;

use App\Filament\Resources\ActiviteitTemplateResource;
use App\Services\ActiviteitTemplateService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateActiviteitTemplate extends CreateRecord
{
    protected static string $resource = ActiviteitTemplateResource::class;

    protected function afterCreate(): void
    {
        $service = new ActiviteitTemplateService;
        $count = $service->generateSessions($this->record);

        if ($count === 0) {
            Notification::make()
                ->title('Geen sessies aangemaakt')
                ->body('Er zijn geen datums gevonden die overeenkomen met de gekozen dag en periode.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title("{$count} sessies aangemaakt voor {$this->record->titel_nl}")
            ->success()
            ->send();
    }
}
