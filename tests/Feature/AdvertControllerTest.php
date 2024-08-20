<?php

namespace Tests\Feature;

use App\Models\Media\Disk;
use App\Models\Media\MediaFile;
use App\Models\Advert;
use App\Models\Tag;
use App\Models\Users\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdvertControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_lists_all_adverts_in_paginated_format()
    {
        $tags = Tag::factory(4)->create();
        $adverts = Advert::factory(10)->hasAttached($tags)->create();

        $response = $this->getJson('/adverts?'.http_build_query([
            'tags' => $tags->pluck('id')->toArray(),
        ]));
        
        $response->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'heading', 'link', 'description', 'image_url']]]);
    }

    /**
     * @test
     */
    public function it_shows_an_advert()
    {
        $tags = Tag::factory(4)->create();
        $advert = Advert::factory()
                    ->hasAttached($tags)
                    ->create();

        $response = $this->getJson('/adverts/'.$advert->id);
        
        $response->assertOk()
        ->assertJsonStructure(['data' => ['id', 'heading', 'link', 'description', 'image_url']]);
    }

    /**
     * @test
     */
    public function it_creates_an_advert_while_uploading_an_image()
    {
        Storage::fake(env('DEFAULT_DISK', 'local'));
        $admin = Admin::factory()->create();
        $tags = Tag::factory(4)->create();
        $image = MediaFile::factory()->image()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/adverts', 
            [
                'heading' => 'Samsung',
                'link' => fake()->url(),
                'description' => 'Lorem ipsum sit amit diet',
                'image' => UploadedFile::fake()->image('apple.jpg')
            ]
        );
        $response->assertCreated()
                  ->assertJsonPath('data.heading', 'Samsung')
                  ->assertJsonStructure(['data' => ['image_url', 'link', 'heading', 'description']]);
            
        $this->assertDatabaseHas('adverts', [
            'heading' => 'Samsung',
            'description' => 'Lorem ipsum sit amit diet'
        ]);

        // $this->get($response['data']['link'])->assertOk();
    }

    /**
     * @test
     */
    public function it_updates_an_advert_while_uploading_an_image()
    {
        Storage::fake(env('DEFAULT_DISK', 'local'));
        $admin = Admin::factory()->create();
        $image = MediaFile::factory()->image()->create();
        $advert = Advert::factory()->create([
            'heading' => 'Lorem ipsum sit amit diet'
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->putJson(
            '/adverts/' . $advert->id, 
            [
                'heading' => 'Nel principio era Iddio',
                'image' => UploadedFile::fake()->image('apple.jpg')
            ]
        );

        $response->assertOk()
                  ->assertJsonPath('data.heading', 'Nel principio era Iddio')
                  ->assertJsonStructure(['data' => ['image_url', 'link', 'heading', 'description']]);

        $this->assertDatabaseHas('adverts', [
            'heading' => 'Nel principio era Iddio'
        ]);
    }

    /**
     * @test
     */
    public function it_deletes_an_advert()
    {
        $admin = Admin::factory()->create();
        $advert =  Advert::factory()->create();

        $this->actingAs($admin, 'admin');
        $response = $this->deleteJson(
            '/adverts/' . $advert->id
        );
        
        $response->assertOk();
        $this->assertNull(Advert::find($advert->id));
    }
}
