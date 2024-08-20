<?php

namespace Database\Factories\Ratings;

use App\Models\Products\Brand;
use App\Models\Products\Product;
use App\Models\Ratings\Rating;
use App\Models\Stores\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ratings\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rating::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $rateable = $this->rateable();

        return [
            'comment_title' => fake()->words(3, true),
            'comment' => fake()->sentence(25),
            'rateable_type' => $rateable,
            'rateable_id' => $rateable::factory(),
            'rater_id' => User::factory()
        ];
    }

    public function rateable()
    {
        return $this->faker->randomElement([
            Product::class,
            Brand::class,
            Store::class
        ]);
    }
}
