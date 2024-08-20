<?php

namespace Tests\Unit\Model;

use App\Models\Media\MediaFile;
use App\Models\Products\Product;
use App\Models\Specifications\Specification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * @test
     */
    public function product_has_specifications()
    {
        $specifications = Specification::factory(5)->create();
        $product = Product::factory()->create();

        $this->assertInstanceOf(Collection::class, $product->specifications);
    }

    /**
     * @test
     */
    public function product_has_cover_image()
    {
        $image = MediaFile::factory()->create();
        $product = Product::factory()->create([
            'cover_image_id' => $image->id
        ]);

        $this->assertInstanceOf(MediaFile::class, $product->coverImage);
    }
}
