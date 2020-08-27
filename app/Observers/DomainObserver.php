<?php

namespace App\Observers;

use App\Models\Domain;
use App\Events\Domain\Created;
use App\Libs\Helpers\Projects;
use Illuminate\Database\Eloquent\Model;

/**
 * Domain Observer class.
 */
class DomainObserver
{
    /**
     * Handle the Domain "created" event.
     *
     * @param  \App\Models\Domain $domain
     * @return void
     */
    public function created(Domain $domain)
    {
        event(new Created($domain));
    }

    /**
     * Handle the Domain "saved" event.
     *
     * @param  \App\Models\Domain $domain
     * @return void
     */
    public function saved(Domain $domain)
    {
        if ($domain->wasChanged('domain_id')) {
            Projects::relateProjectsFromSourceToTarget($domain->parent, $domain);
        }

        if ($domain->wasChanged('host_id')) {
            Projects::relateProjectsFromSourceToTarget($domain, $domain->host);
        }

        if ($domain->wasChanged('certificate_id')) {
            Projects::relateProjectsFromSourceToTarget($domain, $domain->certificate);
        }
    }

    /**
     * Handle the Domain "deleting" event.
     *
     * @param  \App\Models\Domain $domain
     * @return void
     */
    public function deleting(Domain $domain)
    {
        $domain->findings()->delete();
        $domain->projects()->detach();
        $domain->subdomains()->delete();
        $domain->moduleLogs()->delete();
        $domain->dns()->delete();
        $domain->nameservers()->delete();
        $domain->whois()->delete();
        if ($domain->certificate && $domain->certificate->domains && $domain->certificate->domains->count() == 1) {
            $domain->certificate->delete();
        }
    }
}
