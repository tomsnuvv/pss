<?php

namespace App\Listeners\Domains;

use App\Libs\Helpers\Domains;
use App\Events\Contracts\Interfaces\ModelEvent;

/**
 * Creates an associates a parent domain.
 */
class CreateParentDomain
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Contracts\Interfaces\ModelEvent $event
     * @return void
     */
    public function handle(ModelEvent $event)
    {
        if (Domains::isSubdomain($event->model->name)) {
            $top = Domains::getTopLevelDomain($event->model->name);
            $topDomain = Domains::createDomain($top);
            if ($topDomain) {
                // Preserve domain key
                /*if ($event->model->key) {
                    $topDomain->key = 1;
                    $topDomain->save();
                }*/
                $event->model->parent()->associate($topDomain);
                $event->model->save();
            }
        }
    }
}
