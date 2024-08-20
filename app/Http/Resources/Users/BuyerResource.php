<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class BuyerResource extends JsonResource
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
            'email' => hide_mail($this->email),
            'user' => UserResource::make($this->user),
            'balance' => $this->current_wallet_balance,
            'has_verified_email' => $this->hasVerifiedEmail(),
            'user_type' => 'Buyer'
        ];
    }
}
