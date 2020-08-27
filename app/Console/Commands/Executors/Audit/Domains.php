<?php

namespace App\Console\Commands\Executors\Audit;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Domain;
use App\Libs\Executors\Audit\Domains as Executor;

/**
 * Domains Audit Executor command.
 *
 * Executes all the available audit modules for domains.
 */
class Domains extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'audit:domains {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the audit modules for domains';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No domains found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Domain::class;

    /**
     * {@inheritdoc}
     */
    protected $executor = Executor::class;

    /**
     * {@inheritdoc}
     */
    protected $field = 'name';

    /**
     * {@inheritdoc}
     */
    protected $title = 'Auditing Domain';

    /**
     * {@inheritdoc}
     */
    protected function queryAll()
    {
        return $this->model::where('key', 1)->topLevel()->inRandomOrder();
    }
}
