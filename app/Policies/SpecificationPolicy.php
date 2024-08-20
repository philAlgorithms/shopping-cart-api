<?php

namespace App\Policies;

use App\Models\Specifications\Specification;
use App\Models\User;
use App\Models\Users\{Admin};
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecificationPolicy
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
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Specification $specification)
    {
        //
    }

    /**
     * Determine whether the user can create a specification.
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
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Specification $specification)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Specification $specification)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Specification $specification)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Specifications\Specification  $specification
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Specification $specification)
    {
        //
    }
}
