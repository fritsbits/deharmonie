<?php

namespace Database\Factories;

use App\Models\OverOnsContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OverOnsContent>
 */
class OverOnsContentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'jaarverslag_jaar' => 2025,
            'impact_1_aantal' => '250',
            'impact_1_omschrijving_nl' => 'wekelijks bij ons over de vloer',
            'impact_1_omschrijving_fr' => 'chaque semaine chez nous',
            'impact_2_aantal' => '4500',
            'impact_2_omschrijving_nl' => 'maaltijden per maand',
            'impact_2_omschrijving_fr' => 'repas par mois',
            'impact_3_aantal' => '60+',
            'impact_3_omschrijving_nl' => 'activiteiten per jaar',
            'impact_3_omschrijving_fr' => 'activités par an',
        ];
    }
}
