<?php

namespace Tests\Feature;

use App\Models\Media\Disk;
use App\Models\Media\MediaFile;
use App\Models\Products\Brand;
use App\Models\Products\Product;
use App\Models\Tag;
use App\Models\Users\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_Lists_all_brands_in_paginated_format()
    {
        $tags = Tag::factory(4)->create();
        $brands = Brand::factory(10)->hasAttached($tags)->create();
        // $products = Product::factory(6)->create([
        //     'brand'
        // ]);

        $response = $this->getJson('/brands?'.http_build_query([
            'tags' => $tags->pluck('id')->toArray(),
            // 'products' => $products->pluck('id')->toArray()
        ]));
        
        
        $response->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'logo', 'tags']]]);
    }

    /**
     * @test
     */
    public function it_shows_a_brand()
    {
        $tags = Tag::factory(4)->create();
        $brand = Brand::factory()
                    ->hasAttached($tags)
                    ->create();
        $products = Product::factory(6)->create([
            'brand_id' => $brand->id
        ]);

        $response = $this->getJson('/brands/'.$brand->id);
        
        $response->assertOk()
            ->assertJsonCount(4, 'data.tags')
            ->assertJsonCount(6, 'data.products');
    }

    /**
     * @test
     */
    public function it_creates_a_brand_with_an_existing_logo()
    {
        $admin = Admin::factory()->create();
        $tags = Tag::factory(4)->create();
        $logo = MediaFile::factory()->image()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/brands', 
            [
                'name' => 'Apple',
                'logo_id' => $logo->id,
                'tags' => $tags->pluck('id')->toArray()
            ]
        );
        $response->assertCreated()
                  ->assertJsonPath('data.name', 'Apple')
                  ->assertJsonPath('data.logo.id', $logo->id)
                  ->assertJsonCount(4, 'data.tags');

        $this->assertDatabaseHas('brands', [
            'name' => 'Apple'
        ]);
    }

    /**
     * @test
     */
    public function it_creates_a_brand_while_uploading_a_logo()
    {
        Storage::fake(env('DEFAULT_DISK', 'local'));
        $admin = Admin::factory()->create();
        $tags = Tag::factory(4)->create();
        $logo = MediaFile::factory()->image()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/brands', 
            [
                'name' => 'Samsung',
                'logo' => UploadedFile::fake()->image('apple.jpg')
            ]
        );
        $response->assertCreated()
                  ->assertJsonPath('data.name', 'Samsung')
                  ->assertJsonStructure(['data' => ['logo' => []]]);

        Storage::disk(Disk::find($response['data']['logo']['disk_id'])->name)
            ->assertExists($response['data']['logo']['path']);
            
        $this->assertDatabaseHas('brands', [
            'name' => 'Samsung'
        ]);
    }

    /**
     * @test
     */
    public function file_attaching_takes_precedence_over_file_upload()
    {
        Storage::fake(env('DEFAULT_DISK', 'local'));
        $admin = Admin::factory()->create();
        $tags = Tag::factory(4)->create();
        $logo = MediaFile::factory()->image()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/brands', 
            [
                'name' => 'Xiaomi',
                'logo' => UploadedFile::fake()->image('apple.jpg'),
                'logo_id' => $logo->id
            ]
        );
        
        $response->assertCreated()
                  ->assertJsonPath('data.logo.id', $logo->id);

        $this->assertDatabaseHas('brands', [
            'name' => 'Xiaomi'
        ]);
    }

    /**
     * @test
     */
    public function it_updates_a_brand_with_an_existing_logo()
    {
        $admin = Admin::factory()->create();
        $logo = MediaFile::factory()->image()->create();
        $brand = Brand::factory()->create([
            'name' => 'Apple'
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->putJson(
            '/brands/' . $brand->id, 
            [
                'name' => 'Oppo',
                'logo_id' => $logo->id
            ]
        );
        $response->assertOk()
                  ->assertJsonPath('data.name', 'Oppo')
                  ->assertJsonPath('data.logo.id', $logo->id);

        $this->assertDatabaseHas('brands', [
            'name' => 'Oppo'
        ]);
    }

    /**
     * @test
     */
    public function it_updates_a_brand_while_uploading_a_logo()
    {
        Storage::fake(env('DEFAULT_DISK', 'local'));
        $admin = Admin::factory()->create();
        $logo = MediaFile::factory()->image()->create();
        $brand = Brand::factory()->create([
            'name' => 'Apple'
        ]);

        $this->actingAs($admin, 'admin');

        $response = $this->putJson(
            '/brands/' . $brand->id, 
            [
                'name' => 'Samsung',
                'logo' => UploadedFile::fake()->image('apple.jpg')
            ]
        );
        $response->assertOk()
                  ->assertJsonPath('data.name', 'Samsung')
                  ->assertJsonStructure(['data' => ['logo' => []]]);

        Storage::disk(Disk::find($response['data']['logo']['disk_id'])->name)
            ->assertExists($response['data']['logo']['path']);

        $this->assertDatabaseHas('brands', [
            'name' => 'Samsung'
        ]);
    }

    /**
     * @test
     */
    public function it_deletes_a_brand()
    {
        $admin = Admin::factory()->create();
        $brand =  Brand::factory()->create();

        $this->actingAs($admin, 'admin');
        $response = $this->deleteJson(
            '/brands/' . $brand->id
        );

        $response->assertOk();
        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    /**
     * @test
     */
    public function it_force_deletes_a_brand()
    {
        $admin = Admin::factory()->create();
        $brand =  Brand::factory()->create();

        $this->actingAs($admin, 'admin');
        $response = $this->deleteJson(
            '/brands/force-delete/' . $brand->id
        );
        
        $response->assertOk();
        $this->assertNull(Brand::find($brand->id));
    }
}
