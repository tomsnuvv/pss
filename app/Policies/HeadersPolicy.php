<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Header;
use Illuminate\Auth\Access\HandlesAuthorization;

class HeadersPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the header.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Header  $header
     * @return mixed
     */
    public function view(User $user, Header $header)
    {
        return true;
    }

    /**
     * Determine whether the user can create headers.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the header.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Header  $header
     * @return mixed
     */
    public function update(User $user, Header $header)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the header.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Header  $header
     * @return mixed
     */
    public function delete(User $user, Header $header)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the header.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Header  $header
     * @return mixed
     */
    public function restore(User $user, Header $header)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the header.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Header  $header
     * @return mixed
     */
    public function forceDelete(User $user, Header $header)
    {
        return false;
    }
}
