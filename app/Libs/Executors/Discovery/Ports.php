<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Ports Discovery Executor.
 *
 * Executes all the Discovery modules for a given Port.
 */
class Ports extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Discovery\Ports\Websites::class,
        \App\Libs\Modules\Discovery\Ports\Installations\Nmap::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
