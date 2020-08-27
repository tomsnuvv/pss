<?php

namespace App\Policies;

use App\Models\User;
use App\Models\FindingStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class FindingStatusesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the finding status.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FindingStatus  $findingStatus
     * @return mixed
     */
    public function view(User $user, FindingStatus $findingStatus)
    {
        return true;
    }

    /**
     * Determine whether the user can create finding statuses.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the finding status.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FindingStatus  $findingStatus
     * @return mixed
     */
    public function update(User $user, FindingStatus $findingStatus)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the finding status.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FindingStatus  $findingStatus
     * @return mixed
     */
    public function delete(User $user, FindingStatus $findingStatus)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the finding status.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FindingStatus  $findingStatus
     * @return mixed
     */
    public function restore(User $user, FindingStatus $findingStatus)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the finding status.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\FindingStatus  $findingStatus
     * @return mixed
     */
    public function forceDelete(User $user, FindingStatus $findingStatus)
    {
        return false;
    }
}
