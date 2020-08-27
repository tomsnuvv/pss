<?php

namespace App\Nova\Actions\Findings;

use App\Models\FindingStatus;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Fields\Select;

class ChangeStatus extends Action
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
        $status = FindingStatus::find($fields->status);
        if (!$status) {
            return Action::danger('Invalid Status');
        }

        foreach ($models as $finding) {
            $finding->status()->associate($status);
            $finding->save();
        }

        return Action::message('Findings status changed');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make('Status')->options(FindingStatus::pluck('name', 'id'))
        ];
    }
}
