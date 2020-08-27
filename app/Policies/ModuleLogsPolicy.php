<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ModuleLog;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModuleLogsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the module logs.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModuleLog  $ModuleLog
     * @return mixed
     */
    public function view(User $user, ModuleLog $ModuleLog)
    {
        return true;
    }

    /**
     * Determine whether the user can create module logs.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the module logs.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModuleLog  $ModuleLog
     * @return mixed
     */
    public function update(User $user, ModuleLog $ModuleLog)
    {
        return false;
    }

    /**
     * Determine whether the user can delete the module logs.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModuleLog  $ModuleLog
     * @return mixed
     */
    public function delete(User $user, ModuleLog $ModuleLog)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the module logs.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModuleLog  $ModuleLog
     * @return mixed
     */
    public function restore(User $user, ModuleLog $ModuleLog)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the module logs.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ModuleLog  $ModuleLog
     * @return mixed
     */
    public function forceDelete(User $user, ModuleLog $ModuleLog)
    {
        return false;
    }
}
