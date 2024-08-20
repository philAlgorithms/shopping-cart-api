<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\Media\MediaFileResource;
use App\Http\Resources\Ratings\RatingResource;
use App\Http\Resources\Specifications\ProductSpecificationResource;
use App\Http\Resources\Stores\StoreResource;
use App\Http\Resources\TagResource;
use App\Models\Media\MediaFile;
use App\Models\Products\Product;
use App\Models\Products\ProductSubCategory;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $subCategory = ProductSubCategory::find($this->product_sub_category_id);
        unset($subCategory->products);

        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "sub_category" => ProductSubCategoryResource::make(
                $subCategory
            ),
            "cover_image_url" => $this->cover_image_url,
            "brand" => BrandResource::make($this->brand),
            "store" => StoreResource::make($this->store->unsetRelation('products')),
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'images' => $this->images->map(function($img) {
                return $img->temporaryUrl(60);
            }),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            // "specifications" => ProductSpecificationResource::collection($this->whenLoaded('specifications')),
            "reviews" => RatingResource::collection($this->whenLoaded('reviews')),
        ];
    }
}
