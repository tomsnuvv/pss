<?php

namespace App\Console\Commands\Executors\Discovery;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Repository;
use App\Libs\Executors\Discovery\Repositories as Executor;

/**
 * Repository Discovery Executor command.
 *
 * Executes all the available discovery modules for repositories.
 */
class Repositories extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'discovery:repositories {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the discovery modules for repositories';

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
    protected $title = 'Discovering Repository';
}
