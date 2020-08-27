<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganisationsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the organisation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function view(User $user, Organisation $organisation)
    {
        return true;
    }

    /**
     * Determine whether the user can create organisations.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the organisation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function update(User $user, Organisation $organisation)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the organisation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function delete(User $user, Organisation $organisation)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the organisation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function restore(User $user, Organisation $organisation)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the organisation.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Organisation  $organisation
     * @return mixed
     */
    public function forceDelete(User $user, Organisation $organisation)
    {
        return $user->isAdmin();
    }
}
