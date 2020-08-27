<?php

namespace App\Console\Commands\Executors\Discovery;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Certificate;
use App\Libs\Executors\Discovery\Certificates as Executor;

/**
 * Certificates Discovery Executor command.
 *
 * Executes all the available discovery modules for domains.
 */
class Certificates extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'discovery:certificates {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the discovery modules for certificates';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No certificates found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Certificate::class;

    /**
     * {@inheritdoc}
     */
    protected $executor = Executor::class;

    /**
     * {@inheritdoc}
     */
    protected $field = 'subject_common_name';

    /**
     * {@inheritdoc}
     */
    protected $title = 'Discovering Certificate';
}
