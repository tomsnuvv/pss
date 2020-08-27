<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Nameserver;
use Illuminate\Auth\Access\HandlesAuthorization;

class NameserversPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the nameserver.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nameserver  $nameserver
     * @return mixed
     */
    public function view(User $user, Nameserver $nameserver)
    {
        return true;
    }

    /**
     * Determine whether the user can create nameservers.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the nameserver.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nameserver  $nameserver
     * @return mixed
     */
    public function update(User $user, Nameserver $nameserver)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the nameserver.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nameserver  $nameserver
     * @return mixed
     */
    public function delete(User $user, Nameserver $nameserver)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the nameserver.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nameserver  $nameserver
     * @return mixed
     */
    public function restore(User $user, Nameserver $nameserver)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the nameserver.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nameserver  $nameserver
     * @return mixed
     */
    public function forceDelete(User $user, Nameserver $nameserver)
    {
        return false;
    }
}
