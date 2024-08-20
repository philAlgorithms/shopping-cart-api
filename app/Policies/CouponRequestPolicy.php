<?php

namespace App\Policies;

use App\Models\CouponRequest;
use App\Models\User;
use App\Models\Users\Admin;
use App\Models\Users\Buyer;
use App\Models\Users\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class CouponRequestPolicy
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
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, CouponRequest $couponRequest)
    {
        if ($user instanceof Admin) {
            return true;
        } else if ($user instanceof Buyer) {
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
     * Determine whether the user can request for a coupon.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Authenticatable $user)
    {
        return $user instanceof Buyer;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, CouponRequest $couponRequest)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, CouponRequest $couponRequest)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, CouponRequest $couponRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CouponRequest  $couponRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, CouponRequest $couponRequest)
    {
        //
    }

    /**
     * Determine whether the user can request for a coupon.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function grant(Authenticatable $user, CouponRequest $request)
    {
        return $user instanceof Admin;
    }
}
