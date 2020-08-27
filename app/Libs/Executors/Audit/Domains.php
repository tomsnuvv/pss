<?php

namespace App\Libs\Executors\Audit;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Domains Audit Executor.
 *
 * Executes all the Audit modules for a given Domain.
 */
class Domains extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Audit\Domains\Whois\Expiration::class,
        \App\Libs\Modules\Audit\Domains\Certificate\Expiration::class,
        \App\Libs\Modules\Audit\Domains\Certificate\TestSSL::class,
        \App\Libs\Modules\Audit\Domains\DNS\ZoneTransfer::class,
        \App\Libs\Modules\Audit\Domains\Email\DMARC::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
