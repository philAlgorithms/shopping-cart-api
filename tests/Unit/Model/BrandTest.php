<?php

namespace Tests\Unit\Model;

use App\Models\Media\{MediaFile};
use App\Models\Products\{Brand, Product};
use App\Models\{Tag};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function brands_table_has_expected_columns()
    {
        $this->assertTrue(
            Schema::hasColumns(
                'brands',
                [
                    'name',
                    'logo_id',
                    'deleted_at'
                ]
            )
        );
    }

    /**
     * @test
     */
    public function brand_has_a_logo()
    {
        $logo = MediaFile::factory()->image()->create();
        $brand = Brand::factory()->create([
            'logo_id' => $logo->id
        ]);

        $this->assertInstanceOf(MediaFile::class, $brand->logo);
        $this->assertEquals($logo->id, $brand->logo->id);
    }
    /**
     * @test
     */
    public function brand_has_tags()
    {
        $tags = Tag::factory(5)->create();
        $brand = Brand::factory()->create();

        $this->assertInstanceOf(Collection::class, $brand->tags);
    }

    /**
     * @test
     */
    public function brand_has_products()
    {
        $brand = Brand::factory()->create();
        $products = Product::factory(5)->create([
            'brand_id' => $brand->id
        ]);

        $this->assertInstanceOf(Collection::class, $brand->products);
        $this->assertInstanceOf(Product::class, $brand->products->first());
        $this->assertCount(5, $brand->products);
    }
}
