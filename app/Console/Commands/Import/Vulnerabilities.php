<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use App\Libs\Executors\Import\Vulnerabilities as Executor;
use Illuminate\Support\Facades\DB;

/**
 * Import Vulnerabilities command.
 *
 * Imports vulnerabilities from different providers.
 */
class Vulnerabilities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:vulnerabilities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import vulnerabilities from diferent providers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Disable the query log, as it's a long process.
        DB::connection()->disableQueryLog();

        $executor = new Executor(null, $this->output);
        $executor->run();
    }
}
