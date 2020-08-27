<?php

namespace App\Console\Commands\Executors\Discovery;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Website;
use App\Libs\Executors\Discovery\Websites as Executor;

/**
 * Websites Discovery Executor command.
 *
 * Executes all the available discovery modules for websites.
 */
class Websites extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'discovery:websites {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the discovery modules for websites';

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
    protected $title = 'Discovering Website';

    /**
     * {@inheritdoc}
     */
    protected function queryAll()
    {
        return $this->model::where('key', 1)->inRandomOrder();
    }
}
