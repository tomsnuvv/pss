<?php

namespace App\Console\Commands\Executors\Discovery;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Host;
use App\Libs\Executors\Discovery\Hosts as Executor;

/**
 * Hosts Discovery Executor command.
 *
 * Executes all the available discovery modules for hosts.
 */
class Hosts extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'discovery:hosts {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the discovery modules for hosts';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No hosts found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Host::class;

    /**
     * {@inheritdoc}
     */
    protected $executor = Executor::class;

    /**
     * {@inheritdoc}
     */
    protected $field = 'ip';

    /**
     * {@inheritdoc}
     */
    protected $title = 'Discovering Host';

    /**
     * {@inheritdoc}
     */
    protected function queryAll()
    {
        return $this->model::where('key', 1)->inRandomOrder();
    }
}
