<?php

namespace Database\Factories;

use App\Enums\Interesse;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActiviteitTemplateFactory extends Factory
{
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+1 month');
        $end = $this->faker->dateTimeBetween('+2 months', '+4 months');

        return [
            'titel_nl' => $this->faker->sentence(3),
            'titel_fr' => $this->faker->sentence(3),
            'beschrijving_nl' => $this->faker->paragraph(),
            'beschrijving_fr' => $this->faker->paragraph(),
            'notice_nl' => null,
            'notice_fr' => null,
            'startuur' => '10:00:00',
            'einduur' => '12:00:00',
            'locatie' => 'De Harmonie',
            'prijs' => null,
            'max_deelnemers' => null,
            'interesse' => Interesse::Activiteiten,
            'dag_van_de_week' => $this->faker->numberBetween(0, 6),
            'reeks_start' => $start->format('Y-m-d'),
            'reeks_einde' => $end->format('Y-m-d'),
        ];
    }

    public function monday(): static
    {
        return $this->state(['dag_van_de_week' => 1]);
    }
}
