<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Severity;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeveritiesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the severity.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Severity  $severity
     * @return mixed
     */
    public function view(User $user, Severity $severity)
    {
        return true;
    }

    /**
     * Determine whether the user can create severities.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the severity.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Severity  $severity
     * @return mixed
     */
    public function update(User $user, Severity $severity)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the severity.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Severity  $severity
     * @return mixed
     */
    public function delete(User $user, Severity $severity)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the severity.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Severity  $severity
     * @return mixed
     */
    public function restore(User $user, Severity $severity)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the severity.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Severity  $severity
     * @return mixed
     */
    public function forceDelete(User $user, Severity $severity)
    {
        return false;
    }
}
