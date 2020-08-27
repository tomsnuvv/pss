<?php

namespace App\Observers;

use App\Models\Port;
use App\Libs\Helpers\Projects;

/**
 * Port Observer class.
 */
class PortObserver
{
    /**
     * Handle the Port "created" event.
     *
     * @param \App\Models\Port $port
     */
    public function created(Port $port)
    {
        Projects::relateProjectsFromSourceToTarget($port->host, $port);
    }

    /**
     * Handle the Port "deleting" event.
     *
     * @param  \App\Models\Port $port
     * @return void
     */
    public function deleting(Port $port)
    {
        $port->installation()->delete();
        $port->findings()->delete();
        $port->projects()->detach();
    }
}
