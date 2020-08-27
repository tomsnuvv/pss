<?php

namespace App\Console\Commands\Executors\Discovery;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Domain;
use App\Libs\Executors\Discovery\Domains as Executor;

/**
 * Domains Discovery Executor command.
 *
 * Executes all the available discovery modules for domains.
 */
class Domains extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'discovery:domains {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the discovery modules for domains';

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
    protected $title = 'Discovering Domain';

    /**
     * Query all the items.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function queryAll()
    {
        return $this->model::where('key', 1)->topLevel();
    }
}
