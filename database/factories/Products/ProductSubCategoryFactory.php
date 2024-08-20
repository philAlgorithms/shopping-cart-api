<?php

namespace Database\Factories\Products;

use App\Models\Media\MediaFile;
use App\Models\Products\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Products\ProductSubCategory>
 */
class ProductSubCategoryFactory extends Factory
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
            'product_category_id' => ProductCategory::factory(),
            'cover_image_id' => MediaFile::factory()
        ];
    }
}
