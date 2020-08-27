<?php

namespace App\Console\Commands\Tools\Wipe;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WipeFindings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:wipe-findings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete findings, module logs & finding projectables';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('projectables')->where('projectable_type', 'App\\Models\\Finding')->delete();
        DB::table('findings')->truncate();
        DB::table('module_logs')->truncate();
    }
}
