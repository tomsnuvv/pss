<?php

namespace App\Observers;

use App\Models\Repository;
use App\Libs\Helpers\Projects;
use App\Libs\Providers\Repositories\Git;

/**
 * Repository Observer class.
 */
class RepositoryObserver
{
    /**
     * Handle the Repository "deleting" event.
     *
     * @param  \App\Models\Repository $repository
     * @return void
     */
    public function deleting(Repository $repository)
    {
        $provider = new Git($repository);
        $provider->delete();
        $repository->installations()->delete();
        $repository->findings()->delete();
        $repository->projects()->detach();
        $repository->moduleLogs()->delete();
    }
}
