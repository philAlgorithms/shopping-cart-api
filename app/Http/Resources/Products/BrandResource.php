<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\Media\{MediaFileResource};
use App\Http\Resources\{TagResource};
use App\Models\Products\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "logo_url" => $this->logo_url,
            "tags" => TagResource::collection($this->whenLoaded('tags')),
            "products" => ProductResource::collection(
                $this->whenLoaded(
                    'products',
                    Product::query()->without(['brand'])->where('brand_id', $this->id)->get()
                )
            ),
        ];
    }
}
