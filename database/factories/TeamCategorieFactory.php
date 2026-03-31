<?php

namespace Database\Factories;

use App\Models\TeamCategorie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamCategorie>
 */
class TeamCategorieFactory extends Factory
{
    public function definition(): array
    {
        return [
            'naam_nl' => $this->faker->words(2, true),
            'naam_fr' => $this->faker->words(2, true),
            'volgorde' => $this->faker->numberBetween(1, 100),
        ];
    }
}
