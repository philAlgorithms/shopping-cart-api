<?php

namespace Database\Factories\Products;

use App\Models\Media\MediaFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Products\ProductCategory>
 */
class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->words(2, true),
            'icon_id' => MediaFile::factory(),
            'cover_image_id' => MediaFile::factory()
        ];
    }
}
