<?php

namespace App\Console\Commands\Tools\Clean;

use Illuminate\Console\Command;
use App\Models\Certificate;
use App\Models\ModuleLog;
use App\Models\RequestContent;
use App\Models\Finding;
use App\Models\Installation;
use App\Models\Vendor;
use App\Models\Website;
use App\Models\Port;

class CleanOrphans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:clean-orphans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all orphan models';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Findings of non existing models or non-key models (except repositories)
        Finding::with('target')->chunk(100, function ($findings) {
            foreach ($findings as $finding) {
                if (!$finding->target || (get_class($finding->target) !== 'App\\Models\\Repository' && (!isset($finding->target->key) || !$finding->target->key))) {
                    $finding->delete();
                }
            }
        });

        // Ports of non-key hosts
        $ports = Port::whereHas('host', function ($query) {
            $query->where('key', 0);
        })->get();
        foreach ($ports as $port) {
            $port->delete();
        }

        // Installations of non existing models
        Installation::with('source')->chunk(100, function ($installations) {
            foreach ($installations as $installation) {
                if (!$installation->source) {
                    $installation->delete();
                }
            }
        });

        // Certificates that doesnt belong to any domain / host
        Certificate::whereDoesntHave('domains')->whereDoesntHave('hosts')->delete();

        // Module logs of non existing models
        ModuleLog::with('model')->whereNotNull('model_id')->chunk(100, function ($moduleLogs) {
            foreach ($moduleLogs as $moduleLog) {
                if (!$moduleLog->model) {
                    $moduleLog->delete();
                }
            }
        });

        // Vendors without products
        Vendor::chunk(100, function ($vendors) {
            foreach ($vendors as $vendor) {
                if ($vendor->products()->count() == 0) {
                    $vendor->delete();
                }
            }
        });

        // Request Content not used in any Request
        RequestContent::whereDoesntHave('requests')->delete();
    }
}
