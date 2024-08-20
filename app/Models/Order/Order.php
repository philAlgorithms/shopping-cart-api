<?php

namespace App\Models\Order;

use App\Models\Cart;
use App\Models\Currency;
use App\Models\Location\Town;
use App\Models\Payments\{PaystackPurchase, Purchase, WalletFunding, WalletPurchase};
use App\Models\RangeType;
use App\Models\Users\Buyer;
use App\Models\Users\LogisticsPersonnel;
use App\Models\Users\Vendor;
use Illuminate\Database\Eloquent\{Builder, Collection};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasOne};
use Illuminate\Validation\ValidationException;
use Unicodeveloper\Paystack\Paystack;
use Unicodeveloper\Paystack\TransRef;

class Order extends Model
{
    use HasFactory;

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class, 'order_id');
    }

    public function vendorCart(Vendor $vendor): HasMany
    {
        return $this->cart()->whereHas(
            'product',
            fn ($query) => $query->whereHas(
                'store',
                fn ($q) => $q->where('vendor_id', $vendor->id)
            )
        );
    }

    public function waybill(): HasOne
    {
        return $this->hasOne(OrderJourney::class, 'order_id');
    }

    public function homeDelivery(): HasOne
    {
        return $this->hasOne(HomeDelivery::class, 'order_id');
    }

    public function paystackPurchases(): HasMany
    {
        return $this->hasMany(PaystackPurchase::class, 'order_id');
    }

    public function walletPurchases(): HasMany
    {
        return $this->hasMany(WalletPurchase::class, 'order_id');
    }

    public function purchases()
    {
        return Purchase::whereMorphRelation(
            'purchasable',
            [PaystackPurchase::class, WalletPurchase::class],
            fn ($builder) => $builder->where('order_id', $this->id)

        );
    }

    public function tripsWithSameDestination(): Builder
    {
        return Journey::where('destination_town_id', $this->delivery_town_id);
    }

    public function logisticsPersonnelsWithSameDestination(): Builder
    {
        return LogisticsPersonnel::where('destination_town_id', $this->delivery_town_id);
    }

    public function autoInitiateWaybill()
    {
        $waybill = $this->initiateWaybill();
        if ($waybill && $this->has_paid_full) {
            $trip = $this->tripsWithSameDestination()->first();
            $handler = $this->logisticsPersonnelsWithSameDestination()->first();
            if (!is_null($trip)) {
                $waybill->update([
                    'journey_id' => $trip->id,
                    'logistics_personnel_id' => $handler->id ?? null
                ]);
            }
        }
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function deliveryTown(): BelongsTo
    {
        return $this->belongsTo(Town::class, 'delivery_town_id');
    }

    public function getCartTotalAttribute(): float
    {
        return array_sum($this->cart->map(function (Cart $cart) {
            return $cart->total;
        })->all());
    }

    public function getCouponDiscountAttribute(): float
    {
        if(is_null($this->coupon))
        {
            return 0;
        }
        if($this->locked)
        {
            return $this->cart_total * 0.01 * ($this->coupon_percentage ?? 0) ;
        }else {
            return array_sum($this->coupon->orderItems($this)->get()->map(function (Cart $cart) {
                return $cart->total;
            })->all()) * $this->coupon->percentage * 0.01;
        }
    }

    public function vendorCartTotal(Vendor $vendor): float
    {
        return array_sum($this->vendorCart($vendor)->get()->map(function (Cart $cart) {
            return $cart->total;
        })->all());
    }

    public function vendorCouponDiscount(Vendor $vendor): float
    {
        if(is_null($this->coupon) || $this->coupon->store_id !== $vendor->store->id)
        {
            return 0;
        }
        if($this->locked)
        {
            return $this->vendorCartTotal($vendor) * 0.01 * ($this->coupon_percentage ?? 0) ;
        }else {
            return array_sum($this->coupon->vendorOrderItems($this, $vendor)->get()->map(function (Cart $cart) {
                return $cart->total;
            })->all()) * $this->coupon->percentage * 0.01;
        }
    }

    public function vendorTotal(Vendor $vendor): float
    {
        return $this->vendorCartTotal($vendor) - $this->vendorCouponDiscount($vendor);
    }

    public function getTotalShippingCostAttribute(): float
    {
        return $this->waybill_cost + $this->home_delivery_cost;
    }

    public function getTotalAttribute(): float
    {
        return $this->cart_total - $this->coupon_discount + $this->total_shipping_cost;
    }

    public function getPaidPaystackPurchasesAttribute(): Collection
    {
        return $this->paystackPurchases->filter(function (PaystackPurchase $purchase) {
            return $purchase->has_paid;
        });
    }

    public function getPaidWalletPurchasesAttribute(): Collection
    {
        return $this->walletPurchases->filter(function (WalletPurchase $purchase) {
            return true;
        });
    }

    public function getAmountPaidViaPaystackAttribute(): float
    {
        return array_sum($this->paid_paystack_purchases->map(function (PaystackPurchase $purchase) {
            return $purchase->amount;
        })->all());
    }

    public function getAmountPaidFromWalletAttribute(): float
    {
        return array_sum($this->paid_wallet_purchases->map(function (WalletPurchase $purchase) {
            return $purchase->amount;
        })->all());
    }

    public function getTotalInstallmentAttribute(): float
    {
        return array_sum($this->installmentPayments->map(function (WalletFunding $funding) {
            return $funding->amount;
        })->all());
    }

    public function getAmountPaidAttribute(): float
    {
        return $this->amount_paid_via_paystack + $this->amount_paid_from_wallet;
    }

    public function getHasPaidFullAttribute(): bool
    {
        return $this->amount_paid >= $this->total;
    }

    public function getHasPaidAtAllAttribute(): bool
    {
        return $this->total_installment > 0;
    }

    public function fillCart()
    {
        $cart = $this->buyer->session_cart;

        foreach ($cart as $item) {
            $item->fill([
                'order_id' => $this->id,
            ]);
            $item->save();
        }

        session(['cart' => collect([])]);

        return $this->refresh();
    }

    public function payInstallment()
    {
        $amount = $this->total / $this->installments;

        return $this->buyer->fundWallet($amount, $this);
    }

    public function pay()
    {
        if ($this->has_paid_full) {
            throw ValidationException::withMessages([
                'order' => 'Order has already been paid for.'
            ]);
        }

        $installments = $this->installments;
        if ($this->cart()->count() < 1) {
            $this->fillCart();
        }
        $this->refresh();

        if ($installments === 1) {
            $amount = $this->total;
            $user = $this->buyer->user;
            $ref = TransRef::getHashedToken();

            try {
                $paystack = (new Paystack);
                $data = [
                    "amount" => ceil($amount * 100),
                    "reference" => $ref,
                    "email" => $this->buyer->email,
                    "first_name" => $user->first_name,
                    "last_name" => $user->last_name,
                    "currency" => "NGN",
                    "metadata" => [
                        "payable_type" => PaystackPurchase::class,
                        "payable_load" => [
                            'order_id' => $this->id ?? null,
                            'currency_id' => Currency::firstWhere('acronym', 'NGN')->id
                        ]
                    ]
                ];
                return $paystack->getAuthorizationUrl($data)->redirectNow();
            } catch (\Exception $e) {
                throw $e;
                throw ValidationException::withMessages(['paystack' => 'Some error occured. Please try again later.']);
            }
        } else {
            // Installments must be a number greater than 1 as the integer field cannot be null
            return $this->payInstallment();
        }
    }

    public function installmentPayments(): HasMany
    {
        return $this->hasMany(WalletFunding::class, 'order_id');
    }

    public function successfulInstallments(): HasMany
    {
        return $this->installmentPayments()->whereHas('paystackPayment', fn ($builder) => $builder->whereJsonContains('payload', ['status' => 'success']));
    }

    public function getHasPaidFullInstallmentsAttribute(): bool
    {
        return ceil($this->total_installments) >= ceil($this->total);
    }

    public function getHasPaidAnyInstallmentsAttribute(): bool
    {
        return $this->total_installments > 0;
    }

    public function getHasArrivedAttribute(): bool
    {
        return !is_null($this->arrived_at) && $this->arrived_at < now();
    }

    public function getIsLockedAttribute(): bool
    {
        return !is_null($this->locked_at) && $this->locked_at < now();
    }

    public function getIsLockableAttribute(): bool
    {
        return is_null($this->locked_at);
    }

    /**
     * Lock the order
     */
    public function lock(): bool
    {
        if ($this->is_lockable) {
            foreach ($this->cart as $item) {
                $item->update([
                    'price' => $item->product->price,
                ]);
            }
            $coupon = $this->coupon;
            return $this->update([
                'locked_at' => now(),
                'coupon_percentage' => is_null($coupon) ? null : $coupon->percentage
            ]);
        } else
            return false;
    }

    public function getIsCancelledAttribute(): bool
    {
        return !is_null($this->cancelled_at);
    }

    /**
     * Cancel a task
     */
    public function cancel(): bool
    {
        return $this->is_cancellable ?
            $this->update([
                'cancelled_at' => now()
            ]) :
            false;
    }

    /**
     * Tries to pay for an order using funds from the wallet
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function payFromWallet()
    {
        if ($this->has_paid_full) {
            throw ValidationException::withMessages([
                'order' => 'Order has already been paid for.'
            ]);
        }

        if ($this->has_enough_to_pay_from_wallet_balance) {
            return $this->walletPurchases()->create([
                'currency_id' => Currency::firstWhere('acronym', 'NGN')->id,
                'amount' => $this->total
            ]);
        } else {
            throw ValidationException::withMessages([
                'order' => 'Not enough funds to pay from wallet.'
            ]);
        }
    }

    // BOOLEANS START
    public function getHasHomeDeliveryAttribute(): bool
    {
        return $this->home_delivery && !is_null($this->homeDelivery);
    }

    public function getHasShippingAttribute(): bool
    {
        return !is_null($this->waybill);
    }

    /**
     * Checks if the `delivery_town` is the same as the default town where waybill journey starts
     */
    public function getRequiresShippingAttribute(): bool
    {
        return true;
    }

    public function getHasEnoughToPayFromWalletBalanceAttribute(): bool
    {
        return $this->total <= $this->current_wallet_balance_available_for_purchases;
    }

    public function getHasEnoughToPayFromOrderFundingAttribute(): bool
    {
        return $this->total <= $this->total_installments;
    }
    // BOOLEANS END

    // CALCULATIONS START

    public function getShippingCostAttribute(): float
    {
        return $this->requires_waybill && $this->has_waybill ? $this->waybill->cost : 0;
    }

    public function getHomeDeliveryCostAttribute(): float
    {
        return $this->has_home_delivery ? $this->homeDelivery->cost : 0;
    }

    public function getWaybillCostAttribute(): float
    {
        return $this->waybill ? $this->waybill->cost : 0;
    }

    public function getTotalInstallmentsAttribute(): float
    {
        return array_sum($this->successfulInstallments->map(function (WalletFunding $walletFunding) {
            return $walletFunding->amount;
        })->all());
    }

    /**
     * Since the buyer's current wallet balance does not include fundings for uncancelled orders,
     * This attribute sums up the buyer's wallet balance and the sum of all successful installments
     * for this order. This makes sure the funds for a particular order is locked for that particular order
     * while leaving room to use available wallet fundings that are not associated with any order.
     */
    public function getCurrentWalletBalanceAvailableForPurchasesAttribute(): float
    {
        return $this->buyer->current_useable_wallet_balance + $this->total_installments;
    }
    // CALCULATIONS END

    // ACTIONS START
    public function initiateWaybill(): Model | false
    {
        if (is_null($this->waybill)) {
            return $this->waybill()->create([
                'cost' => $this->default_waybill_cost,
                // 'destination_town_id' => $this->delivery_town_id
            ]);
        }
        return false;
    }

    public function initiateHomeDelivery(): Model | false
    {
        if ($this->home_delivery) {
            if (is_null($this->homeDelivery)) {
                return $this->homeDelivery()->create([
                    'cost' => $this->default_home_delivery_cost,
                    'origin_address' => ''
                ]);
            }
            return false;
        }
        return false;
    }

    public function disableHomeDelivery(): bool
    {
        if ($this->home_delivery) {
            if (!is_null($this->homeDelivery)) {
                if ($this->homeDelivery->has_left)
                    return false;
                else {
                    $this->homeDelivery()->delete();
                    return $this->update([
                        'home_delivery' => 0
                    ]);
                }
            }
            return false;
        }
        return false;
    }

    public function getDefaultWaybillCostAttribute(): float
    {
        return RangeType::firstWhere('key', 'WAYBILL_COST')->getValue($this->cart_total);
    }

    public function getDefaultHomeDeliveryCostAttribute(): float
    {
        return RangeType::firstWhere('key', 'HOME_DELIVERY_COST')->getValue($this->cart_total);
    }

    public function makeWaybill()
    {
        if (is_null($this->waybill)) {
            return $this->waybill()->create([
                'cost' => $this->default_waybill_cost
            ]);
        }
        return false;
    }
    // ACTIONS END
}
