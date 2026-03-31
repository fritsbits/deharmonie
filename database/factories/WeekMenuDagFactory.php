<?php

namespace Database\Factories;

use App\Models\WeekMenuDag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WeekMenuDag>
 */
class WeekMenuDagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'date' => $this->faker->unique()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'closed' => false,
            'special_event' => false,
            'price' => 9,
            'main_nl' => $this->faker->sentence(3),
            'main_fr' => $this->faker->sentence(3),
            'event_label_nl' => null,
            'event_label_fr' => null,
            'courses' => null,
        ];
    }

    public function closed(): static
    {
        return $this->state([
            'closed' => true,
            'special_event' => false,
            'price' => null,
            'main_nl' => null,
            'main_fr' => null,
        ]);
    }

    public function specialEvent(): static
    {
        return $this->state([
            'closed' => false,
            'special_event' => true,
            'price' => 20,
            'main_nl' => null,
            'main_fr' => null,
            'event_label_nl' => $this->faker->words(2, true),
            'event_label_fr' => $this->faker->words(2, true),
            'courses' => [
                ['nl' => $this->faker->words(2, true), 'fr' => $this->faker->words(2, true)],
                ['nl' => $this->faker->words(2, true), 'fr' => $this->faker->words(2, true)],
            ],
        ]);
    }
}
