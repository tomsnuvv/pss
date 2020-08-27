<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Host;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the host.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Host  $host
     * @return mixed
     */
    public function view(User $user, Host $host)
    {
        return true;
    }

    /**
     * Determine whether the user can create hosts.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the host.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Host  $host
     * @return mixed
     */
    public function update(User $user, Host $host)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the host.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Host  $host
     * @return mixed
     */
    public function delete(User $user, Host $host)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the host.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Host  $host
     * @return mixed
     */
    public function restore(User $user, Host $host)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the host.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Host  $host
     * @return mixed
     */
    public function forceDelete(User $user, Host $host)
    {
        return $user->isAdmin();
    }
}
