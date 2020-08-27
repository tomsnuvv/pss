<?php

namespace App\Console\Commands\PoC;

use Illuminate\Console\Command;
use App\Models\Domain;
use App\Models\Project;
use App\Libs\Helpers\Domains as DomainsHelper;

class Domains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poc:domains {project} {path} {--wildcard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a Proof of Concept from a domains list';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $project = Project::firstOrCreate(['name' => $this->argument('project')]);
        $wildcard = $this->option('wildcard');
        $path = $this->argument('path');

        $handle = fopen($path, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = DomainsHelper::clearDomain($line);
                $domain = Domain::firstOrCreate(['name' => $line]);
                if ($wildcard) {
                    $domain->wildcard = true;
                }
                $domain->key = true;
                $domain->projects()->syncWithoutDetaching($project->id);
                $domain->save();
                $this->line('Domain: <info> ' . $domain->name . '</info>');
            }

            fclose($handle);
        }

        $this->call('discovery:domains');
        $this->call('discovery:subdomains');
        $this->call('discovery:hosts');
        $this->call('discovery:ports');
        $this->call('discovery:websites');

        $this->call('audit:domains');
        $this->call('audit:subdomains');
        $this->call('audit:websites');
    }
}
