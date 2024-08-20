<?php

namespace App\Http\Resources\Products;

use App\Models\Products\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $product = Product::find($this->product_id);
        unset($product->discount);
        return [
            'id' => $this->id,
            'percentage' => $this->percentage,
            'count' => $this->count,
            'expires_at' => $this->expires_at,
            'product' => $this->product
        ];
    }
}
