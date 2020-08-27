<?php

namespace App\Console\Commands\PoC;

use Illuminate\Console\Command;

class Full extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poc:full';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all the executors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('discovery:organisations');
        $this->call('discovery:domains');
        $this->call('discovery:subdomains');
        $this->call('discovery:hosts');
        $this->call('discovery:ports');
        $this->call('discovery:websites');
        $this->call('discovery:repositories');

        $this->call('audit:domains');
        $this->call('audit:subdomains');
        $this->call('audit:websites');
        $this->call('audit:repositories');
        $this->call('audit:installations');
    }
}
