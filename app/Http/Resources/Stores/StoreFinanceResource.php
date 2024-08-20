<?php

namespace App\Http\Resources\Stores;

use App\Http\Resources\Media\MediaFileResource;
use App\Http\Resources\Users\VendorResource;
use App\Models\Users\{Admin, Vendor};
use Illuminate\Http\Resources\Json\JsonResource;

class StoreFinanceResource extends JsonResource
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
            'balance' => $this->available_balance,
            'total_product_purchase' => $this->total_product_purchase,
            'total_approved_payouts' => $this->total_approved_payouts
        ];
    }
}
