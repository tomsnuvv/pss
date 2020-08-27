<?php

namespace App\Listeners\Domains;

use App\Libs\Modules\Discovery\Domains\Host;
use App\Events\Contracts\Interfaces\ModelEvent;

/**
 * Executes the Discover Domains Host module, to associate the related domains's host.
 */
class DiscoverHost
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Contracts\Interfaces\ModelEvent $event
     * @return void
     */
    public function handle(ModelEvent $event)
    {
        $module = new Host($event->model);
        $module->execute();
    }
}
