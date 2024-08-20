<?php

namespace Database\Factories\Stores;

use App\Models\Media\MediaFile;
use App\Models\Products\Brand;
use App\Models\Users\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stores\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->company,
            'vendor_id' => Vendor::factory(),
            // 'brand_id' => Brand::factory(),
            'logo_id' => MediaFile::factory(),
            'description' => fake()->text(),
            'key' => fake()->unique()->name()
        ];
    }
}
