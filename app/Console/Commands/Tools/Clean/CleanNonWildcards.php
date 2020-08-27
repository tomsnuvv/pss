<?php

namespace App\Console\Commands\Tools\Clean;

use Illuminate\Console\Command;
use App\Models\Domain;

class CleanNonWildcards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:clean-non-wildcards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all subdomains from non-wildcard parents.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domains = Domain::where('key', 0)->whereHas('parent', function ($query) {
            $query->where('wildcard', 0);
        })
        ->whereDoesntHave('targetDNS')
        ->whereDoesntHave('websites', function ($query) {
            $query->where('key', 1);
        })
        ->get();

        $this->line($domains->count());

        foreach ($domains as $domain) {
            $this->line($domain->name);
            $domain->delete();
        }
    }
}
