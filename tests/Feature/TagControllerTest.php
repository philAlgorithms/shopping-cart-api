<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Users\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_Lists_all_tags_in_paginated_format()
    {
        $tags1 = Tag::factory(10)->create();
        $tags2 = Tag::factory(6)->create();

        $response1 = $this->getJson('/tags?'.http_build_query([
            'id' => [
                ...$tags1->pluck('id')->toArray(),
                ...$tags2->pluck('id')->toArray()
            ],
        ]));

        $response2 = $this->getJson('/tags?'.http_build_query([
            'id' => $tags1->pluck('id')->toArray(),
        ]));

        $response3 = $this->getJson('/tags?'.http_build_query([
            'names' => $tags1->pluck('name')->toArray(),
        ]));

        $response4 = $this->getJson('/tags?'.http_build_query([
            'id' => $tags1->pluck('id')->toArray(),
            'names' => $tags1->pluck('name')->toArray(),
        ]));

        $response5 = $this->getJson('/tags?'.http_build_query([
            'id' => $tags2->pluck('id')->toArray(),
        ]));

        $response6 = $this->getJson('/tags?'.http_build_query([
            'names' => $tags2->pluck('name')->toArray(),
        ]));

        $response7 = $this->getJson('/tags?'.http_build_query([
            'id' => $tags2->pluck('id')->toArray(),
            'names' => $tags2->pluck('name')->toArray(),
        ]));

        $response8 = $this->getJson('/tags?'.http_build_query([
            'id' => $tags1->pluck('id')->toArray(),
            'names' => $tags2->pluck('name')->toArray(),
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
    }

    /**
     * @test
     */
    public function it_shows_a_tag()
    {
        $tag = Tag::factory()->create();
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->getJson('/tags/'.$tag->id);

        $response->dump();

        $response->assertOk()
            ->assertJsonStructure(["data" => ["id", "name"]]);
    }

    /**
     * @test
     */
    public function it_creates_a_tag()
    {
        $admin = Admin::factory()->create();

        // $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/tags', 
            [
                'name' => 'unisex clothes',
            ]
        );

        $response->dump();
        $response->assertCreated()
                 ->assertJsonPath('data.name', 'unisex clothes');
    }

    /**
     * @test
     */
    public function it_updates_a_tag()
    {
        $tag = Tag::factory()->create(['name' => 'samsumg products']);
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->putJson(
            '/tags/' . $tag->id, 
            [
                'name' => 'apple products',
            ]
        );

        $response->dump();
        $response->assertOk()
                 ->assertJsonPath('data.name', 'apple products');
    }

    /**
     * @test
     */
    public function it_deletes_a_tag()
    {
        $admin = Admin::factory()->create();
        $tag = Tag::factory()->create();

        $this->actingAs($admin, 'admin');
        $response = $this->deleteJson(
            '/tags/' . $tag->id
        );

        $response->assertOk();
        $this->assertNull(Tag::find($tag->id));
    }

    /**
     * @test?
     */
    // public function it_force_deletes_a_tag()
    // {
    //     $admin = Admin::factory()->create();
    //     $tag = Tag::factory()->create();

    //     $this->actingAs($admin, 'admin');
    //     $response = $this->deleteJson(
    //         '/tags/force-delete/' . $tag->id
    //     );
        
    //     $response->assertOk();
    //     $this->assertNull(Tag::find($tag->id));
    // }
}
