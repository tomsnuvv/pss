<?php

namespace App\Observers;

use App\Models\Pivots\Projectable;
use App\Models\Domain;
use App\Models\Host;
use App\Models\Website;
use App\Models\Installation;
use App\Models\Certificate;
use App\Models\Port;
use App\Models\Repository;
use App\Libs\Helpers\Projects;

/**
 * Projectable Observer class.
 */
class ProjectableObserver
{
    /**
     * Handle the Projectable "created" event.
     *
     * @param \App\Models\Pivots\Projectable $model
     */
    public function created(Projectable $model)
    {
        $projectable = $model->projectable;

        // Domain
        if (get_class($projectable) == Domain::class) {
            foreach (['host', 'certificate'] as $relationship) {
                if ($projectable->$relationship) {
                    $projectable->$relationship->projects()->attach($model->project);
                }
            }

            // Subdomains (special naming)
            $itemsIds = $projectable->subdomains()->pluck('domains.id')->toArray();
            if (!empty($itemsIds)) {
                $model->project->domains()->syncWithoutDetaching($itemsIds);
            }

            Projects::relateProjectsToRelationships($model->project, $projectable, ['findings', 'websites']);
        }

        // Host
        // Websites and Domains will not be associated (shared hostings etc)
        elseif (get_class($projectable) == Host::class) {
            Projects::relateProjectsToRelationships($model->project, $projectable, ['installations', 'findings', 'ports']);
        }

        // Website
        elseif (get_class($projectable) == Website::class) {
            Projects::relateProjectsToRelationships($model->project, $projectable, ['domains', 'hosts', 'installations', 'findings']);
        }

        // Repository
        elseif (get_class($projectable) == Repository::class) {
            Projects::relateProjectsToRelationships($model->project, $projectable, ['installations', 'findings']);
        }

        // Installation
        elseif (get_class($projectable) == Installation::class) {
            $itemsIds = $projectable->findings()->pluck('findings.id')->toArray();
            if (!empty($itemsIds)) {
                $model->project->findings()->syncWithoutDetaching($itemsIds);
            }
        }

        // Certificate
        elseif (get_class($projectable) == Certificate::class) {
            $itemsIds = $projectable->findings()->pluck('findings.child_target_id')->toArray();
            if (!empty($itemsIds)) {
                $model->project->findings()->syncWithoutDetaching($itemsIds);
            }
        }

        // Port
        elseif (get_class($projectable) == Port::class) {
            $itemsIds = $projectable->findings()->pluck('findings.child_target_id')->toArray();
            if (!empty($itemsIds)) {
                $model->project->findings()->syncWithoutDetaching($itemsIds);
            }
            if ($projectable->installation) {
                $model->project->installations()->syncWithoutDetaching([$projectable->installation->id]);
            }
        }
    }
}
