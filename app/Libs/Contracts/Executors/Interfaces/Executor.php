<?php

namespace App\Libs\Contracts\Executors\Interfaces;

/**
 * Executor Interface.
 */
interface Executor
{
    /**
     * Run all the executor Modules.
     *
     * @return array
     */
    public function run();
}
