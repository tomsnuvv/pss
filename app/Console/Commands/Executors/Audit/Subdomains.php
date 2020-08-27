<?php

namespace App\Console\Commands\Executors\Audit;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Domain;
use App\Libs\Executors\Audit\Subdomains as Executor;

/**
 * Subdomains Audit Executor command.
 *
 * Executes all the available audit modules for subdomains.
 */
class Subdomains extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'audit:subdomains {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the audit modules for subdomains';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No subdomains found.';

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
    protected $title = 'Auditing Subdomain';

    /**
     * {@inheritdoc}
     */
    protected function queryAll()
    {
        return $this->model::where('key', 1)->querySubdomains()->inRandomOrder();
    }
}
