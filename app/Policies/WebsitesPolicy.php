<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Website;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebsitesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Website  $website
     * @return mixed
     */
    public function view(User $user, Website $website)
    {
        return true;
    }

    /**
     * Determine whether the user can create websites.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Website  $website
     * @return mixed
     */
    public function update(User $user, Website $website)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Website  $website
     * @return mixed
     */
    public function delete(User $user, Website $website)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Website  $website
     * @return mixed
     */
    public function restore(User $user, Website $website)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the website.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Website  $website
     * @return mixed
     */
    public function forceDelete(User $user, Website $website)
    {
        return true;
    }
}
