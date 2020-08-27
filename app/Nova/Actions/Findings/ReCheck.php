<?php

namespace App\Nova\Actions\Findings;

use App\Models\Finding;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\DestructiveAction;

class ReCheck extends DestructiveAction
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $finding) {
            $moduleClass = 'App\\Libs\\Modules\\' . $finding->module->code;
            if ($finding->installation) {
                $model = $finding->installation;
            } else {
                $model = $finding->target;
            }
            $module = new $moduleClass($model);
            $module->execute();
        }

        return Action::message('Check executed');
    }
}
