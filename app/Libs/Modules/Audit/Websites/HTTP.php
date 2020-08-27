<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Http as HttpTrait;

/**
 * Websites HTTP Audit Module.
 *
 * Checks if the website is server over HTTP.
 */
class HTTP extends Audit
{
    use HttpTrait;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'HTTP';

    /*
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
        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        $url = str_replace('https://', 'http://', $this->model->url);
        $url = $this->getLastUrl($url);
        if (!$this->response || $this->message) {
            return false;
        }
        return !strstr($url, 'https://');
    }
}
