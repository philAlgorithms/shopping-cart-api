<?php

namespace App\Models\Payments;

use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphOne};

use function Illuminate\Events\queueable;

class WalletPurchase extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(queueable(function (WalletPurchase $walletPurchase) {
            if (is_null($walletPurchase->purchase)) $walletPurchase->purchase()->create();
            // Lock order
            $walletPurchase->order->lock();
        }));
    }

    /**
     * Get the purchase.
     */
    public function purchase(): MorphOne
    {
        return $this->morphOne(Purchase::class, 'purchasable');
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }

    public function pay()
    {
        // Do something reasonable, Philip
    }
}
