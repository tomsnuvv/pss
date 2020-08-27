<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Installation;
use Illuminate\Auth\Access\HandlesAuthorization;

class InstallationsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the installation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Installation  $installation
     * @return mixed
     */
    public function view(User $user, Installation $installation)
    {
        return true;
    }

    /**
     * Determine whether the user can create installations.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the installation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Installation  $installation
     * @return mixed
     */
    public function update(User $user, Installation $installation)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the installation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Installation  $installation
     * @return mixed
     */
    public function delete(User $user, Installation $installation)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the installation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Installation  $installation
     * @return mixed
     */
    public function restore(User $user, Installation $installation)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the installation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Installation  $installation
     * @return mixed
     */
    public function forceDelete(User $user, Installation $installation)
    {
        return false;
    }
}
