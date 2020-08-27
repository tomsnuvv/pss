<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * Websites Host Header Injection Audit Module.
 *
 * Checks if the website is vulnerable to Host Header Injection attacks.
 */
class HostHeaderInjection extends Audit
{
    use Http;

    /**
     * Example Host to inject.
     *
     * @var string
     */
    const HOST = 'google.com';

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'HOST_HEADER_INJECTION';

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
     * Builds an URL based on the website's URL.
     *
     * Removes the schema, as the vulnerability might reside
     * in the initial protocol redirection.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function buildURL($uri = '')
    {
        return preg_replace('#^https?://#', '', $this->model->url . '/' . $uri);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->request('GET', '', ['headers' => ['Host' => self::HOST], 'allow_redirects' => false]);
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
        if (!$this->response->hasHeader('location')) {
            return false;
        }
        $header = $this->response->getHeader('location');
        if (strstr($header[0], self::HOST)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = 'curl -v -H "Host: '.self::HOST.'" ' . $this->buildURL('') . PHP_EOL;
        $details .= 'Response: Location: ' . $this->response->getHeader('location')[0];

        return $details;
    }
}
