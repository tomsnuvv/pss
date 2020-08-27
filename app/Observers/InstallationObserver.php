<?php

namespace App\Observers;

use App\Models\Installation;
use App\Libs\Helpers\Projects;

/**
 * Installation Observer class.
 */
class InstallationObserver
{
    /**
     * Handle the Installation "creating" event.
     *
     * @param \App\Models\Installation $installation
     */
    public function creating(Installation $installation)
    {
        // Installation title
        if ($installation->product) {
            $installation->title = $installation->product->name;
            if ($installation->version !== null) {
                $installation->title .= ' ' . $installation->version;
            }
        }
    }

    /**
     * Handle the Installation "created" event.
     *
     * @param \App\Models\Installation $installation
     */
    public function created(Installation $installation)
    {
        // Relate the Installation to target's Projects
        if (isset($installation->source->projects)) {
            Projects::relateProjectsFromSourceToTarget($installation->source, $installation);
        }
        // Relate the Installation to childSource's Projects
        if (isset($installation->childSource->projects)) {
            Projects::relateProjectsFromSourceToTarget($installation->childSource, $installation);
        }
    }

    /**
     * Handle the Installation "deleting" event.
     *
     * @param \App\Models\Installation $installation
     */
    public function deleting(Installation $installation)
    {
        // Delete database rows, as morph relations doesn't have foreing keys.
        $installation->projects()->detach();
    }
}
