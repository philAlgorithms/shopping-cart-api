<?php

namespace Tests\Feature;

use App\Models\Products\Product;
use App\Models\Specifications\{Specification};
use App\Models\Users\{Admin};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SpecificationControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_Lists_all_specifications_in_paginated_format()
    {
        $specifications1 = Specification::factory(10)->create();
        $specifications2 = Specification::factory(6)->create();
        $products = Product::factory(3)
                    ->hasAttached($specifications1, 
                        ['detail' => fake()->text(100)]
                    )
                    ->create();
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');
        $response1 = $this->getJson('/specifications?'.http_build_query([
            'id' => [
                ...$specifications1->pluck('id')->toArray(),
                ...$specifications2->pluck('id')->toArray()
            ],
        ]));

        $response2 = $this->getJson('/specifications?'.http_build_query([
            'id' => $specifications1->pluck('id')->toArray(),
        ]));

        $response3 = $this->getJson('/specifications?'.http_build_query([
            'names' => $specifications1->pluck('name')->toArray(),
        ]));

        $response4 = $this->getJson('/specifications?'.http_build_query([
            'id' => $specifications1->pluck('id')->toArray(),
            'names' => $specifications1->pluck('name')->toArray(),
        ]));

        $response5 = $this->getJson('/specifications?'.http_build_query([
            'id' => $specifications2->pluck('id')->toArray(),
        ]));

        $response6 = $this->getJson('/specifications?'.http_build_query([
            'names' => $specifications2->pluck('name')->toArray(),
        ]));

        $response7 = $this->getJson('/specifications?'.http_build_query([
            'id' => $specifications2->pluck('id')->toArray(),
            'names' => $specifications2->pluck('name')->toArray(),
        ]));

        $response8 = $this->getJson('/specifications?'.http_build_query([
            'id' => $specifications1->pluck('id')->toArray(),
            'names' => $specifications2->pluck('name')->toArray(),
        ]));

        $response9 = $this->getJson('/specifications?'.http_build_query([
            'products' => $products->pluck('id')->toArray(),
        ]));
        
        // $response->dump();

        $response1->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(16, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);

        $response2->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);

        $response3->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);

        $response4->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);

        $response5->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(6, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);

        $response6->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(6, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);

        $response7->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(6, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name']]]);

        $response8->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(0, 'data');

        $response9->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(10, 'data');
    }

    /**
     * @test
     */
    public function it_creates_a_specification()
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/specifications', 
            [
                'name' => 'color',
                'icon' => 'fa fa-palette'
            ]
        );

        $response->dump();
        $response->assertCreated()
                 ->assertJsonPath('data.name', 'color')
                 ->assertJsonPath('data.icon', 'fa fa-palette');
    }
}
