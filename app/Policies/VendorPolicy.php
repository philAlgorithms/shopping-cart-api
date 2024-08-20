<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Users\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class VendorPolicy
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
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Vendor $vendor)
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
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Vendor $vendor)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Vendor $vendor)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Vendor $vendor)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\Vendor  $vendor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Vendor $vendor)
    {
        //
    }

    /**
     * Determine whether the user has a store.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function hasStore(Authenticatable $user)
    {
        if($user instanceof Vendor)
        {
            return $user->has_store;
        }
        return false;
    }
}
