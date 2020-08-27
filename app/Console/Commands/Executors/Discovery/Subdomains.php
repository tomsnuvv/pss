<?php

namespace App\Console\Commands\Executors\Discovery;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Domain;
use App\Libs\Executors\Discovery\Subdomains as Executor;

/**
 * Subdomains Discovery Executor command.
 *
 * Executes all the available discovery modules for subdomains.
 */
class Subdomains extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'discovery:subdomains {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the discovery modules for subdomains';

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
    protected $title = 'Discovering Subdomain';

    /**
     * Query all the items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function queryAll()
    {
        return $this->model::where('key', 1)->querySubdomains()->inRandomOrder();
    }
}
