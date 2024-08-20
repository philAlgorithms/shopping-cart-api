<?php

namespace App\Policies;

use App\Models\BuyerReferralProgram;
use App\Models\User;
use App\Models\Users\{Admin, Buyer};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class BuyerReferralProgramPolicy
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
     * @param  \App\Models\BuyerReferralProgram  $buyerReferralProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Authenticatable $user, BuyerReferralProgram $buyerReferralProgram)
    {
        if($user instanceof Admin)
        {
            return true;
        }else if($user instanceof Buyer)
        {
            return $buyerReferralProgram->buyer->id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can request for a referral code.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  float  $amount
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Authenticatable $user)
    {
        if (!($user instanceof Buyer)) return false;

        if(is_null($user->referralProgram))
        {
            return true;
        }else {
            return $user->referralProgram->is_deactived ?
                Response::deny('Your referral code was deactivated. Contact support.') :
                Response::deny('Your referral code has already been activated.');
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BuyerReferralProgram  $buyerReferralProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, BuyerReferralProgram $buyerReferralProgram)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BuyerReferralProgram  $buyerReferralProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, BuyerReferralProgram $buyerReferralProgram)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BuyerReferralProgram  $buyerReferralProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, BuyerReferralProgram $buyerReferralProgram)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\BuyerReferralProgram  $buyerReferralProgram
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, BuyerReferralProgram $buyerReferralProgram)
    {
        //
    }

    /**
     * Determine whether the user can decline a payout.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\BuyerReferralProgram  $program
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function activate(Authenticatable $user, BuyerReferralProgram $program)
    {
        if (!($user instanceof Admin)) return false;

        if ($program->is_activated) {
            return Response::deny("This request has already been activated");
        } else if ($program->is_deactivated) {
            return Response::deny("This request has already been deactivated.");
        }

        return true;
    }

    /**
     * Determine whether the user can decline a payout.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\BuyerReferralProgram  $program
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function referRegistrant(Authenticatable $user, BuyerReferralProgram $program)
    {
        if (!($user instanceof Admin)) return false;

        if ($program->is_activated) {
            return Response::deny("This request has already been activated");
        } else if ($program->is_deactivated) {
            return Response::deny("This request has already been deactivated.");
        }

        return true;
    }
    
}
