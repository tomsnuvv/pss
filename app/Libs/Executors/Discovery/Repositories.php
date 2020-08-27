<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Repositories Discovery Executor.
 *
 * Executes all the Discovery modules for a given Repository.
 */
class Repositories extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Discovery\Repositories\Products\Composer::class,
        \App\Libs\Modules\Discovery\Repositories\Products\Yarn::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
