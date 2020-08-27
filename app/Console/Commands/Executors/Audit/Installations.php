<?php

namespace App\Console\Commands\Executors\Audit;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Installation;
use App\Libs\Executors\Audit\Installations as Executor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Installations Audit Executor command.
 *
 * Executes all the available audit modules for installations.
 */
class Installations extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'audit:installations {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the audit modules for installations';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No installations found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Installation::class;

    /**
     * {@inheritdoc}
     */
    protected $executor = Executor::class;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        // Disable the query log, as it's a long process.
        DB::connection()->disableQueryLog();

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function showItem(Model $model)
    {
        $this->line(PHP_EOL . '<info> [+] Auditing Installation: </info><comment>' . $model->product->code . '</comment> ' . $model->version . ' (ID: ' . $model->id . ')');
        $this->line('');
    }

    /**
     * {@inheritdoc}
     */
    protected function queryAll()
    {
        return $this->model::with('product')->inRandomOrder();
    }
}
