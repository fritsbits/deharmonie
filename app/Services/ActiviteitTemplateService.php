<?php

namespace App\Services;

use App\Enums\ActiviteitStatus;
use App\Models\Activiteit;
use App\Models\ActiviteitTemplate;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;

class ActiviteitTemplateService
{
    public function generateSessions(ActiviteitTemplate $template): int
    {
        $count = 0;
        $period = CarbonPeriod::create($template->reeks_start, $template->reeks_einde);

        foreach ($period as $date) {
            if ($date->dayOfWeek !== $template->dag_van_de_week) {
                continue;
            }

            $dateString = $date->format('Y-m-d');

            $exists = Activiteit::where('template_id', $template->id)
                ->whereDate('datum', $dateString)
                ->exists();

            if ($exists) {
                continue;
            }

            Activiteit::create([
                'template_id' => $template->id,
                'titel_nl' => $template->titel_nl,
                'titel_fr' => $template->titel_fr,
                'beschrijving_nl' => $template->beschrijving_nl,
                'beschrijving_fr' => $template->beschrijving_fr,
                'notice_nl' => $template->notice_nl,
                'notice_fr' => $template->notice_fr,
                'datum' => $dateString,
                'startuur' => $template->startuur,
                'einduur' => $template->einduur,
                'locatie' => $template->locatie,
                'prijs' => $template->prijs,
                'max_deelnemers' => $template->max_deelnemers,
                'interesse' => $template->interesse?->value,
                'status' => ActiviteitStatus::Concept,
                'slug' => $this->uniqueSlug($template->titel_nl, $dateString),
            ]);

            $count++;
        }

        return $count;
    }

    public function propagateToFutureSessions(ActiviteitTemplate $template): int
    {
        $updated = 0;
        $sessions = Activiteit::where('template_id', $template->id)
            ->where('datum', '>=', today())
            ->where('status', '!=', ActiviteitStatus::Geannuleerd->value)
            ->get();

        foreach ($sessions as $session) {
            $activeRegistrations = $session->deelnameverzoeken()
                ->whereIn('status', ['te_contacteren', 'afgehandeld'])
                ->count();

            // Skip only if the new max_deelnemers would cause overbooking; sessions with
            // registrations but remaining capacity are still eligible for propagation
            if ($template->max_deelnemers !== null && $activeRegistrations >= $template->max_deelnemers) {
                continue;
            }

            // prijs is intentionally excluded: price is set at generation time and never propagated
            $data = [
                'titel_nl' => $template->titel_nl,
                'titel_fr' => $template->titel_fr,
                'beschrijving_nl' => $template->beschrijving_nl,
                'beschrijving_fr' => $template->beschrijving_fr,
                'notice_nl' => $template->notice_nl,
                'notice_fr' => $template->notice_fr,
                'startuur' => $template->startuur,
                'einduur' => $template->einduur,
                'locatie' => $template->locatie,
                'interesse' => $template->interesse?->value,
            ];

            if ($template->max_deelnemers !== null) {
                $data['max_deelnemers'] = $template->max_deelnemers;
            }

            $session->update($data);
            $updated++;
        }

        return $updated;
    }

    private function uniqueSlug(string $title, string $date): string
    {
        $base = Str::slug($title).'-'.$date;
        $slug = $base;
        $i = 2;

        while (Activiteit::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
