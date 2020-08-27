<?php

namespace App\Libs\Helpers;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;

/**
 * Projects Helper class.
 */
class Projects
{
    /**
     * Attach a project into a model.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  int                                 $projectId
     */
    public static function attachProjectIntoModel($model, $projectId)
    {
        $model->projects()->syncWithoutDetaching([$projectId]);
    }

    /**
     * Get all the projects from a Source Model, and attach them into all targets.
     *
     * @param \Illuminate\Database\Eloquent\Model $source
     * @param string                              $relation
     * @param array                               $targetIds
     */
    public static function relateProjectsFromSourceToTargets(Model $source, $relation, $targetIds)
    {
        foreach ($targetIds as $targetId) {
            $target = $source->$relation()->find($targetId);
            self::relateProjectsFromSourceToTarget($source, $target);
        }
    }

    /**
     * Get all the projects from a Source Model, and attach them into a Target Model.
     *
     * @param \Illuminate\Database\Eloquent\Model $source
     * @param \Illuminate\Database\Eloquent\Model $target
     */
    public static function relateProjectsFromSourceToTarget(Model $source, Model $target)
    {
        $projectsIds = $source->projects()->pluck('id')->toArray();
        if (!empty($projectsIds)) {
            $target->projects()->syncWithoutDetaching($projectsIds);
        }
    }

    /**
     * Assocaite multiple relationships models from a model into a project.
     *
     * @param  Project $project
     * @param  Model   $model
     * @param  array   $relationships List of relationship names.
     */
    public static function relateProjectsToRelationships(Project $project, Model $model, $relationships)
    {
        foreach ($relationships as $relationship) {
            $itemsIds = $model->$relationship()->pluck($relationship . '.id')->toArray();
            if (!empty($itemsIds)) {
                $project->$relationship()->syncWithoutDetaching($itemsIds);
            }
        }
    }
}
