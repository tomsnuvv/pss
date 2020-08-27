<?php

namespace App\Listeners\Websites;

use App\Libs\Modules\Discovery\Websites\Domains;
use App\Events\Contracts\Interfaces\ModelEvent;

/**
 * Executes the Discover Website Domain module, to associate the related website's domain.
 */
class DiscoverDomain
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\Contracts\Interfaces\ModelEvent $event
     * @return void
     */
    public function handle(ModelEvent $event)
    {
        $module = new Domains($event->model);
        $module->execute();
    }
}
