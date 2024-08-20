<?php

namespace App\Http\Resources\Location;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'iso' => $this->iso,
            'nicename' => $this->nicename,
            'iso3' => $this->iso3,
            'numcode' => $this->numcode,
            'phonecode' => $this->phonecode
        ];
    }
}
