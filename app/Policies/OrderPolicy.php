<?php

namespace App\Policies;

use App\Models\Order\Coupon;
use App\Models\Order\Order;
use App\Models\User;
use App\Models\Users\{Admin, Buyer, Vendor};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

class OrderPolicy
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
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Authenticatable $user, Order $order)
    {
        return $user instanceof Admin ? true : (
            $user instanceof Buyer ? $user->orders->contains('id', $order->id) : ($user instanceof Vendor && $user->has_store ? $user->store->orders->contains('id', $order->id) : false)
        );
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
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Order $order)
    {
        //
    }

    /**
     * Determine whether the user can pay for an order.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, Order $order)
    {
        $is_owner = $user instanceof Buyer && $user->hasOrder($order);
        if ($is_owner) {
            return $order->has_paid_at_all ? Response::deny('A payment has already been made for this order.') : true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Order $order)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Order $order)
    {
        //
    }

    /**
     * Determine whether the user can checkout.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function checkout(Authenticatable $user, ?Coupon $coupon = null)
    {
        if (is_null(session('cart')) || !(session('cart') instanceof Collection)) session(['cart' => collect([])]);
        $cart = session('cart');
        if ($cart instanceof Collection) {
            if ($cart->count() > 0) {
                if($user instanceof Buyer)
                {
                    if(is_null($coupon))
                    {
                        return true;
                    }
                    if(! $coupon->is_active || $coupon->expired)
                    {
                        return Response::deny('Inactive/Expired Coupon code.');
                    }
                    return $coupon->applicableToCart($cart) ? true : Response::deny('Coupon code does not cover any item in cart.');
                }
                else return false;
            } else
                return Response::deny('Cart is empty');
        }
        return Response::deny('Cart is empty');
    }

    /**
     * Determine whether the user can pay for an order.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function pay(Authenticatable $user, Order $order)
    {
        $is_owner = $user instanceof Buyer && $user->hasOrder($order);
        if ($is_owner) {
            return $order->has_paid_full ? Response::deny('This order payment has already been completed') : true;
        }

        return false;
    }

    /**
     * Determine whether the user update shipping information.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateShipping(Authenticatable $user, Order $order)
    {
        return $this->pay($user, $order);
    }

    /**
     * Determine whether the user can pay for an order using funds in the wallet.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function payFromWallet(Authenticatable $user, Order $order)
    {
        $can_pay = $this->pay($user, $order);
        if ($can_pay) {
            // Check if installment payments have been intialized
            if ($order->installmentPayments()->count() > 0) {
                return Response::deny('Payments for this order can only be made in installments');
            }
        }

        return $can_pay;
    }

    /**
     * Determine whether the user can check the stat.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Order  $order
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function verifyTransaction(Authenticatable $user, Order $order)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can initiate home delivery.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function initiateWaybill(Authenticatable $user, Order $order)
    {
        if ($user instanceof Admin) {
            return is_null($order->waybill) ? true : Response::deny('Waybill has already been initiated');
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can initiate home delivery.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function initiateHomeDelivery(Authenticatable $user, Order $order)
    {
        if ($user instanceof Buyer && $user->hasOrder($order)) {
            if ($order->home_delievry && $user->hasOrder($order)) {
                return is_null($order->homeDelivery) ? true : Response::deny('Home delivery has already been initiated');
            } else Response::deny('Home delivery is not enabled for this order.');
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can disable home delivery.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function disableHomeDelivery(Authenticatable $user, Order $order)
    {
        if ($user instanceof Buyer && $user->hasOrder($order)) {
            if (!$order->home_delievry || !is_null($order->homeDelivery)) {
                if ($order->homeDelivery && $order->homeDelivery->has_left) {
                    return Response::deny('Home delivery has already started.');
                }
                return true;
            } else
                return Response::deny('Home delivery is already disabled for this order.');
        } else {
            return false;
        }
    }
}
