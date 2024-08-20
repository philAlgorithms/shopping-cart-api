<?php

namespace App\Policies;

use App\Models\Location\Town;
use App\Models\Users\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class TownPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Authenticatable $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Location\Town  $town
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Authenticatable $user, Town $town)
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
     * @param  \App\Models\Location\Town  $town
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Authenticatable $user, Town $town)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Location\Town  $town
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, Town $town)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Location\Town  $town
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Authenticatable $user, Town $town)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Location\Town  $town
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Authenticatable $user, Town $town)
    {
        //
    }
}
