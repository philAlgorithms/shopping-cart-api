<?php

namespace App\Http\Resources;

use App\Http\Resources\Order\OrderResource;
use App\Http\Resources\Users\BuyerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyerReferralResource extends JsonResource
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
            'referee' => BuyerResource::make($this->referee),
            'program' => BuyerReferralProgramResource::make($this->program),
            'order' => OrderResource::make($this->order),
            'reward' => $this->reward
        ];
    }
}
