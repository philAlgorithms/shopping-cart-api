<?php

namespace App\Http\Resources;

use App\Http\Resources\Users\AdminResource;
use App\Http\Resources\Users\BuyerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyerReferralProgramResource extends JsonResource
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
            'code' => $this->code,
            'buyer' => BuyerResource::make($this->buyer),
            'activator' => AdminResource::make($this->activator),
            'deactivator' => AdminResource::make($this->deactivator),
            'is_active' => $this->is_active,
            'activated_at' => $this->activated_at,
            'deactivated_at' => $this->deactivated_at
        ];
    }
}
