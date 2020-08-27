<?php

namespace App\Libs\Modules\Audit\Domains\DNS;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use \App\Libs\Contracts\Modules\Traits\Process;

/**
 * DNS Zone Transfer Domains Audit Module.
 *
 * Tries to perform DNS Zone Transfer attack on a domain.
 * https://digi.ninja/projects/zonetransferme.php
 */
class ZoneTransfer extends Audit
{
    use Process;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'DNS_ZONE_TRASNFER';

    /**
     * Process results.
     *
     * @var array
     */
    protected $results = [];

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        if ($this->model->parent()->exists()) {
            $this->setMessage('Not a top level domain');
            return false;
        }

        if (!$this->model->nameservers->count()) {
            $this->setMessage('No nameservers found.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        foreach ($this->model->nameservers as $nameserver) {
            try {
                $this->results[] = $this->runProcess(['dig', 'axfr', $this->model->name, '@' . $nameserver->name]);
            } catch (\Exception $e) {
                if (strstr($e->getMessage(), 'timed out')) {
                    $this->setMessage('Connection timed out');
                    continue;
                } else {
                    throw $e;
                }
            }
        }

        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        foreach ($this->results as $result) {
            if (!strstr($result, 'Transfer failed')) {
                return true;
            }
        }

        return false;
    }
}
