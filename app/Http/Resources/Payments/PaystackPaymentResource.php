<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Resources\Json\JsonResource;

class PaystackPaymentResource extends JsonResource
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
            'name' => $this->name,
            'payload' => $this->payload,
            'reference' => $this->reference,
        ];
    }
}
