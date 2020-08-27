<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HostType;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostTypesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the host type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HostType  $hostType
     * @return mixed
     */
    public function view(User $user, HostType $hostType)
    {
        return true;
    }

    /**
     * Determine whether the user can create host types.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the host type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HostType  $hostType
     * @return mixed
     */
    public function update(User $user, HostType $hostType)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the host type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HostType  $hostType
     * @return mixed
     */
    public function delete(User $user, HostType $hostType)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the host type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HostType  $hostType
     * @return mixed
     */
    public function restore(User $user, HostType $hostType)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the host type.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\HostType  $hostType
     * @return mixed
     */
    public function forceDelete(User $user, HostType $hostType)
    {
        return false;
    }
}
