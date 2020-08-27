<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Domain;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the domain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return mixed
     */
    public function view(User $user, Domain $domain)
    {
        return true;
    }

    /**
     * Determine whether the user can create domains.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the domain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return mixed
     */
    public function update(User $user, Domain $domain)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the domain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return mixed
     */
    public function delete(User $user, Domain $domain)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the domain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return mixed
     */
    public function restore(User $user, Domain $domain)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the domain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Domain  $domain
     * @return mixed
     */
    public function forceDelete(User $user, Domain $domain)
    {
        return $user->isAdmin();
    }
}
