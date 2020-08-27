<?php

namespace App\Nova\Actions\Projects;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\DestructiveAction;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Delete Project action.
 */
class Delete extends DestructiveAction implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $project) {
            $project->delete();

            $this->markAsFinished($project);
        }
    }
}
