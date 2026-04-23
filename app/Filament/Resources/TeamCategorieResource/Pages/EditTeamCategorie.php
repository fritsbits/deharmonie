<?php

namespace App\Filament\Resources\TeamCategorieResource\Pages;

use App\Filament\Resources\TeamCategorieResource;
use App\Models\TeamCategorie;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditTeamCategorie extends EditRecord
{
    protected static string $resource = TeamCategorieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action, TeamCategorie $record) {
                    if ($record->leden()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Kan niet verwijderen')
                            ->body('Deze categorie heeft '.$record->leden()->count().' teamleden. Verplaats ze eerst naar een andere categorie.')
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
