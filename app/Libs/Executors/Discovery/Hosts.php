<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Hosts Discovery Executor.
 *
 * Executes all the Discovery modules for a given Host.
 */
class Hosts extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Discovery\Hosts\Ports\Nmap::class,
        \App\Libs\Modules\Discovery\Hosts\Ports\Masscan::class,
        #\App\Libs\Modules\Discovery\Hosts\Domains\ReverseIPuk::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
