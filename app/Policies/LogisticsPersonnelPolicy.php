<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Users\Admin;
use App\Models\Users\LogisticsPersonnel;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class LogisticsPersonnelPolicy
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
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, LogisticsPersonnel $logisticsPersonnel)
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
        if ($user instanceof Admin) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, LogisticsPersonnel $logisticsPersonnel)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, LogisticsPersonnel $logisticsPersonnel)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, LogisticsPersonnel $logisticsPersonnel)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Users\LogisticsPersonnel  $logisticsPersonnel
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, LogisticsPersonnel $logisticsPersonnel)
    {
        //
    }
}
