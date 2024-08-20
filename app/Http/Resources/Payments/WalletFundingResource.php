<?php

namespace App\Http\Resources\Payments;

use App\Http\Resources\CurrencyResource;
use App\Http\Resources\Users\BuyerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletFundingResource extends JsonResource
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
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'paystack_payment' => PaystackPaymentResource::make($this->paystackPayment),
            'currency' => CurrencyResource::make($this->currency),
            'buyer' => BuyerResource::make($this->buyer)
        ];
    }
}
