<?php

namespace Tests\Feature;

use App\Models\Products\{Product};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_adds_to_cart()
    {
        $product = Product::factory()->create([
            'quantity' => 10
        ]);

        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 3
        ]);

        $response->assertJsonCount(1, 'data');
        $this->assertEquals(3, $response['data'][0]['quantity']);
    }

    /**
     * @test
     */
    public function it_removes_from_cart()
    {
        $product = Product::factory()->create([
            'quantity' => 10
        ]);

        $response = $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 7
        ]);

        $response = $this->postJson('/cart/remove', [
            'product_id' => $product->id,
            'quantity' => 3
        ]);
        $response2 = $this->postJson('/cart/remove', [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response->assertJsonCount(1, 'data');
        $this->assertEquals(4, $response['data'][0]['quantity']);

        $response2->assertJsonCount(1, 'data');
        $this->assertEquals(3, $response2['data'][0]['quantity']);
    }

    /**
     * @test
     */
    public function it_clears_cart()
    {
        $product = Product::factory()->create([
            'quantity' => 10
        ]);

        $product2 = Product::factory()->create([
            'quantity' => 15
        ]);

        $this->postJson('/cart/add', [
            'product_id' => $product->id,
            'quantity' => 7
        ]);
        $this->postJson('/cart/add', [
            'product_id' => $product2->id,
            'quantity' => 11
        ]);

        $response = $this->postJson('/cart/clear');
        $response->dump();
        $response->assertJsonCount(0, 'data');
    }
}
