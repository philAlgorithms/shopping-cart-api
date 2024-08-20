<?php

namespace App\Policies;

use App\Models\Products\Brand;
use App\Models\User;
use App\Models\Users\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class BrandPolicy
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
     * @param  \App\Models\Products\Brand  $brand
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Brand $brand)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Authenticatable $user)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Products\Brand  $brand
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Authenticatable $user, Brand $brand)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Products\Brand  $brand
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, Brand $brand)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Products\Brand  $brand
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Authenticatable $user, Brand $brand)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Products\Brand  $brand
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Authenticatable $user, Brand $brand)
    {
        return $user instanceof Admin;
    }
}
