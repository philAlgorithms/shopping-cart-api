<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Users\Buyer;
use Illuminate\Auth\Access\HandlesAuthorization;

class BuyerPolicy
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
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Buyer $buyer)
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
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Buyer $buyer)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Buyer $buyer)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Buyer $buyer)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\Buyer  $buyer
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Buyer $buyer)
    {
        //
    }
}
