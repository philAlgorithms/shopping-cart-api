<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Users\Admin;
use App\Models\Users\Buyer;
use App\Models\Users\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class UserPolicy
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
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
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
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can upload bvn. Only users that have not
     * uploaded their bvn or whose bvn have been declined can upload.
     *
     * @param  Illuminate\Contracts\Auth\Authenticatable $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function uploadBvn(Authenticatable $user): Response | bool
    {
        if($user instanceof Buyer || $user instanceof Vendor)
        {
            $model = $user->user;

            return ! $model->has_bvn || $model->bvn_is_declined ? true :
                Response::deny('BVN already uploaded');
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can verify bvn.
     *
     * @param  Illuminate\Contracts\Auth\Authenticatable $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function verifyBvn(Authenticatable $user, User $model): Response | bool
    {
        if ($user instanceof Admin) {
            return $model->bvn_is_verified ?
                Response::deny('BVN has already been verified') : ($model->bvn_is_declined ?
                    Response::deny('BVN has already been declined') :
                    $model->bvn_is_verifiable
                );
        } else {
            return false;
        }
    }

    /**
     * Determine whether the user can decline bvn.
     *
     * @param  Illuminate\Contracts\Auth\Authenticatable $user
     * @param  \App\Models\User  $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function declineBvn(Authenticatable $user, User $model): Response|bool
    {
        if ($user instanceof Admin) {
            return $model->bvn_is_verified ?
                Response::deny('BVN has already been verified') : ($model->bvn_is_declined ?
                    Response::deny('BVN has already been declined') :
                    $model->bvn_is_declinable
                );
        } else {
            return false;
        }
    }
}
