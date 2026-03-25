<?php

namespace App\Filament\Resources\ActiviteitTemplateResource\Pages;

use App\Filament\Resources\ActiviteitTemplateResource;
use App\Services\ActiviteitTemplateService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditActiviteitTemplate extends EditRecord
{
    protected static string $resource = ActiviteitTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),

            Actions\Action::make('saveAndPropagate')
                ->label('Opslaan en toepassen op toekomstige sessies')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Toepassen op toekomstige sessies')
                ->modalDescription('Wijzigingen worden toegepast op alle toekomstige sessies die nog geen inschrijvingen hebben en niet geannuleerd zijn.')
                ->modalSubmitActionLabel('Ja, toepassen')
                ->action(function (): void {
                    $this->save();

                    $service = new ActiviteitTemplateService;
                    $count = $service->propagateToFutureSessions($this->record);

                    Notification::make()
                        ->title("{$count} toekomstige sessies bijgewerkt")
                        ->success()
                        ->send();
                }),

            $this->getCancelFormAction(),
        ];
    }

    protected function afterSave(): void
    {
        if (! $this->record->wasChanged(['reeks_start', 'reeks_einde'])) {
            return;
        }

        $service = new ActiviteitTemplateService;
        $count = $service->generateSessions($this->record);

        if ($count > 0) {
            Notification::make()
                ->title("{$count} nieuwe sessies aangemaakt")
                ->success()
                ->send();
        }
    }
}
