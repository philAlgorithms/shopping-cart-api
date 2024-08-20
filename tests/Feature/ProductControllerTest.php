<?php

namespace Tests\Feature;

use App\Models\Media\Disk;
use App\Models\Products\Brand;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Products\ProductSubCategory;
use App\Models\Ratings\Rating;
use App\Models\Specifications\ProductSpecification;
use App\Models\Specifications\Specification;
use App\Models\Stores\Store;
use App\Models\Tag;
use App\Models\User;
use App\Models\Users\Admin;
use App\Models\Users\Vendor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * @test
     */
    public function it_Lists_all_products_in_paginated_format()
    {
        $tags = Tag::factory(4)->create();
        $products = Product::factory(10)->hasAttached($tags)->create();
        $specifications = Specification::factory(6)
                            ->hasAttached($products, 
                                ['detail' => fake()->text(100)]
                            )
                            ->create();

        $response = $this->getJson('/products?'.http_build_query([
            'tags' => $tags->pluck('id')->toArray(),
            'specifications' => $specifications->pluck('id')->toArray()
        ]));
        
        
        $response->assertOk()
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonCount(10, 'data')
            ->assertJsonStructure(['data' => ['*' => ['id', 'cover_image', 'tags', 'specifications']]]);
    }

    /**
     * @test
     */
    public function it_shows_a_product()
    {
        $tags = Tag::factory(4)->create();
        $product = Product::factory()
                    ->hasAttached($tags)
                    ->create();
        $specifications = Specification::factory(6)
                            ->hasAttached($product, 
                                ['detail' => fake()->text(100)]
                            )
                            ->create();
        $reviews = Rating::factory(3)->for($product, 'rateable')
                    ->create();

        $response = $this->getJson('/products/'.$product->id);
        $response->dump();
        $response->assertOk()
            ->assertJsonCount(4, 'data.tags')
            ->assertJsonCount(6, 'data.specifications')
            ->assertJsonCount(3, 'data.reviews');
    }

    /**
     * @test
     */
    public function it_creates_a_product()
    {
        $vendor = Vendor::factory()->create();
        $tags = Tag::factory(4)->create();
        $specifications = Specification::factory(6)->create();
        
        $category = ProductCategory::factory()->create([
            'name' => 'Phones and Tablets'
        ]);
        $sub_category = ProductSubCategory::factory()->create([
            'name' => 'iPhones',
            'product_category_id' => $category->id
        ]);
        $brand = Brand::factory()->create([
            'name' => 'Apple'
        ]);
        $store = Store::factory()->create([
            'vendor_id' => $vendor->id
        ]);

        Storage::fake(env('DEFAULT_DISK', 'local'));
        $this->actingAs($vendor, 'vendor');

        $response = $this->postJson(
            '/products', 
            [
                'cover_image' => UploadedFile::fake()->image('image.jpg'),
                'name' => 'iPhone 14 Pro Max',
                'product_sub_category_id' => $sub_category->id,
                'brand_id' => $brand->id,
                'tags' => $tags->pluck('id')->toArray(),
                'specifications' => array_map('specPivots', $specifications->pluck('id')->toArray())
            ]
        );
        $response->dump();
        $response->assertCreated()
                  ->assertJsonPath('data.name', 'iPhone 14 Pro Max')
                  ->assertJsonPath('data.store.id', $vendor->store->id)
                  ->assertJsonCount(4, 'data.tags')
                  ->assertJsonCount(6, 'data.specifications');

        Storage::disk(Disk::find($response['data']['cover_image']['disk_id'])->name)
            ->assertExists($response['data']['cover_image']['path']);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 14 Pro Max'
        ]);
    }

    /**
     * @test
     */
    public function it_updates_a_product()
    {
        $vendor = Vendor::factory()->create();
        $tags = Tag::factory(4)->create();
        $specifications = Specification::factory(6)->create();
        $store = Store::factory()->create([
            'vendor_id' => $vendor->id
        ]);
        $product = Product::factory()->create([
            'store_id' => $store->id
        ]);
        
        $category = ProductCategory::factory()->create([
            'name' => 'Phones and Tablets'
        ]);
        $sub_category = ProductSubCategory::factory()->create([
            'name' => 'iPhones',
            'product_category_id' => $category->id
        ]);
        $brand = Brand::factory()->create([
            'name' => 'Apple'
        ]);
        Storage::fake(env('DEFAULT_DISK', 'local'));
        $this->actingAs($vendor, 'vendor');

        $response = $this->putJson(
            '/products/' . $product->id, 
            [
                'name' => 'iPhone 14 Pro Max',
                'tags' => $tags->pluck('id')->toArray(),
                'specifications' => array_map('specPivots', $specifications->pluck('id')->toArray())
            ]
        );
        $response->dump();
        $response->assertOk()
                  ->assertJsonPath('data.name', 'iPhone 14 Pro Max')
                  ->assertJsonPath('data.store.id', $vendor->store->id)
                  ->assertJsonCount(4, 'data.tags')
                  ->assertJsonCount(6, 'data.specifications');

        // Storage::disk(Disk::find($response['data']['cover_image']['disk_id'])->name)
        //     ->assertExists($response['data']['cover_image']['path']);

        $this->assertDatabaseHas('products', [
            'name' => 'iPhone 14 Pro Max'
        ]);
    }

    /**
     * @test?
     */
    public function it_does_not_allow_creating_if_scope_is_not_provided()
    {
        $vendor = Vendor::factory()->create();
        
        $category = ProductCategory::factory()->create([
            'name' => 'Phones and Tablets'
        ]);
        $sub_category = ProductSubCategory::factory()->create([
            'name' => 'iPhones',
            'product_category_id' => $category->id
        ]);
        $brand = Brand::factory()->create([
            'name' => 'Apple'
        ]);
        $store = Store::factory()->create([
            'vendor_id' => $vendor->id
        ]);

        $token = $vendor->createToken('test', ['product.create']);
        // dd($token);

        $response = $this->postJson(
            '/products', 
            [
                'name' => 'iPhone 14 Pro Max',
                'product_sub_category_id' => $sub_category->id,
                'brand_id' => $brand->id,
            ],
            [
                'Authorization' => 'Bearer '.$token->plainTextToken,
            ]
        );

        $response->dump();
        $response->assertCreated()
                  ->assertJsonPath('data.name', 'iPhone 14 Pro Max')
                  ->assertJsonCount(4, 'data.tags')
                  ->assertJsonCount(6, 'data.specifications');
    }

    /**
     * @test
     */
    public function it_deletes_a_product()
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin, 'admin');
        $response = $this->deleteJson(
            '/products/' . $product->id
        );

        $response->assertOk();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /**
     * @test
     */
    public function it_force_deletes_a_product()
    {
        $admin = Admin::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin, 'admin');
        $response = $this->deleteJson(
            '/products/force-delete/' . $product->id
        );
        
        $response->assertOk();
        $this->assertNull(Product::find($product->id));
    }
}