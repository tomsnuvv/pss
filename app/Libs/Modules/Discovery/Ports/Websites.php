<?php

namespace App\Libs\Modules\Discovery\Ports;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Websites as WebsitesHelper;
use App\Libs\Contracts\Modules\Traits\Http;
use App\Libs\Helpers\Domains;
use App\Models\Domain;

/**
 * Ports Websites Discovery Module.
 *
 * Obtains Websites from Ports.
 */
class Websites extends Module
{
    use Http;

    /**
     * Indicates if the module is re-trying the request.
     * Avoids infinite loops.
     *
     * @var bool
     */
    protected $retry = false;

    /**
     * Ignore non-success HTTP status codes.
     *
     * @var bool
     */
    private $onlySuccess = false;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        if (!$this->model->host->key) {
            $this->setMessage('Port is not in a key host');
            return false;
        }

        if (!strstr($this->model->service, 'http')) {
            $this->setMessage('Not a http/s service');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildURL()
    {
        return $this->url;
    }

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->generateURL();
    }

    /**
     * Generate the URL.
     *
     * @param string $service
     */
    protected function generateURL($service = null)
    {
        $host = $this->model->host->name ?: $this->model->host->ip;

        if (!$service) {
            $service = 'http';
            if (strstr($this->model->service, 'https')) {
                $service .= 's';
            }
        }

        $url = $service . '://' . $host;

        if (!in_array($this->model->port, [80, 443])) {
            $url .= ':' . $this->model->port;
        }

        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $url = $this->getLastUrl();
        if (!$this->response || !$this->response->getStatusCode()) {
            return;
        }

        if (!$this->retry && $this->response->getStatusCode() == 400) {
            // HTTP request to HTTPS port
            if (strstr($this->response->getBody(), "HTTP request was sent to HTTPS")) {
                $this->retry = true;
                $this->generateURL('https');
                return $this->run();
            }
        }

        if ($this->onlySuccess && !$this->isSuccess()) {
            return;
        }

        $website = WebsitesHelper::createWebsite($url, null);
        WebsitesHelper::attachHost($website, $this->model->host);
        $website->status = $this->response->getStatusCode();

        if (filter_var($url, FILTER_VALIDATE_IP)) {
            $website->key = $this->model->host->key;
        } else {
            // Key only if top domain is key
            $domain = parse_url($url, PHP_URL_HOST);
            $topDomain = Domains::getTopLevelDomain($domain);
            if (Domain::where('name', $topDomain)->where('key', 1)->exists()) {
                $website->key = $this->model->host->key;
            }
        }
        $website->save();

        $this->outputDetail('Website', $website->url);
        $this->items[] = $website;
    }
}
