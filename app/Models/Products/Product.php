<?php

namespace App\Models\Products;

use App\Models\{Cart, Tag};
use App\Models\Media\MediaFile;
use App\Models\Order\Order;
use App\Models\Ratings\Rating;
use App\Models\Specifications\Specification;
use App\Models\Stores\Store;
use App\Models\Users\{Buyer};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Gets the sub category of this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(ProductSubCategory::class, 'product_sub_category_id');
    }

    /**
     * Gets the category of this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->subCategory->category();
    }

    /**
     * Gets the cover image of this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class, 'cover_image_id');
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->coverImage->temporary_url ?? '';
    }

    /**
     * Gets the images associated with this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function images(): MorphToMany
    {
        return $this->morphToMany(MediaFile::class, 'media_fileable');
    }

    public function discount(): HasOne
    {
        return $this->hasOne(Discount::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'product_id');
    }

    public function allDiscounts(): HasMany
    {
        return $this->hasMany(Discount::class);
    }

    public function getHasDiscountAttribute(): bool
    {
        return !is_null($this->discount);
    }

    public function addDiscount(float $percentage)
    {
        if ($percentage > 99) {
            throw ValidationException::withMessages([
                'discount' => 'Percentage must not be greater than 99%'
            ]);
        }
        if ($percentage <= 0) {
            throw ValidationException::withMessages([
                'discount' => 'Percentage must not be less than 0%'
            ]);
        }

        foreach ($this->allDiscounts as $discount) {
            $discount->delete();
        }
        $discount = $this->allDiscounts()->create([
            'percentage' => $percentage
        ]);

        return $this->refresh();
    }

    public function getDiscountPriceAttribute(): float
    {
        return $this->has_discount ?
            $this->price - percentage_fraction($this->price, $this->discount->percentage) :
            0.00;
    }

    /**
     * Gets the brand of this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Gets the store this product belongs to
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Gets the tags associated with this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Gets the specifications of this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function specifications(): BelongsToMany
    {
        return $this->belongsToMany(Specification::class, 'product_specifications', 'product_id');
    }

    /**
     * Gets the reviews associated with this product
     * 
     * @return Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    /**
     * Add to cart
     */
    public function addToCart(int $quantity, Order|null $order=null)
    {
        $buyer = auth()->user() instanceof Buyer ? Buyer::find(auth()->user()->id) : null;
        $buyer_exists = !is_null($buyer);
        $quantity = abs($quantity);
        if ($this->quantity < $quantity) {
            throw ValidationException::withMessages([
                'cart' => 'Not enough items in stock'
            ]);
        }

        if (is_null(session('cart'))) session(['cart' => collect([])]);
        $cart = $buyer_exists ?
            session('cart') // $buyer->activeCart()
            : session('cart');
        $item = $cart->firstWhere('product_id', $this->id);

        if (is_null($item)) {
            $cart_item = new Cart([
                'product_id' => $this->id,
                'quantity' => $quantity,
                'order_id' => $order->id ?? null
            ]);
            !is_null($order) ? $cart_item->save() :
                session()->push('cart', new Cart([
                    'product_id' => $this->id,
                    'quantity' => $quantity
                ]));
        } else {
            if ($this->quantity < $item->quantity + $quantity) {
                throw ValidationException::withMessages([
                    'cart' => 'Not enough items in stock'
                ]);
            }

            $update_object = [
                'quantity' => $item->quantity + $quantity
            ];

            $buyer_exists ?
                $item->update($update_object) :
                $item->fill($update_object);
        }

        return session('cart');
    }

    public function removeFromCart(int $quantity)
    {
        $buyer = auth()->user() instanceof Buyer ? Buyer::find(auth()->user()->id) : null;
        $buyer_exists = !is_null($buyer);
        $quantity = abs($quantity);
        $cart = $buyer_exists ? $buyer->activeCart : session('cart');

        if (is_null($cart) && !$buyer_exists) {
            session(['cart' => collect([])]); //intialise cart and exit
            $cart = session('cart');

            throw ValidationException::withMessages([
                'cart' => 'Cart is already empty'
            ]);
        } else if ($cart instanceof Collection) { // Continue coding from here
            if ($cart->count() === 0) {
                throw ValidationException::withMessages([
                    'cart' => 'Cart is already empty'
                ]);
            }

            $item = $cart->firstWhere('product_id', $this->id);

            if ($quantity > $item->quantity) {
                throw ValidationException::withMessages([
                    'cart' => 'Cannot reduce item quantity by this amount'
                ]);
            }
            $item->fill([
                'quantity' => $item->quantity - $quantity
            ]);

            return session('cart');
        }
    }

    public function getHasBeenPurchasedAttribute()
    {
        return false;
    }
}
