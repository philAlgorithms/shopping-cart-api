<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\{TagResource};
use App\Http\Resources\Media\{MediaFileResource};
use Illuminate\Http\Resources\Json\JsonResource;

class ProductSubCategoryResource extends JsonResource
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
            "category" => ProductCategoryResource::make($this->category),
            "cover_image_url" => $this->cover_image_url,
            "icon_url" => $this->icon_url,
            "products" => ProductCategoryResource::make($this->whenLoaded('products')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
