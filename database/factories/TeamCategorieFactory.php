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
            'naam_nl' => fake()->words(2, true),
            'naam_fr' => fake()->words(2, true),
            'volgorde' => fake()->numberBetween(1, 100),
        ];
    }
}
