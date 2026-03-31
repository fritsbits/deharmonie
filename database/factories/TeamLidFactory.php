<?php

namespace Database\Factories;

use App\Models\TeamCategorie;
use App\Models\TeamLid;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamLid>
 */
class TeamLidFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_categorie_id' => TeamCategorie::factory(),
            'naam' => $this->faker->name(),
            'titel_nl' => null,
            'titel_fr' => null,
            'volgorde' => $this->faker->numberBetween(1, 100),
        ];
    }
}
