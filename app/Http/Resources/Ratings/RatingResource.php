<?php

namespace App\Http\Resources\Ratings;

use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\UserResource;
use App\Models\Products\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $rateable_type = $this->rateable_type;
        $rateable_is_product = $rateable_type === Product::class;
        return [
            'id' => $this->id,
            'title' => $this->comment_title,
            'comment' => $this->comment,
            'rateable' => $rateable_is_product ? ProductResource::make($this->whenLoaded('rateable')) : $this->whenLoaded('rateable'),
            'rater' => UserResource::make($this->rater)
        ];
    }
}
