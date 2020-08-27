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

class IgnoreAll extends DestructiveAction
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
            if ($finding->vulnerability) {
                $related = Finding::where('vulnerability_id', $finding->vulnerability->id)->get();
            } elseif ($finding->type) {
                $related = Finding::where('vulnerability_type_id', $finding->type->id)->get();
            }
            foreach ($related as $relatedFinding) {
                $relatedFinding->markAsFalsePositive();
            }
        }
        return Action::message('All related findings were marked as False Positives');
    }
}
