<?php

namespace App\Filament\Resources\ActiviteitResource\Pages;

use App\Enums\ActiviteitStatus;
use App\Enums\Soort;
use App\Filament\Resources\ActiviteitResource;
use App\Models\Activiteit;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateActiviteit extends CreateRecord
{
    protected static string $resource = ActiviteitResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // soort_query is a UI-only Hidden field that carries ?soort= through Livewire state.
        $soortFromForm = $data['soort_query'] ?? null;
        $soortFromQuery = request()->query('soort');
        $soort = $soortFromForm ?: $soortFromQuery;

        $data['soort'] = in_array($soort, ['vast', 'speciaal'], true)
            ? $soort
            : Soort::Speciaal->value;

        unset($data['soort_query']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $form = $this->form->getRawState();

        if (empty($form['herhaal_wekelijks']) || empty($form['herhaal_t_m'])) {
            return;
        }

        /** @var Activiteit $created */
        $created = $this->record;
        $startDate = Carbon::parse($created->datum);
        $endDate = Carbon::parse($form['herhaal_t_m']);

        $cursor = $startDate->copy()->addWeek();
        while ($cursor->lte($endDate)) {
            Activiteit::create([
                'titel_nl' => $created->titel_nl,
                'titel_fr' => $created->titel_fr,
                'beschrijving_nl' => $created->beschrijving_nl,
                'beschrijving_fr' => $created->beschrijving_fr,
                'notice_nl' => null,
                'notice_fr' => null,
                'datum' => $cursor->toDateString(),
                'startuur' => $created->startuur,
                'einduur' => $created->einduur,
                'locatie_nl' => $created->locatie_nl,
                'locatie_fr' => $created->locatie_fr,
                'prijs' => $created->prijs,
                'max_deelnemers' => $created->max_deelnemers,
                'status' => ActiviteitStatus::Concept,
                'soort' => Soort::Vast,
                'categorie' => $created->categorie,
            ]);
            $cursor->addWeek();
        }
    }
}
