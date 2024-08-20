<?php

namespace App\Models\Stores;

use App\Models\Cart;
use App\Models\Media\MediaFile;
use App\Models\Order\Order;
use App\Models\Products\Product;
use App\Models\StorePayout;
use App\Models\Users\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough};

class Store extends Model
{
    use HasFactory;

    /**
     * Gets the vendor of this store
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Gets the logo of this store
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'logo_id');
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo->temporary_url ?? '';
    }

    /**
     * Gets the CAC registration document of this store
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cac(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'logo_id');
    }

    public function getCacUrlAttribute(): string
    {
        return $this->cac->temporary_url ?? '';
    }

    /**
     * Gets the products in this store
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Checks if a task is complted
     */
    public function getIsVerifiedAttribute(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Checks if this project can be marked as Verified
     */
    public function getIsVerifiableAttribute(): bool
    {
        return $this->is_approved && !$this->is_verified;
    }

    /**
     * Verify store and allow for transactions
     */
    public function markAsVerified(): bool
    {
        return $this->is_verifiable ?
            $this->update([
                'verified_at' => now()
            ]) :
            false;
    }

    /**
     * Check if all product ids contained in an array belongs to this store
     */
    public function hasProducts(array $productArrays)
    {
        return $this->products->only($productArrays)->count() === count(array_unique($productArrays));
    }
    
    public function hasProduct(Product|int $product)
    {
        $productObject = is_numeric($product) ? Product::find($product) : $product;
        return is_null($product) ? false : ($productObject->store_id === $this->id);
    }

    public function carts(): HasManyThrough
    {
        return $this->hasManyThrough(Cart::class, Product::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(StorePayout::class, 'store_id');
    }

    public function pendingPayouts(): HasMany
    {
        return $this->payouts()->whereNull('declined_at')->whereNull('approved_at');
    }

    public function approvedPayouts(): HasMany
    {
        return $this->payouts()->whereNull('declined_at')->whereNotNull('approved_at');
    }

    public function declinedPayouts(): HasMany
    {
        return $this->payouts()->whereNotNull('approved_at')->whereNull('declined_at');
    }

    public function orders()
    {
        return Order::query()->wherein('id', $this->carts->pluckToArray('order_id'));
    }

    public function fullyPaidOrdersCollection()
    {
        return $this->orders()->get()->filter(function (Order $order) {
            return $order->has_paid_full;
        });
    }

    public function fullyPaidCarts(): HasManyThrough
    {
        return $this->carts()->wherein('order_id', $this->fullyPaidOrdersCollection()->map(fn ($f) => $f->id)->all());
    }

    // START FINANCE
    public function getTotalProductPurchaseAttribute()
    {
        return array_sum($this->fullyPaidCarts->map(fn (Cart $cart) => $cart->price * $cart->quantity)->all());
    }

    public function getTotalApprovedPayoutsAttribute()
    {
        return array_sum($this->approvedPayouts->map(fn (StorePayout $storePayout) => $storePayout->amount)->all());
    }

    public function getAvailableBalanceAttribute()
    {
        return $this->total_product_purchase - $this->total_approved_payouts;
    }
    // END FINANCE
}
