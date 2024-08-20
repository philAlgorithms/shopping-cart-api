<?php

namespace App\Policies;

use App\Models\BuyerReferral;
use App\Models\User;
use App\Models\Users\{Admin, Buyer};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class BuyerReferralPolicy
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
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Authenticatable $user, BuyerReferral $buyerReferral)
    {
        if($user instanceof Admin)
        {
            return true;
        }else if($user instanceof Buyer)
        {
            return $buyerReferral->program->buyer->id === $user->id;
        }
        return false;
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
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, BuyerReferral $buyerReferral)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, BuyerReferral $buyerReferral)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, BuyerReferral $buyerReferral)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BuyerReferral  $buyerReferral
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, BuyerReferral $buyerReferral)
    {
        //
    }
}
