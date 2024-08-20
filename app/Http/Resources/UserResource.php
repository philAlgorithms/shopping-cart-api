<?php

namespace App\Http\Resources;

use App\Http\Resources\Location\CountryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address' => $this->address,
            'country' => CountryResource::make($this->whenLoaded('country')),
            'phone_number' => $this->phone_number,
            'avatar_url' => $this->avatar_url,
            'city' => $this->city,
            'location' => $this->location,
            "bvn"=> hide_string($this->bvn, 8),
            "bank_account_number" => $this->bvn,
            "bank_slug" => $this->bank_slug,
            "paystack_customer_code" => $this->paystack_customer_code
        ];
    }
}
