<?php

namespace Tests\Feature;

use App\Models\Products\{Product};
use App\Models\Ratings\{Rating};
use App\Models\Users\{Admin, Buyer};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductRatingControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * @test
     */
    public function it_Lists_all_product_ratings_in_paginated_format()
    {
        $product = Product::factory()->create();
        $ratings = Rating::factory(10)->create([
            'rateable_id' => $product->id,
            'rateable_type' => Product::class
        ]);
        $product2 = Product::factory()->create();
        $ratings2 = Rating::factory(10)->create([
            'rateable_id' => $product2->id,
            'rateable_type' => Product::class
        ]);

        $response = $this->getJson("/products/{$product->id}/ratings?".http_build_query([
            // 
        ]));
        
        $response->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'comment', 'title', 'rater']]]);
    }

    /**
     * @test
     */
    public function it_rates_a_product()
    {
        $product = Product::factory()->create();
        $buyer = Buyer::factory()->create();
        $title = fake()->words(3, true);
        $comment = fake()->text(40);

        $this->actingAs($buyer, 'buyer');

        $response = $this->postJson("/products/{$product->id}/ratings", [
            'title' => $title,
            'comment' => $comment
        ]);

        $response->dump();

        $response->assertCreated();
    }

    /**
     * @test
     */
    public function it_shows_a_product_rating()
    {
        $product = Product::factory()->create();
        $rating = Rating::factory()->create([
            'rateable_id' => $product->id,
            'rateable_type' => Product::class
        ]);

        $response = $this->getJson("/products/{$product->id}/ratings/{$rating->id}");
        $response->dump();
        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'rater', 'rateable', 'title', 'comment']]);
    }

    /**
     * @test
     */
    public function it_deletes_a_product_rating()
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $rating = Rating::factory()->create([
            'rateable_id' => $product->id,
            'rateable_type' => Product::class
        ]);

        $this->actingAs($admin, 'admin');
        // $this->actingAs($rating->rater, 'buyer');
        $response = $this->deleteJson(
            "/products/{$product->id}/ratings/{$rating->id}"
        );

        $response->assertOk();
        $this->assertSoftDeleted('ratings', ['id' => $rating->id]);
    }

    /**
     * @test
     */
    public function it_force_deletes_a_product_rating()
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();
        $rating = Rating::factory()->create([
            'rateable_id' => $product->id,
            'rateable_type' => Product::class
        ]);

        $this->actingAs($admin, 'admin');
        // $this->actingAs($rating->rater, 'buyer');
        $response = $this->deleteJson(
            "/products/{$product->id}/ratings/force-delete/{$rating->id}"
        );

        $response->assertOk();
        $this->assertNull(Rating::find($rating->id));
    }
}
