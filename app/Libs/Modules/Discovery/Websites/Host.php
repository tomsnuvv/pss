<?php

namespace App\Libs\Modules\Discovery\Websites;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Hosts;
use App\Libs\Helpers\Websites;

/**
 * Host Websites Discovery Module.
 *
 * Obtains the Host from a website.
 */
class Host extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $host = Hosts::createServerFromIP($this->getIp());
        if (!$host) {
            return;
        }
        Websites::attachHost($this->model, $host);

        // Keep the key value
        if ($this->model->key) {
            $host->key = true;
            $host->save();
        }

        $this->outputDetail('IP', $host->ip);
    }

    /**
     * Get the website's IP.
     *
     * @return string
     */
    private function getIp()
    {
        $host = parse_url($this->model->url, PHP_URL_HOST);

        return Hosts::getIp($host);
    }
}
