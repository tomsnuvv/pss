<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProductLicense;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductLicensesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the product license.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductLicense  $productLicense
     * @return mixed
     */
    public function view(User $user, ProductLicense $productLicense)
    {
        return true;
    }

    /**
     * Determine whether the user can create product licenses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the product license.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductLicense  $productLicense
     * @return mixed
     */
    public function update(User $user, ProductLicense $productLicense)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the product license.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductLicense  $productLicense
     * @return mixed
     */
    public function delete(User $user, ProductLicense $productLicense)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the product license.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductLicense  $productLicense
     * @return mixed
     */
    public function restore(User $user, ProductLicense $productLicense)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the product license.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductLicense  $productLicense
     * @return mixed
     */
    public function forceDelete(User $user, ProductLicense $productLicense)
    {
        return false;
    }
}
