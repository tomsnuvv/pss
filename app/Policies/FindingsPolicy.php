<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Finding;
use Illuminate\Auth\Access\HandlesAuthorization;

class FindingsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the finding.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Finding  $finding
     * @return mixed
     */
    public function view(User $user, Finding $finding)
    {
        return true;
    }

    /**
     * Determine whether the user can create findings.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the finding.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Finding  $finding
     * @return mixed
     */
    public function update(User $user, Finding $finding)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the finding.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Finding  $finding
     * @return mixed
     */
    public function delete(User $user, Finding $finding)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the finding.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Finding  $finding
     * @return mixed
     */
    public function restore(User $user, Finding $finding)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the finding.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Finding  $finding
     * @return mixed
     */
    public function forceDelete(User $user, Finding $finding)
    {
        return $user->isAdmin();
    }
}
