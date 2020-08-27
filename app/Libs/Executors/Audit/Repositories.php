<?php

namespace App\Libs\Executors\Audit;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Repositories Audit Executor.
 *
 * Executes all the Audit modules for a given Repository.
 */
class Repositories extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Audit\Repositories\Secrets\TruffleHog::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [
        'Installations' => [
            \App\Libs\Modules\Audit\Installations\Vulnerabilities::class,
        ],
    ];
}
