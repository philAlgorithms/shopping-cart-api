<?php

namespace App\Policies;

use App\Models\StorePayout;
use App\Models\User;
use App\Models\Users\{Admin, Vendor};
use Illuminate\Auth\Access\{HandlesAuthorization, Response};
use Illuminate\Contracts\Auth\Authenticatable;

class StorePayoutPolicy
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
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, StorePayout $storePayout)
    {
        //
    }

    /**
     * Determine whether the user can request for a payout.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  float  $amount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Authenticatable $user, float $amount)
    {
        if (!($user instanceof Vendor)) return false;

        $store = $user->store;
        if ($store->pendingPayouts()->count() > 0) {
            return Response::deny("You still have a pending payout request");
        } else {
            return $amount > $store->available_balance ? Response::deny("Insufficient balance") : true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, StorePayout $storePayout)
    {
        //
    }

    /**
     * Determine whether the user can delete a payout.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, StorePayout $storePayout)
    {
        return $this->decline($user, $storePayout);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, StorePayout $storePayout)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, StorePayout $storePayout)
    {
        //
    }

    /**
     * Determine whether the user can approve a payout.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function approve(Authenticatable $user, StorePayout $storePayout)
    {
        if (!($user instanceof Admin)) return false;

        $paydays = ['Saturday'];
        if (in_array(date('l', strtotime(now())), $paydays)) {
            if ($storePayout->is_approved) {
                return Response::deny("This request has already been approved");
            } else if ($storePayout->is_declined) {
                return Response::deny("This request has already been declined.");
            }

            $store = $storePayout->store;

            return $storePayout->amount > $store->available_balance ? Response::deny("Insufficient balance") : true;
        } else {
            return Response::deny("Payouts can only be made on these days: " . implode(',', $paydays));
        }
    }

    /**
     * Determine whether the user can decline a payout.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\StorePayout  $storePayout
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function decline(Authenticatable $user, StorePayout $storePayout)
    {
        if (!($user instanceof Admin)) return false;

        if ($storePayout->is_approved) {
            return Response::deny("This request has already been approved");
        } else if ($storePayout->is_declined) {
            return Response::deny("This request has already been declined.");
        }

        return true;
    }
}
