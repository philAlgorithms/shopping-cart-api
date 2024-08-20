<?php

namespace App\Http\Resources\Stores;

use App\Http\Resources\Media\MediaFileResource;
use App\Http\Resources\Users\VendorResource;
use App\Models\Users\{Admin, Vendor};
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = auth()->user();
        $isOwner = $user instanceof Vendor && $user->store->id === $this->id;
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "logo" => MediaFileResource::make($this->logo),
            'vendor' => VendorResource::make($this->vendor),
            'product_count' => $this->products->count(),
            $this->mergeWhen(
                ($user instanceof Admin || $isOwner),
                [
                    'cac_file_url' => $this->cac_url
                ]
            ),
        ];
    }
}
