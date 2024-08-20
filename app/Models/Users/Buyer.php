<?php

namespace App\Models\Users;

use App\Models\{BuyerReferralProgram, Cart, Currency, User};
use App\Models\Order\{Order};
use App\Models\Payments\{WalletFunding, WalletPurchase};
use App\Notifications\Registration\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\{Builder, Collection};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, HasManyThrough, HasOne};
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Unicodeveloper\Paystack\{Paystack, TransRef};

class Buyer extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasApiTokens, HasRoles, Notifiable;

    protected $guard_name = 'buyer';

    // Method to send email verification
    public function sendEmailVerificationNotification()
    {
        // We override the default notification and will use our own
        $this->notify(new VerifyEmailNotification());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referralProgram(): HasOne
    {
        return $this->hasOne(BuyerReferralProgram::class, 'buyer_id');
    }

    public function getIsPartOfReferralProgramAttribute(): bool
    {
        return is_null($this->referralProgram) ? false : ($this->referralProgram->is_active ? true : false);
    }

    public function getSessionCartAttribute()
    {
        is_null(session('cart')) ? session(['cart' => collect([])]) : session('cart');
        $cart = session('cart');

        return $cart;
    }

    public function cart(): HasMany
    {
        return $this->hasMany(Cart::class, 'buyer_id');
    }

    public function activeCart(): HasMany
    {
        return $this->cart()->whereNull('order_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function walletFundings(): HasMany
    {
        return $this->hasMany(WalletFunding::class, 'buyer_id');
    }

    public function walletPurchases(): HasManyThrough
    {
        return $this->hasManyThrough(WalletPurchase::class, Order::class);
    }

    public function successfulWalletFundings(): HasMany
    {
        return $this->walletFundings()->whereHas('paystackPayment', fn ($builder) => $builder->whereJsonContains('payload', ['status' => 'success']));
    }

    public function installments(): HasMany
    {
        return $this->successfulWalletFundings()->whereNotNull('order_id');
    }

    public function regularWalletFundings(): HasMany
    {
        return $this->successfulWalletFundings()->whereNull('order_id');
    }

    public function installmentOrders(): HasMany
    {
        return $this->orders()->whereHas('installmentPayments');
    }

    public function getFullyPaidInstallmentOrdersAttribute(): Collection
    {
        return $this->installmentOrders->filter(function (Order $order) {
            return $order->has_paid_full_installments;
        });
    }

    /**
     * Depicts the wallet funds which can be used for purchases 
     * (not taking into acoount the buyer's wallet purchases account).
     * Nevertheless, this takes into account wallent fundings for specific orders;
     * Hence one can pay for an order from the wallet if the total amount calculated
     * using this method plus the total installment for the order is greater than or
     * equal to the order total (while taking into account the totall wallet purchases, that is).
     */
    public function allTimeUseableWalletFundings(): HasMany
    {
        return $this->regularWalletFundings()->orWhereHas('order', function (Builder $builder) {
            $builder->whereKey($this->getFullyPaidInstallmentOrdersAttribute()->map(fn (Order $order) => $order->id)->all())
                ->whereNotNull('cancelled_at');
        });
    }

    public function hasOrder(Order $order): bool
    {
        return $this->orders->contains('id', $order->id);
    }

    /**
     * Funds a buyer's wallet while making sure order has not been paid for and if the buyer
     * actually owns the said order
     */
    public function fundWallet(float $amount, Order|null $order = null, string|null $callback_url = null)
    {
        if (!is_null($order)) {
            if(! $this->hasOrder($order))
            {
                throw ValidationException::withMessages([
                    'order' => 'This action is not allowed.'
                ]);
            }
            if ($order->has_paid_full) {
                throw ValidationException::withMessages([
                    'order' => 'Order has already been paid for.'
                ]);
            }
        }
        $user = $this->user;
        $ref = TransRef::getHashedToken();

        try {
            $paystack = (new Paystack);
            $data = [
                "amount" => ceil($amount * 100),
                "reference" => $ref,
                "email" => $this->email,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "currency" => "NGN",
                "metadata" => [
                    "payable_type" => WalletFunding::class,
                    "payable_load" => [
                        'buyer_id' => $this->id,
                        'order_id' => $order->id ?? null,
                        'currency_id' => Currency::firstWhere('acronym', 'NGN')->id
                    ]
                ]
            ];
            if (! is_null($callback_url))
                $data['callback_url'] = $callback_url;

            return $paystack->getAuthorizationUrl($data)->redirectNow();
        } catch (\Exception $e) {
            throw $e;
            throw ValidationException::withMessages([
                'paystack' => 'Some error occured. Please refresh the page and try again.',
                'amount' => $amount
            ]);
        }
    }

    // CALCULATIONS START

    /**
     * Sum of all amounts that has benn successfully deposited into the buyer's wallet
     */
    public function getAllTimeWalletBalanceAttribute(): float
    {
        return array_sum($this->successfulWalletFundings->pluckToArray('amount'));
    }

    /**
     * Sum of buyer's all-time wallet fundings minus uncancelled orders fundings
     */
    public function getAllTimeUseableWalletBalanceAttribute(): float
    {
        return array_sum(
            $this->allTimeUseableWalletFundings->map(fn ($f) => $f->amount)->all()
        );
    }

    /**
     * Sum of all purchases made using the buyer's wallet
     */
    public function getAllTimeTotalWalletPurchaseAttribute(): float
    {
        return array_sum($this->walletPurchases->pluckToArray('amount'));
    }

    /**
     * Sum of all purchases made using the buyer's wallet
     */
    public function getCurrentUseableWalletBalanceAttribute(): float
    {
        return $this->all_time_useable_wallet_balance;
        // - $this->all_time_total_wallet_purchase; Not so so why I addeed this.
    }

    /**
     * Sum of all purchases made using the buyer's wallet
     */
    public function getCurrentWalletBalanceAttribute(): float
    {
        return $this->all_time_wallet_balance - $this->all_time_total_wallet_purchase;
    }
    // CALCULATIONS END
}
