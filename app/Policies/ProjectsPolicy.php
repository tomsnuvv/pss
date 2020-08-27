<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the project.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
        return true;
    }

    /**
     * Determine whether the user can create projects.
     *
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the project.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the project.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the project.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function restore(User $user, Project $project)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the project.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function forceDelete(User $user, Project $project)
    {
        return true;
    }

    /**
     * Determine whether the user can attach a certificate to a project.
     *
     * @param  \App\Models\User        $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function attachAnyCertificate(User $user, Project $project)
    {
        return false;
    }

    /**
     * Determine whether the user can detach a certificate from a project.
     *
     * @param  \App\Models\User        $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function detachAnyCertificate(User $user, Project $project)
    {
        return false;
    }

    /**
     * Determine whether the user can attach a port to a project.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function attachAnyPort(User $user, Project $project)
    {
        return false;
    }

    /**
     * Determine whether the user can detach a port from a project.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function detachAnyPort(User $user, Project $project)
    {
        return false;
    }

    /**
     * Determine whether the user can attach an installation to a project.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function attachAnyInstallations(User $user, Project $project)
    {
        return false;
    }

    /**
     * Determine whether the user can detach an installation from a project.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function detachAnyInstallations(User $user, Project $project)
    {
        return false;
    }

    /**
     * Determine whether the user can attach a finding to a project.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function attachAnyFinding(User $user, Project $project)
    {
        return false;
    }

    /**
     * Determine whether the user can detach a finding from a project.
     *
     * @param  \App\Models\User    $user
     * @param  \App\Models\Project $project
     * @return mixed
     */
    public function detachAnyFinding(User $user, Project $project)
    {
        return false;
    }
}
