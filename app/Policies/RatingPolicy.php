<?php

namespace App\Policies;

use App\Models\Ratings\Rating;
use App\Models\User;
use App\Models\Users\Admin;
use App\Models\Users\Buyer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class RatingPolicy
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
     * @param  \App\Models\Ratings\Rating  $rating
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Rating $rating)
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
     * @param  \App\Models\Ratings\Rating  $rating
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Rating $rating)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Ratings\Rating  $rating
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, Rating $rating)
    {
        $is_admin = $user instanceof Admin;

        if($is_admin)
        {
            return Response::allow();
        }

        $is_rater = $user->user->id ?? 0 === $rating->rater->id;

        return $is_rater ? Response::allow() : Response::deny('You cannot delete this rating');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ratings\Rating  $rating
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Rating $rating)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Ratings\Rating  $rating
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Admin $user, Rating $rating)
    {
        return true;
    }
}
