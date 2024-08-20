<?php

namespace App\Http\Resources\Users;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class VendorResource extends JsonResource
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
            'name' => $this->user->name,
            'email' => hide_mail($this->email),
            'user' => UserResource::make($this->user),
            'has_verified_email' => $this->hasVerifiedEmail(),
            'user_type' => 'Vendor'
        ];
    }
}
