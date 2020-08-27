<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vulnerability;
use Illuminate\Auth\Access\HandlesAuthorization;

class VulnerabilitiesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the vulnerability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return mixed
     */
    public function view(User $user, Vulnerability $vulnerability)
    {
        return true;
    }

    /**
     * Determine whether the user can create vulnerabilities.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the vulnerability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return mixed
     */
    public function update(User $user, Vulnerability $vulnerability)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the vulnerability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return mixed
     */
    public function delete(User $user, Vulnerability $vulnerability)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the vulnerability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return mixed
     */
    public function restore(User $user, Vulnerability $vulnerability)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the vulnerability.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return mixed
     */
    public function forceDelete(User $user, Vulnerability $vulnerability)
    {
        return $user->isAdmin();
    }
}
