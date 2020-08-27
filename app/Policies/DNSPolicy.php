<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DNS;
use Illuminate\Auth\Access\HandlesAuthorization;

class DNSPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the d n s.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DNS  $dNS
     * @return mixed
     */
    public function view(User $user, DNS $dNS)
    {
        return true;
    }

    /**
     * Determine whether the user can create d n s s.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the d n s.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DNS  $dNS
     * @return mixed
     */
    public function update(User $user, DNS $dNS)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the d n s.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DNS  $dNS
     * @return mixed
     */
    public function delete(User $user, DNS $dNS)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the d n s.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DNS  $dNS
     * @return mixed
     */
    public function restore(User $user, DNS $dNS)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the d n s.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DNS  $dNS
     * @return mixed
     */
    public function forceDelete(User $user, DNS $dNS)
    {
        return false;
    }
}
