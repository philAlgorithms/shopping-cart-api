<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use App\Models\Users\{Admin};
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
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
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $user, Tag $tag)
    {
        return true;
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
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $user, Tag $tag)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $user, Tag $tag)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $user, Tag $tag)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Tag  $tag
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Admin $user, Tag $tag)
    {
        return true;
    }
}
