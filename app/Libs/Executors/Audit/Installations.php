<?php

namespace App\Libs\Executors\Audit;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Installations Audit Executor.
 *
 * Executes all the Audit modules for a given Installation.
 */
class Installations extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Audit\Installations\Vulnerabilities::class,
        #\App\Libs\Modules\Audit\Installations\Outdated::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
