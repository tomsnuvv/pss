<?php

namespace App\Libs\Executors\Audit;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Websites Audit Executor.
 *
 * Executes all the Audit modules for a given Website.
 */
class Websites extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Audit\Websites\HTTP::class,
        \App\Libs\Modules\Audit\Websites\WAF::class,
        \App\Libs\Modules\Audit\Websites\Headers::class,
        \App\Libs\Modules\Audit\Websites\VersionControlSystems::class,
        \App\Libs\Modules\Audit\Websites\HostHeaderInjection::class,
        \App\Libs\Modules\Audit\Websites\HostHeaderIPLeak::class,
        \App\Libs\Modules\Audit\Websites\Requests\GoogleMaps::class,
        \App\Libs\Modules\Audit\Websites\Products\WordPress\XMLRPC::class,
        \App\Libs\Modules\Audit\Websites\Products\WordPress\Login\Unrestricted::class,
        \App\Libs\Modules\Audit\Websites\Products\WordPress\Enumeration\Author::class,
        \App\Libs\Modules\Audit\Websites\Products\WordPress\Enumeration\Feed::class,
        \App\Libs\Modules\Audit\Websites\Products\WordPress\Enumeration\Login::class,
        \App\Libs\Modules\Audit\Websites\Products\WordPress\Enumeration\WPAPI::class,
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
