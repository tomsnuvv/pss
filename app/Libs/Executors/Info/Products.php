<?php

namespace App\Libs\Executors\Info;

use App\Libs\Contracts\Executors\Abstracts\Executor as ExecutorAbstract;
use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;

/**
 * Products Info Executor.
 *
 * Executes all the Product Info modules to update the product's info,
 * for example, the lastest available version.
 */
class Products extends ExecutorAbstract implements ExecutorInterface
{
    /**
     * Modules.
     *
     * @var array
     */
    const MODULES = [
        \App\Libs\Modules\Info\Products\WordPress\WPAPI::class,
        \App\Libs\Modules\Info\Products\Composer\Packagist::class,
        \App\Libs\Modules\Info\Products\Jenkins\Plugins\Jenkins::class,
    ];

    /**
     * Relations modules.
     *
     * @var array
     */
    const MODULES_RELATIONS = [];
}
