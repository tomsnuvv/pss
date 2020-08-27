<?php

namespace App\Console\Commands\Executors\Audit;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Repository;
use App\Libs\Executors\Audit\Repositories as Executor;

/**
 * Repositories Audit Executor command.
 *
 * Executes all the available audit modules for repositories.
 */
class Repositories extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'audit:repositories {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the audit modules for repositories';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No repositories found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Repository::class;

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
    protected $title = 'Auditing Repository';

    /**
     * {@inheritdoc}
     */
    protected function queryAll()
    {
        return $this->model::with('installations.product.vulnerabilities.versions')->inRandomOrder();
    }
}
