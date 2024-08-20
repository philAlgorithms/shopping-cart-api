<?php

namespace Database\Factories\Location;

use App\Models\Location\Town;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Location\ResidentialArea>
 */
class ResidentialAreaFactory extends Factory
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
            'town_id' => Town::factory()
        ];
    }
}
