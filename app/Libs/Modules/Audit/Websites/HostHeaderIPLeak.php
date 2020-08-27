<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * Websites Host Header IP Leak Audit Module.
 *
 * Determines if the web server leaks its internal IP address when sending an HTTP/1.0 request without a Host header.
 */
class HostHeaderIPLeak extends Audit
{
    use Http;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'HOST_HEADER_IP_LEAK';

    /**
     * The leaked IP.
     *
     * @var string
     */
    protected $ip;

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
        $this->request('GET', '', [
            'headers' => [
                'Host' => ''
            ],
            'allow_redirects' => false
        ]);
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
        if (!filter_var($header, FILTER_VALIDATE_IP)) {
            return false;
        }

        $this->ip = $header[0];

        if (!$this->model->hosts()->where('ip', $this->ip)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = 'curl -v -H "Host: " ' . $this->buildURL('') . PHP_EOL;
        $details .= 'Response: Location: ' . $this->ip . PHP_EOL;
        $hosts = $this->model->hosts()->where('ip', '<>', $this->ip)->pluck('ip');
        if (is_array($hosts) && !empty($hosts)) {
            $details .= 'Public IPs: ' . implode(', ', $hosts);
        }

        return $details;
    }
}
