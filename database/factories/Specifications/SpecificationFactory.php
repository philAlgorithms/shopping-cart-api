<?php

namespace Database\Factories\Specifications;

use App\Models\Media\MediaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specifications\Specification>
 */
class SpecificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->unique()->word,
            'icon' => fake()->words(2, true)
        ];
    }
}
