<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Libs\Executors\Audit;
use App\Libs\Executors\Discovery;

/**
 * Audit Project command.
 */
class AuditProject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:project {project}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit a project';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $project = Project::findOrFail($this->argument('project'));

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
    }
}
