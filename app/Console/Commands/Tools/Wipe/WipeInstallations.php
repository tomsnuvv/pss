<?php

namespace App\Console\Commands\Tools\Wipe;

use Illuminate\Console\Command;
use App\Models\Installation;

class WipeInstallations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:wipe-installations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all installations and their findings & project relations.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $installations = Installation::all();
        foreach ($installations as $installation) {
            $installation->delete();
        }
    }
}
