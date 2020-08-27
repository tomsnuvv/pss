<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Integration;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntegrationsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the integration.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Integration  $integration
     * @return mixed
     */
    public function view(User $user, Integration $integration)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create integrations.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the integration.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Integration  $integration
     * @return mixed
     */
    public function update(User $user, Integration $integration)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the integration.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Integration  $integration
     * @return mixed
     */
    public function delete(User $user, Integration $integration)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the integration.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Integration  $integration
     * @return mixed
     */
    public function restore(User $user, Integration $integration)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the integration.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Integration  $integration
     * @return mixed
     */
    public function forceDelete(User $user, Integration $integration)
    {
        return $user->isAdmin();
    }
}
