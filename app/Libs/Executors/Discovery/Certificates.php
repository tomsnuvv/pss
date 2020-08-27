<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Certificates Discovery Executor.
 *
 * Executes all the Discovery modules for a given Website.
 */
class Certificates extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Discovery\Certificates\Domain::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
