<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * Websites WAF Audit Module.
 *
 * Checks if the website is running a Web Application Firewall.
 */
class WAF extends Audit
{
    use Http;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'WAF_MISSING';

    /**
     * URI Payload.
     *
     * @var string
     */
    const PAYLOAD = '/?pwd=../../etc';

    /*
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        if ($this->model->environment->isTest()) {
            $this->setMessage('Website is in test environment');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->request('GET', self::PAYLOAD);
        if (!$this->response) {
            return;
        }

        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        return $this->response->getStatusCode() !== 401 &&
               $this->response->getStatusCode() !== 403 &&
               $this->response->getStatusCode() !== 406;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = '`GET ' . self::PAYLOAD . '`' . PHP_EOL . PHP_EOL;
        $details .= 'Response code: ' . $this->response->getStatusCode() . PHP_EOL . PHP_EOL;
        $details .= '[Reproduce](' . $this->buildURL(self::PAYLOAD) . ')';

        return $details;
    }
}
