<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Token;
use Illuminate\Auth\Access\HandlesAuthorization;

class TokensPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the token.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function view(User $user, Token $token)
    {
        return true;
    }

    /**
     * Determine whether the user can create tokens.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the token.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function update(User $user, Token $token)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the token.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function delete(User $user, Token $token)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the token.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function restore(User $user, Token $token)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the token.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function forceDelete(User $user, Token $token)
    {
        return true;
    }
}
