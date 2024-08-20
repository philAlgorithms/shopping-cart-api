<?php

namespace Database\Factories\Location;

use App\Models\Location\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location\Town>
 */
class TownFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->city(),
            'state_id' => State::factory()
        ];
    }
}
