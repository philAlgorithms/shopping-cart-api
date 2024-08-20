<?php

namespace App\Http\Resources;

use App\Http\Resources\Stores\StoreResource;
use App\Http\Resources\Users\AdminResource;
use Illuminate\Http\Resources\Json\JsonResource;

class StorePayoutResource extends JsonResource
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
            'amount' => $this->amount,
            'approved' => $this->is_approved,
            'declined' => $this->is_declined,
            'approved_at' => $this->is_approved_at,
            'declined_at' => $this->is_declined_at,
            'store' => StoreResource::make($this->store),
            'approved_by' => AdminResource::make($this->approver),
            'declined_by' => AdminResource::make($this->decliner) 
        ];
    }
}
