<?php

namespace App\Policies;

use App\Models\Advert;
use App\Models\User;
use App\Models\Users\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvertPolicy
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
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Advert $advert)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Users\Admin  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $user, Advert $advert)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, Advert $advert)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Advert $advert)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Advert  $advert
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Advert $advert)
    {
        //
    }
}
