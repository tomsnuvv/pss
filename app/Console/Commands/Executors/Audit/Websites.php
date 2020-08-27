<?php

namespace App\Console\Commands\Executors\Audit;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Website;
use App\Libs\Executors\Audit\Websites as Executor;

/**
 * Websites Audit Executor command.
 *
 * Executes all the available audit modules for websites.
 */
class Websites extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'audit:websites {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the audit modules for websites';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No websites found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Website::class;

    /**
     * {@inheritdoc}
     */
    protected $executor = Executor::class;

    /**
     * {@inheritdoc}
     */
    protected $field = 'url';

    /**
     * {@inheritdoc}
     */
    protected $title = 'Auditing Website';

    /**
     * {@inheritdoc}
     */
    protected function queryAll()
    {
        return $this->model::where('key', 1)->inRandomOrder();
    }
}
