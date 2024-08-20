<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertResource extends JsonResource
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
            "heading"=> $this->heading,
            "link"=> $this->link,
            "description"=> $this->description,
            "image_url" => $this->image_url,
            "tags" => TagResource::collection($this->whenLoaded('tags')),
        ];
    }
}
