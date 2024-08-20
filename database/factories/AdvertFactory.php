<?php

namespace Database\Factories;

use App\Models\Media\MediaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advert>
 */
class AdvertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'heading' => fake()->text(10),
            'description' => fake()->text(10),
            'link' => fake()->url(),
            'image_id' => MediaFile::factory()
        ];
    }
}
