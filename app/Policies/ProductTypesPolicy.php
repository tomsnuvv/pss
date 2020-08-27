<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProductType;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductTypesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the product type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return mixed
     */
    public function view(User $user, ProductType $productType)
    {
        return true;
    }

    /**
     * Determine whether the user can create product types.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the product type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return mixed
     */
    public function update(User $user, ProductType $productType)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the product type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return mixed
     */
    public function delete(User $user, ProductType $productType)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the product type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return mixed
     */
    public function restore(User $user, ProductType $productType)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the product type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductType  $productType
     * @return mixed
     */
    public function forceDelete(User $user, ProductType $productType)
    {
        return false;
    }
}
