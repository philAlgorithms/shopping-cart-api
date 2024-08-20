<?php

namespace App\Models\Payments;

use App\Handler\Paystack\Paystack;
use App\Models\Currency;
use App\Models\Order\Order;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphOne};
use Illuminate\Validation\ValidationException;
use Unicodeveloper\Paystack\Paystack as UnicodeveloperPaystack;

use function Illuminate\Events\queueable;

class PaystackPurchase extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(queueable(function (PaystackPurchase $paystackPurchase) {
            foreach($paystackPurchase->order->cart as $item)
            {
                $item->update([
                    'price' => $item->product->price
                ]);
            }
            if (is_null($paystackPurchase->purchase)) {
                $paystackPurchase->purchase()->create();
            }
            // Lock order
            $paystackPurchase->order->lock();
            $paystackPurchase->order->autoInitiateWaybill();
            
            // Send mail to admin
        }));
    }

    /**
     * Get the purchase.
     */
    public function purchase(): MorphOne
    {
        return $this->morphOne(Purchase::class, 'purchasable');
    }

    public function paystackPayment(): BelongsTo
    {
        return $this->belongsTo(PaystackPayment::class, 'currency_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function verifyTransaction()
    {
        return (new Paystack)->verifyTransaction($this->paystackPayment->reference);
    }

    public function getPaymentDataAttribute(): array
    {
        $user = $this->order->buyer->user;
        return [
            "amount" => $this->amount * 100,
            "reference" => $this->reference,
            "email" => $this->order->buyer->email,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            // "callback_url" => request()->callback_url,
            "currency" => "NGN",
        ];
    }

    public function getPaymentStatusAttribute(): string | false
    {
        try {
            $tx = $this->verifyTransaction();
            return $tx['data']['status'];
        } catch (ClientException $e) {
            return false;
        }
    }

    public function getHasPaidAttribute(): bool
    {
        return $this->payment_status == 'success';
    }

    public function payWithFreshData()
    {
        if ($this->has_paid) {
            return throw ValidationException::withMessages(['paystack' => 'This installment has already been paid for.']);
        }
        $paystack = new UnicodeveloperPaystack;
        $data = $this->payment_data;
        $newRef = $paystack->genTranxRef();

        $this->update(['reference' => $newRef]);
        $this->paystackPayment()->update(['reference' => $newRef]);
        $data['reference'] = $newRef;

        $paystack->getAuthorizationUrl($data);
        $this->paystackPayment()->update(['authorization_url' => $paystack->url]);
        return $paystack->redirectNow();
    }

    public function pay()
    {
        $paystack = new UnicodeveloperPaystack;
        $data = $this->payment_data;
        $status = $this->payment_status;

        if ($status == 'success') {
            return throw ValidationException::withMessages(['paystack' => 'This installment has already been paid for.']);
        } else if ($status == 'abandoned') {
            // return response($this->verifyTransaction());
            return $this->payWithFreshData();
        }
        try {
            return $paystack->getAuthorizationUrl($data)->redirectNow();
        } catch (ClientException $e) {
            $response = (array)json_decode($e->getResponse()->getBody()->getContents());

            if ($response['message'] == 'Duplicate Transaction Reference') {
                return redirect("https://checkout.paystack.com/" . $this->paystackPayment->authorization_url);
            } else if ($response['message'] == 'Transaction reference not found') {
                return $this->payWithFreshData();
            }
            throw $e;
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['paystack' => 'Some error occured. Please try again.']);
        }
        // return redirect("https://checkout.paystack.com/{$this->reference}");
    }
}
