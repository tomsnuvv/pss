<?php

namespace App\Console\Commands\Tools\Wipe;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class WipeData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:wipe-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all data, except 3rd party imports.';

    /**
     * Deletable tables.
     *
     * @var array
     */
    const TABLES = [
        'domain_website', 'host_website', 'findings', 'dns',
        'websites', 'headers', 'domains', 'certificates', 'organisations',
        'whois', 'hosts', 'ports', 'installations', 'nameservers',
        'tokens', 'projects', 'projectables', 'repositories', 'module_logs',
        'requests', 'certificate_host', 'action_events',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->confirm('All data except 3rd party imports will be deleted. Do you really want to continue?')) {
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach (self::TABLES as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Storage::deleteDirectory('public/websites/snapshots');
    }
}
