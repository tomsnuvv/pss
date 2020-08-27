<?php

namespace App\Listeners\Websites;

use App\Libs\Modules\Discovery\Websites\Host;
use App\Events\Contracts\Interfaces\ModelEvent;

/**
 * Executes the Discover Website Host module, to associate the related website's host.
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
