<?php

namespace App\Nova\Actions\Projects;

use App\Libs\Executors\Audit;
use App\Libs\Executors\Discovery;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuditProject extends Action implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $project) {
            foreach ($project->domains()->topLevel()->get() as $domain) {
                (new Discovery\Domains($domain))->run();
                (new Audit\Domains($domain))->run();
            }

            foreach ($project->domains()->querySubdomains()->get() as $subdomain) {
                (new Discovery\Domains($subdomain))->run();
                (new Audit\Domains($subdomain))->run();
            }

            foreach ($project->websites as $website) {
                (new Discovery\Websites($website))->run();
                (new Audit\Websites($website))->run();
            }

            foreach ($project->repositories as $repository) {
                (new Discovery\Repositories($repository))->run();
                (new Audit\Repositories($repository))->run();
            }

            foreach ($project->hosts as $host) {
                (new Discovery\Hosts($host))->run();
                foreach ($host->ports as $port) {
                    (new Discovery\Ports($port))->run();
                }
            }

            $this->markAsFinished($project);
        }
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [];
    }
}
