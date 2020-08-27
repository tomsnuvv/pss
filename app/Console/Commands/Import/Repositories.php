<?php

namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use App\Libs\Executors\Import\Repositories as Executor;

/**
 * Import Repositories command.
 *
 * Imports repositories from different providers.
 */
class Repositories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:repositories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import repositories from diferent providers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $executor = new Executor(null, $this->output);
        $executor->run();
    }
}
