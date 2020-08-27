<?php

namespace App\Observers;

use App\Models\Host;
use App\Libs\Helpers\Projects;

/**
 * Host Observer class.
 */
class HostObserver
{
    /**
     * Handle the Host "deleting" event.
     *
     * @param  \App\Models\Host $host
     * @return void
     */
    public function deleting(Host $host)
    {
        $host->ports()->delete();
        $host->installations()->delete();
        $host->findings()->delete();
        $host->projects()->detach();
        $host->nameservers()->delete();
        $host->token()->delete();
        $host->moduleLogs()->delete();
    }
}
