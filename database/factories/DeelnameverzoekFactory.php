<?php

namespace Database\Factories;

use App\Models\Activiteit;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeelnameverzoekFactory extends Factory
{
    public function definition(): array
    {
        return [
            'activiteit_id' => Activiteit::factory(),
            'naam' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'telefoon' => $this->faker->phoneNumber(),
            'bericht' => $this->faker->sentence(),
            'status' => 'te_contacteren',
        ];
    }
}
