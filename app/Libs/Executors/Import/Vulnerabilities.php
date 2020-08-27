<?php

namespace App\Libs\Executors\Import;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Vulnerabilities Import Executor.
 *
 * Executes all the Vulnerabilities Import modules.
 */
class Vulnerabilities extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Import\Vulnerabilities\Javascript\Nodejs\SecurityWG::class,
        \App\Libs\Modules\Import\Vulnerabilities\Composer\SecurityAdvisories::class,
        \App\Libs\Modules\Import\Vulnerabilities\WordPress\WPVulnDB::class,
        \App\Libs\Modules\Import\Vulnerabilities\NIST\NVD::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
