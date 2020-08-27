<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Repository;
use Illuminate\Auth\Access\HandlesAuthorization;

class RepositoriesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the repository.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return mixed
     */
    public function view(User $user, Repository $repository)
    {
        return true;
    }

    /**
     * Determine whether the user can create repositories.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the repository.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return mixed
     */
    public function update(User $user, Repository $repository)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the repository.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return mixed
     */
    public function delete(User $user, Repository $repository)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the repository.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return mixed
     */
    public function restore(User $user, Repository $repository)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the repository.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Repository  $repository
     * @return mixed
     */
    public function forceDelete(User $user, Repository $repository)
    {
        return false;
    }
}
