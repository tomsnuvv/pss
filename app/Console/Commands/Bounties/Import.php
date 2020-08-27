<?php

namespace App\Console\Commands\Bounties;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Repository;
use App\Libs\Helpers\Websites;
use App\Libs\Helpers\Domains;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bounties:import {file} {program}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Personal Bounties';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = json_decode(file_get_contents($this->argument('file')));

        $programOnly = $this->argument('program');

        foreach ($data as $program) {
            if ($programOnly && $program->handle != $programOnly) {
                continue;
            }

            $project = Project::firstOrCreate(['name' => $program->name]);

            $this->info($project->name);

            foreach ($program->targets->in_scope as $target) {
                if (!isset($target->asset_type)) {
                    continue;
                }

                if ($target->asset_type == 'SOURCE_CODE') {
                    if (!strstr($target->instruction, 'github')) {
                        continue;
                    }

                    $text = strtolower($target->instruction);

                    // Extract Github repositories
                    preg_match_all('#https://github.com/[\w-_\.]+\/[\w-_\.]+#', $text, $matches);
                    if (isset($matches[0]) && !empty($matches[0])) {
                        foreach ($matches[0] as $url) {
                            $this->createRepository($url, $project);
                        }
                    }
                } elseif ($target->asset_type == 'URL') {
                    $url = $target->asset_identifier;
                    if (substr($url, 0, 2) == '*.') {
                        $this->createDomain($url, $project);
                    } else {
                        $domain = str_replace(['https://', 'http://'], '', $url);

                        echo $domain . PHP_EOL;

                        if (substr($domain, 0, 2) == '*.') {
                            $this->createDomain($domain, $project);
                            continue;
                        }

                        if (substr($url, 0, 4) != 'http') {
                            $url = 'http://' . $url;
                        }
                        $url = str_replace(['*.', '*'], '', $url);
                        $this->createWebsite($url, $project);
                    }
                }
            }
        }
    }

    private function createDomain($domain, $project)
    {
        $wildcard = 0;
        if (strstr($domain, '*')) {
            $wildcard = 1;
        }
        $domain = str_replace(['*.', '*', '/'], '', $domain);
        $domain = Domains::createDomain($domain);
        if (!$domain) {
            return;
        }
        $domain->key = 1;
        $domain->wildcard = $wildcard;
        $domain->save();
        $domain->projects()->syncWithoutDetaching($project->id);
        $this->line('Domain: ' . $domain->name);
    }

    private function createWebsite($url, $project)
    {
        $url = str_replace(['*.', '*'], '', $url);
        $website = Websites::createWebsite($url);
        if (!$website) {
            return;
        }
        $website->key = 1;
        $website->save();
        foreach ($website->domains as $domain) {
            $domain->key = 1;
            $domain->save();
            if ($domain->parent) {
                $domain->parent->key = 1;
                $domain->parent->save();
            }
        }
        foreach ($website->hosts as $host) {
            $host->key = 1;
            $host->save();
        }
        $website->projects()->syncWithoutDetaching($project->id);
        $this->line('Website: ' . $website->url);
    }

    private function createRepository($url, $project)
    {
        $url = trim($url, '.');
        $name = str_replace('https://github.com/', '', $url);
        $repository = Repository::firstOrCreate(['name' => $name, 'url' => $url]);
        $repository->projects()->syncWithoutDetaching($project->id);
        $this->line('Repository: ' . $repository->url);
    }
}
