<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Location\TownResource;
use App\Http\Resources\Users\LogisticsPersonnelResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderJourneyResource extends JsonResource
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
            'id'=> $this->id,
            'order_id'=> $this->order_id,
            'cost'=> $this->cost,
            'journey_id'=> $this->journey_id,
            'destination_town_id'=> $this->order->delivery_town_id ?? null,
            'delivered_at'=> $this->delivered_at,
            'received_at'=> $this->received_at,
            'created_at'=> $this->created_at,
            'status'=> $this->status,
            'origin_town' => TownResource::make($this->has_journey ? $this->journey->origin : null),
            'destination_town' => TownResource::make($this->order->deliveryTown),
            'logistics_personnel' => LogisticsPersonnelResource::make($this->has_journey ? $this->journey->logisticsPersonnel : null),
        ];
    }
}
