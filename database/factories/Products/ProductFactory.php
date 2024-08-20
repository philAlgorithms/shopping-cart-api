<?php

namespace Database\Factories\Products;

use App\Models\Media\MediaFile;
use App\Models\Products\Brand;
use App\Models\Products\ProductSubCategory;
use App\Models\Stores\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Products\Product>
 */
class ProductFactory extends Factory
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
            'price' => fake()->numberBetween(10000, 500000),
            'quantity' => fake()->numberBetween(0, 100),
            'product_sub_category_id' => ProductSubCategory::factory(),
            'cover_image_id' => MediaFile::factory(),
            'store_id' => Store::factory(),
            'brand_id' => Brand::factory()
        ];
    }
}
