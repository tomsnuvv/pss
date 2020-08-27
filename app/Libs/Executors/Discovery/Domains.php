<?php

namespace App\Libs\Executors\Discovery;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Domains Discovery Executor.
 *
 * Executes all the Discovery modules for a given Domain.
 */
class Domains extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Discovery\Domains\Website::class,
        \App\Libs\Modules\Discovery\Domains\NameServers::class,
        \App\Libs\Modules\Discovery\Domains\DNS\DNSRecon::class,
        \App\Libs\Modules\Discovery\Domains\Whois::class,
        \App\Libs\Modules\Discovery\Domains\Host::class,
        \App\Libs\Modules\Discovery\Domains\Subdomains\Amass::class,
        \App\Libs\Modules\Discovery\Domains\Subdomains\OneForAll::class,
        \App\Libs\Modules\Discovery\Domains\Subdomains\Sonar::class,
        \App\Libs\Modules\Discovery\Domains\Subdomains\MassDNS::class,

        # Experimental
        #\App\Libs\Modules\Discovery\Domains\Requests\AlienVaultOTX::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
