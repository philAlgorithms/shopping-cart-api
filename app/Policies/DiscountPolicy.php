<?php

namespace App\Policies;

use App\Models\Products\Discount;
use App\Models\User;
use App\Models\Users\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class DiscountPolicy
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
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Authenticatable $user, Discount $discount)
    {
        $is_vendor = $user instanceof Vendor && ! is_null($user->store);
        $is_owner = $discount->product->store->vendor->id === $user->id;

        return isAdmin($user) || ($is_vendor && $is_owner);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Authenticatable $user)
    {
        return $user instanceof Vendor && ! is_null($user->store);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  \App\Models\Products\Discount  $discount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Authenticatable $user, Discount $discount): Response | bool
    {
        $is_vendor = $user instanceof Vendor && ! is_null($user->store);
        $is_owner = $discount->product->store->vendor->id === $user->id;

        if($is_vendor && $is_owner)
        {
            if($discount->product->has_been_purchased)
                return Response::deny(
                    'Cannot update the discount of a product that has already been bought. Try creating another discount.'
                );

            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable $user
     * @param  \App\Models\Products\Discount  $discount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, Discount $discount)
    {
        return $this->update($user, $discount);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Products\Discount  $discount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Discount $discount)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Products\Discount  $discount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Discount $discount)
    {
        //
    }
}
