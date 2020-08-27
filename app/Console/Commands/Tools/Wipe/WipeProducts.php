<?php

namespace App\Console\Commands\Tools\Wipe;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WipeProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:wipe-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete products, vendors, vulnerabilities...';

    /**
     * Deletable tables.
     *
     * @var array
     */
    const TABLES = [
        'installations', 'vulnerabilities', 'products', 'product_synonyms',
        'findings', 'module_logs', 'action_events', 'vendors',
        'vulnerability_details', 'vulnerability_types', 'vulnerability_versions',
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach (self::TABLES as $table) {
            DB::table($table)->truncate();
        }

        DB::delete("DELETE FROM projectables WHERE projectable_type = ?", ['App\\Models\\Installation']);
        DB::delete("DELETE FROM projectables WHERE projectable_type = ?", ['App\\Models\\Finding']);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
