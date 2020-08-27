<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Organisations Discovery Executor.
 *
 * Executes all the Discovery modules for a given Organisation.
 */
class Organisations extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Discovery\Organisations\Hosts\Censys::class,
        \App\Libs\Modules\Discovery\Organisations\Hosts\Shodan::class,
        \App\Libs\Modules\Discovery\Organisations\Websites\Shodan::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
