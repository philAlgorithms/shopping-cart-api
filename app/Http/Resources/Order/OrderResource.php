<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\{CartResource};
use App\Http\Resources\Users\BuyerResource;
use App\Models\Users\Vendor;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
        $isOwner = $user instanceof Vendor;
        return [
            'id' => $this->id,
            'installments' => $this->installments,
            'cart_total' => $isOwner ? $this->vendorCartTotal($user) : $this->cart_total,
            'coupon_discount' => $isOwner ? $this->vendorCouponDiscount($user) : $this->coupon_discount,
            'shipping_fee' => $this->total_shipping_cost,
            'total' => $isOwner ? $this->vendorTotal($user) : $this->total,
            'requires_shipping' => $this->requires_shipping,
            'payment_details' => OrderPaymentDetailsResource::make($this),
            'cart' => CartResource::collection(
                $isOwner ? $this->vendorCart($user)->get() : $this->cart
            ),
            'buyer' => BuyerResource::make($this->buyer),
            'coupon' => CouponResource::make($this->coupon),
        ];
    }
}
