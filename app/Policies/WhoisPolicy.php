<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Whois;
use Illuminate\Auth\Access\HandlesAuthorization;

class WhoisPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the whois.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whois  $whois
     * @return mixed
     */
    public function view(User $user, Whois $whois)
    {
        return true;
    }

    /**
     * Determine whether the user can create whois.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the whois.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whois  $whois
     * @return mixed
     */
    public function update(User $user, Whois $whois)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the whois.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whois  $whois
     * @return mixed
     */
    public function delete(User $user, Whois $whois)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the whois.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whois  $whois
     * @return mixed
     */
    public function restore(User $user, Whois $whois)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the whois.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Whois  $whois
     * @return mixed
     */
    public function forceDelete(User $user, Whois $whois)
    {
        return false;
    }
}
