<?php

namespace App\Libs\Contracts\Modules\Interfaces;

/**
 * Module interface.
 */
interface Module
{
    /**
     * Executes the module.
     *
     * @return array
     */
    public function execute();
}
