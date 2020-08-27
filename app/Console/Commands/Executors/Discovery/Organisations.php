<?php

namespace App\Console\Commands\Executors\Discovery;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Organisation;
use App\Libs\Executors\Discovery\Organisations as Executor;

/**
 * Organisations Discovery Executor command.
 *
 * Executes all the available discovery modules for organisations.
 */
class Organisations extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'discovery:organisations {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the discovery modules for organisations';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No organisations found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Organisation::class;

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
    protected $title = 'Discovering Organisation';
}
