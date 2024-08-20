<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Stores\StoreResource;
use App\Http\Resources\Users\VendorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
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
            "id"=>$this->id,
            "code"=>$this->code,
            "percentage"=>$this->percentage,
            "count"=>$this->count,
            "is_active"=>$this->is_active,
            "store_id"=>$this->store_id,
            "expires_at"=>$this->expires_at,
            "created_at"=>$this->created_at,
            "deleted_at"=>$this->deleted_at,
            'store' => StoreResource::make($this->vendor),
        ];
    }
}
