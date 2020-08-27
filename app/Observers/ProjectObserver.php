<?php

namespace App\Observers;

use App\Libs\Helpers\Projects;
use App\Models\Host;
use App\Models\Port;
use App\Models\Domain;
use App\Models\Finding;
use App\Models\Project;
use App\Models\Website;
use App\Models\Repository;
use App\Models\Certificate;
use App\Models\Installation;

/**
 * Project Observer class.
 */
class ProjectObserver
{
    /**
     * Handle the Project "deleting" event.
     *
     * @param  \App\Models\Project $project
     * @return void
     */
    public function deleting(Project $project)
    {
        $relations = ['repositories', 'websites', 'hosts', 'domains', 'ports', 'certificates'];
        foreach ($relations as $relation) {
            foreach ($project->$relation as $model) {
                if (!$model) {
                    continue;
                }
                // Not involved in other projects
                if ($model->projects->count() == 1) {
                    $model->delete();
                }
            }
        }
    }
}
