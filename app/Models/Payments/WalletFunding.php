<?php

namespace App\Models\Payments;

use App\Mail\Order\Payment\Installment\InstallmentPaymentCompleted;
use App\Mail\Order\Payment\Installment\InstallmentPaymentReceived;
use App\Models\Currency;
use App\Models\Order\Order;
use App\Models\Users\Buyer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

use function Illuminate\Events\queueable;

class WalletFunding extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(queueable(function (WalletFunding $walletFunding) {
            if (is_null($walletFunding->order)) {
                // Handle pure wallet funding

                // Send mail buyer that account has been funded, cc to admin
            } else {
                // Handle installment payment funding
                $order = $walletFunding->order;

                // Update and lock price of product.
                foreach ($order->cart as $item) {
                    if (is_null($item->price)) {
                        // Please check if this is the price paid per installment
                        $item->update([
                            'price' => $item->product->price
                        ]);
                    }
                }

                // Automatically pay for the order if enough funds is available from installments
                if ($order->has_enough_to_pay_from_order_funding) {
                    $order->payFromWallet();

                    // Send mail to buyer that payment has been made from wallet, cc to admins
                    Mail::to($walletFunding->buyer->email)->send(new InstallmentPaymentCompleted($walletFunding));
                }
                // Send mail to buyer that installment payment has been received, cc to admins
                Mail::to($walletFunding->buyer->email)->send(new InstallmentPaymentReceived($walletFunding));
            }
        }));
    }

    public function paystackPayment(): BelongsTo
    {
        return $this->belongsTo(PaystackPayment::class, 'paystack_payment_id');
    }

    public function HasPaymentIsSuccessfulAttributes(): bool
    {
        return $this->paystackPayment->payload_is_successful;
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class, 'buyer_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }
}
