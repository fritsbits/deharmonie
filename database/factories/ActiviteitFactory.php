<?php

namespace Database\Factories;

use App\Enums\ActiviteitStatus;
use App\Enums\Categorie;
use App\Enums\Soort;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActiviteitFactory extends Factory
{
    public function definition(): array
    {
        $titleNl = $this->faker->sentence(3);

        return [
            'slug' => Str::slug($titleNl).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'titel_nl' => $titleNl,
            'titel_fr' => $this->faker->sentence(3),
            'beschrijving_nl' => $this->faker->paragraph(),
            'beschrijving_fr' => $this->faker->paragraph(),
            'notice_nl' => null,
            'notice_fr' => null,
            'datum' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'startuur' => '10:00:00',
            'einduur' => '12:00:00',
            'locatie_nl' => 'De Harmonie',
            'locatie_fr' => 'De Harmonie',
            'prijs' => null,
            'max_deelnemers' => null,
            'status' => ActiviteitStatus::Gepubliceerd,
            'soort' => Soort::Vast,
            'categorie' => $this->faker->randomElement(Categorie::cases()),
        ];
    }

    public function vast(): static
    {
        return $this->state(['soort' => Soort::Vast]);
    }

    public function speciaal(): static
    {
        return $this->state(['soort' => Soort::Speciaal]);
    }
}
