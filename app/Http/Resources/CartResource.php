<?php

namespace App\Http\Resources;

use App\Http\Resources\Products\{ProductResource};
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'product' => ProductResource::make($this->product),
            'quantity' => $this->quantity,
            'total' => $this->total
        ];
    }
}
