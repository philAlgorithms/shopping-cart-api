<?php

namespace App\Policies;

use App\Models\Stores\Store;
use App\Models\User;
use App\Models\Users\Admin;
use App\Models\Users\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class StorePolicy
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
     * @param  \App\Models\Stores\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Store $store)
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
     * @param  \App\Models\Stores\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stores\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stores\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Store $store)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Stores\Store  $store
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Store $store)
    {
        //
    }

    public function uploadCACVerificationDocument(Authenticatable $user, Store $store): Response | bool
    {
        if($user instanceof Vendor && $user->store->id === $store->id)
        {
            return $store->is_verified ?
                Response::deny('Store has already been approved for transactions.') :
                Response::allow();
        }
        return false;
    }

    public function verify(Authenticatable $user, Store $store): Response | bool
    {
        if($user instanceof Admin)
        {
            return $store->cac === null ?
                Response::deny('Vendor has not uploaded CAC registration document') :
                Response::allow();
        }
        return false;
    }
}
