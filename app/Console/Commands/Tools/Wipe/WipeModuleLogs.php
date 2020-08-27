<?php

namespace App\Console\Commands\Tools\Wipe;

use Illuminate\Console\Command;
use App\Models\ModuleLog;

class WipeModuleLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:wipe-modulelogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all module logs.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ModuleLog::truncate();
    }
}
