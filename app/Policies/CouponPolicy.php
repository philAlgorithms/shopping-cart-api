<?php

namespace App\Policies;

use App\Models\Order\Coupon;
use App\Models\Products\Product;
use App\Models\User;
use App\Models\Users\{Admin, Vendor};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class CouponPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Authenticatable $user)
    {
        return ($user instanceof Admin) || ($user instanceof Vendor);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Coupon  $coupon
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Authenticatable $user, Coupon $coupon)
    {
        if($user instanceof Admin)
        {
            return true;
        }else if($user instanceof Vendor && $coupon->belongs_to_a_store)
        {
            return $user->store->id ?? '' === $coupon->store_id;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Authenticatable $user, array $productArray)
    {
        if ($user instanceof Admin) {
            return true;
        } else if ($user instanceof Vendor) {
            return $user->has_store ?
                ($user->store->hasProducts($productArray) ?
                    true :
                    Response::deny('Some of the products are not owned by you.')
                ) : false;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Coupon  $coupon
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Authenticatable $user, Coupon $coupon)
    {
        if($user instanceof Admin)
        {
            return $coupon->belongs_to_a_store ? Response::deny('Coupon can only be modified by the store owner.') : true;
        }else if($user instanceof Vendor && $coupon->belongs_to_a_store)
        {
            return $user->store->id === $coupon->store_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Order\Coupon  $coupon
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, Coupon $coupon)
    {
        return $this->update($user, $coupon);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Coupon  $coupon
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Coupon $coupon)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Order\Coupon  $coupon
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Coupon $coupon)
    {
        //
    }
}
