<?php

namespace Database\Factories\Specifications;

use App\Models\Products\Product;
use App\Models\Specifications\Specification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specifications\ProductSpecification>
 */
class ProductSpecificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'detail' => fake()->text(100),
            'product_id' => Product::factory(),
            'specification_id' => Specification::factory()
        ];
    }
}
