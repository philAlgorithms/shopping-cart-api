<?php

namespace App\Models\Order;

use App\Models\Products\Product;
use App\Models\Stores\Store;
use App\Models\Users\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, HasOne};
use Illuminate\Support\Collection;

class Coupon extends Model
{
    use HasFactory;

    /**
     * Gets the products associated with this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_coupons');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getBelongsToAStoreAttribute(): bool
    {
        return !is_null($this->store_id);
    }

    public function getExpiredAttribute(): bool
    {
        return !is_null($this->expired_at) && $this->expired_at < now();
    }

    public function applicableToCart(Collection $cart): bool
    {
        if (!$this->is_active || $this->expired) {
            return false;
        }
        if ($this->belongs_to_a_store) {
            $store = $this->store;

            if ($this->products()->count() > 0) {
                foreach ($this->products as $product) {
                    if ($store->hasProduct($product)) return true;
                }
            } else {
                foreach ($cart as $item) {
                    if ($store->hasProduct($item->product_id)) return true;
                }
                return false;
            }
        }
        return true;
    }

    public function orderItems(Order $order)
    {
        if ($order->coupon_id !== $this->id) {
            // Return empty cart if coupon does not match
            return $order->cart()->whereNull('id');
        }
        if ($this->products()->count() > 0) {
            return $order->cart()->whereHas(
                'product',
                fn ($builder) => $builder->wherein('id', $this->products->pluckToArray('id'))
            );
        }
        if ($this->belongs_to_a_store) {
            return $order->cart()->whereHas(
                'product',
                fn ($builder) => $builder->where('store_id', $this->store_id)
            );
        }
        return $order->cart();
    }

    public function vendorOrderItems(Order $order, Vendor $vendor)
    {
        $emptyCart = $order->cart()->whereNull('id');
        if (! $vendor->has_store || is_null($order->coupon) || $order->coupon_id !== $this->id || $order->coupon->store_id !== $vendor->store->id) {
            // Return empty cart if coupon does not match
            return $emptyCart;
        }
        if ($this->products()->count() > 0) {
            $productArray = $this->products->pluckToArray('id');
            return $vendor->store->hasProducts($productArray)
                ?  $order->cart()->whereHas(
                    'product',
                    fn ($builder) => $builder->wherein('id', $productArray)
                ):$emptyCart ;
        }
        return $order->vendorCart($vendor);
    }
}
