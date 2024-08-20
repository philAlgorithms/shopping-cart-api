<?php

namespace App\Models;

use App\Models\Order\{Order};
use App\Models\Products\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo};

class Cart extends Model
{
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Cart price is the product price if order has not been locked else the cart price must have been updated
     * upon locking of the order using the price of the product at that instance.
     */
    public function getTotalAttribute(): float
    {
        $price = is_null($this->order) ? $this->product->price : (($this->order->is_locked && ! is_null($this->price)) ? $this->price : $this->product->price);
        return $this->quantity * $price;
    }
}
