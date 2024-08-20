<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Location\TownResource;
use App\Http\Resources\Users\LogisticsPersonnelResource;
use Illuminate\Http\Resources\Json\JsonResource;

class JourneyResource extends JsonResource
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
            'origin' => TownResource::make($this->origin),
            'destination' => TownResource::make($this->destination),
            'logistics_personnel' => LogisticsPersonnelResource::make($this->logisticsPersonnel),
            'has_left' => $this->has_left,
            'has_arrived' => $this->has_arrived,
            'created_at' => $this->created_at,
            'left_at' => $this->left_at,
            'arrived_at' => $this->arrived_at,
            'waybills' => OrderJourneyResource::collection($this->orderJourneys),
            'status'=> $this->status,
        ];
    }
}
