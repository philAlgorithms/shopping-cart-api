<?php

namespace App\Policies;

use App\Models\Products\Product;
use App\Models\User;
use App\Models\Users\{Admin, Buyer, Vendor};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Authenticatable;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\Users\Vendor  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Vendor $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Products\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Product $product)
    {
        // 
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\Users\Vendor  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Authenticatable $user)
    {
        return $user instanceof Vendor && !is_null($user->store);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\Users\Vendor  $user
     * @param  \App\Models\Products\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Authenticatable $user, Product $product)
    {
        return $user instanceof Vendor && !is_null($user->store) && $user->store->id === $product->store->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Products\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Authenticatable $user, Product $product)
    {
        return $this->update($user, $product); // Also check if product has been bought in the past
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\Users\Admin  $user
     * @param  \App\Models\Products\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $user, Product $product)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\Users\Admin|\App\Models\Users\Vendor  $user
     * @param  \App\Models\Products\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Authenticatable $user, Product $product)
    {
        $same_store = $user instanceof Vendor
            ? $user->store->id === $product->store->id
            : false;
        $is_admin = $user instanceof Admin
            ? true
            : false;

        return $same_store || $is_admin;
    }

    /**
     * Determines if a user can rate a product
     */
    public function rate(Buyer $user, Product $product)
    {
        return true;
    }
}
