<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Websites Discovery Executor.
 *
 * Executes all the Discovery modules for a given Website.
 */
class Websites extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Discovery\Websites\Domains::class,
        \App\Libs\Modules\Discovery\Websites\Certificate::class,
        \App\Libs\Modules\Discovery\Websites\Status::class,
        \App\Libs\Modules\Discovery\Websites\Browsershot::class,
        \App\Libs\Modules\Discovery\Websites\Requests\Crawler::class,
        \App\Libs\Modules\Discovery\Websites\Requests\Contents::class,
        \App\Libs\Modules\Discovery\Websites\Host::class,
        \App\Libs\Modules\Discovery\Websites\Headers::class,
        \App\Libs\Modules\Discovery\Websites\Products\WhatWeb::class,
        \App\Libs\Modules\Discovery\Websites\Products\WordPress\WPPSS::class,
        \App\Libs\Modules\Discovery\Websites\Products\Jenkins\JPSS::class,

        # Experimental
        #\App\Libs\Modules\Discovery\Websites\Requests\Archive::class,
        #\App\Libs\Modules\Discovery\Websites\Requests\CommonCrawl::class,
        #\App\Libs\Modules\Discovery\Websites\Products\WordPress\WPScan::class,

    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
