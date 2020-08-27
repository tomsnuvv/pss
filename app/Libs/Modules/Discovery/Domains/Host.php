<?php

namespace App\Libs\Modules\Discovery\Domains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Hosts;

/**
 * Domains Host Discovery Module.
 *
 * Obtains the Host from a Domain.
 */
class Host extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $host = Hosts::createServerFromDomain($this->model);
        if ($host) {
            if ($host->wasRecentlyCreated) {
                $host->key = $this->model->key;
                $host->save();
            }
            $this->items[] = $host;
            $this->outputDetail('IP', $host->ip);
        }
    }
}
