<?php

namespace App\Models\Payments;

use App\Handler\Paystack\Paystack;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use function Illuminate\Events\queueable;

class PaystackPayment extends Model
{
    use HasFactory;

    protected $casts = [
        'payload' => 'json',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::updated(queueable(function (PaystackPayment $paystackPayment) {
            $payloadWasChanged = $paystackPayment->payload_was_changed;
            $payloadExists = !is_null($paystackPayment->payload);
            $payloadIsSuccessful = $payloadExists && array_key_exists('status', $paystackPayment->payload) && $paystackPayment->payload['status'] === "success";
            if ($payloadWasChanged && $payloadIsSuccessful) {
                if (! is_null($paystackPayment->walletFunding)) {
                    $paystackPayment->walletFunding()->update([
                        'amount' => $paystackPayment->payload['amount'] / 100
                    ]);
                } else if (! is_null($paystackPayment->paystackPurchase)) {
                    $paystackPayment->paystackPurchase()->update([
                        'amount' => $paystackPayment->payload['amount'] / 100
                    ]);
                }
            }
        }));
    }


    public function walletFunding(): HasOne
    {
        return $this->hasOne(WalletFunding::class, 'paystack_payment_id');
    }


    public function paystackPurchase(): HasOne
    {
        return $this->hasOne(PaystackPurchase::class, 'paystack_payment_id');
    }

    public function handleCallback(array $data, bool $isWebhook)
    {
        return $this->update([
            'payload' => $data
        ]);
    }

    public function getPayloadWasChangedAttribute(): bool
    {
        return $this->wasChanged('payload');
    }

    public function getPayloadExistsAttribute(): bool
    {
        return !is_null($this->payload);
    }
    public function getPayloadIsSuccessfulAttribute(): bool
    {
        return $this->payload_exists ? array_key_exists('status', $this->payload) && $this->payload['status'] === "success" : false;
    }

    public function verifyTransaction()
    {
        return $this->payload_exists ? null : (new Paystack)->verifyTransaction($this->payload['reference']);
    }
}
