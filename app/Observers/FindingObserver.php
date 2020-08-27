<?php

namespace App\Observers;

use App\Models\Finding;
use App\Libs\Helpers\Projects;

/**
 * Finding Observer class.
 */
class FindingObserver
{
    /**
     * Handle the Finding "created" event.
     *
     * @param \App\Models\Finding $finding
     */
    public function created(Finding $finding)
    {
        // Relate the Finding to target's Projects
        if (isset($finding->target->projects)) {
            Projects::relateProjectsFromSourceToTarget($finding->target, $finding);
        }
        // Relate the Finding to childTarget's Projects
        if (isset($finding->childTarget->projects)) {
            Projects::relateProjectsFromSourceToTarget($finding->childTarget, $finding);
        }
    }

    /**
     * Handle the Finding "deleting" event.
     *
     * @param \App\Models\Finding $finding
     */
    public function deleting(Finding $finding)
    {
        // Delete database rows, as morph relations doesn't have foreing keys.
        $finding->projects()->detach();
    }
}
