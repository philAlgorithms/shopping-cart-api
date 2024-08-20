<?php

namespace App\Policies;

use App\Models\Payments\PaystackPurchase;
use App\Models\User;
use App\Models\Users\Buyer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class PaystackPurchasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PaystackPurchase $paystackPurchase)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PaystackPurchase $paystackPurchase)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PaystackPurchase $paystackPurchase)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, PaystackPurchase $paystackPurchase)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, PaystackPurchase $paystackPurchase)
    {
        //
    }

    /**
     * Determine whether the user can pay for an order.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Payments\PaystackPurchase  $paystackPurchase
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function pay(Authenticatable $user, PaystackPurchase $paystackPurchase)
    {
        $is_owner = $user instanceof Buyer && $user->hasOrder($paystackPurchase->order);
        if ($is_owner) {
            return true;
        }
        return $paystackPurchase->order->has_paid_full ? Response::deny('This order payment has already been completed') : true;
    }
}
