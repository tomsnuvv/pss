<?php

namespace App\Console\Commands\Executors\Discovery;

use App\Console\Commands\Contracts\ExecutorCommand;
use App\Models\Port;
use App\Libs\Executors\Discovery\Ports as Executor;
use Illuminate\Database\Eloquent\Model;

/**
 * Ports Discovery Executor command.
 *
 * Executes all the available discovery modules for hosts.
 */
class Ports extends ExecutorCommand
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'discovery:ports {id?}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run all the discovery modules for ports';

    /**
     * {@inheritdoc}
     */
    protected $no_items_message = 'No ports found.';

    /**
     * {@inheritdoc}
     */
    protected $model = Port::class;

    /**
     * {@inheritdoc}
     */
    protected $executor = Executor::class;

    /**
     * {@inheritdoc}
     */
    protected function showItem(Model $model)
    {
        $this->line(PHP_EOL . '<info> [+] Discovering Port: </info><comment>' . $model->host->ip . ':' . $model->port . '</comment>');
        $this->line('');
    }
}
