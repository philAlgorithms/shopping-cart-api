<?php

namespace App\Policies;

use App\Models\Products\ProductSubCategory;
use App\Models\User;
use App\Models\Users\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Authenticatable;

class ProductSubCategoryPolicy
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
     * @param  \App\Models\Products\ProductSubCategory  $productCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ProductSubCategory $productCategory)
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
     * @param  \App\Models\Products\ProductSubCategory  $productCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Authenticatable $user, ProductSubCategory $productCategory)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Products\ProductSubCategory  $productCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, ProductSubCategory $productCategory)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Products\ProductSubCategory  $productCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Authenticatable $user, ProductSubCategory $productCategory)
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  \App\Models\Products\ProductSubCategory  $productCategory
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Authenticatable $user, ProductSubCategory $productCategory)
    {
        return $user instanceof Admin;
    }
}
