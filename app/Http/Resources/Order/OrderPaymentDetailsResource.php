<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Payments\InstallmentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderPaymentDetailsResource extends JsonResource
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
            'total_installments' => $this->total_installment,
            'waybill_cost' => $this->waybill_cost,
            'home_delivery_cost' => $this->home_delivery_cost,
            'amount_paid_via_paystack' => $this->amount_paid_via_paystack,
            'amount_paid_from_wallet' => $this->amount_paid_from_wallet,
            'amount_paid' => $this->amount_paid,
            'has_paid_at_all' => $this->has_paid_at_all,
            'has_paid_full' => $this->has_paid_full,
            'installment_payments' => InstallmentResource::collection(
                $this->installmentPayments
            ),
        ];
    }
}
