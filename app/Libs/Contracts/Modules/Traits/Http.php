<?php

namespace App\Libs\Contracts\Modules\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\TransferStats;

/**
 * HTTP Trait for Modules
 */
trait Http
{
    /**
     * HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Last client response
     *
     * @var \GuzzleHttp\Psr7\Response
     */
    protected $response;

    /**
     * Promises requests.
     *
     * Used by multiple asynchronous requests.
     *
     * @var \GuzzleHttp\Promise[]
     */
    protected $promises = [];

    /**
     * Multiple asynchronous requests responses.
     *
     * @var array[]
     */
    protected $responses = [];

    /**
     * Indicates if the client should verify the SSL certificates.
     *
     * @var bool
     */
    protected $verifySSL;

    /**
     * User Agent.
     *
     * @var string
     */
    protected $userAgent;

    /**
     * Builds an URL based on the website's URL
     *
     * @param string $uri
     *
     * @return string
     */
    protected function buildURL($uri = '')
    {
        return $this->model->url . '/' . $uri;
    }

    /**
     * Sets the HTTP client.
     */
    protected function setClient()
    {
        if ($this->client === null) {
            $this->client = new Client;
        }

        $this->verifySSL = config('pss.modules.http.verify-ssl');
        $this->userAgent = config('pss.modules.http.user-agent');
    }

    /**
     * Perform a HTTP request
     *
     * @param  string $verb
     * @param  string $uri
     * @param  array  $params
     * @return \GuzzleHttp\Psr7\Response|null
     */
    protected function request($verb, $uri = '', $params = [])
    {
        $this->setClient();

        try {
            $this->response = $this->client->request(
                $verb,
                $this->buildURL($uri),
                $this->buildParameters($params)
            );
        } catch (\Exception $e) {
            // $this->outputError($e->getMessage());
            $this->response = $e->getResponse();
        }

        return $this->response;
    }

    /**
     * Perform multiple asyncronous requests.
     *
     * @param  string $verb
     * @param  array  $uris
     * @param  array  $params
     * @return array|void
     */
    protected function multipleRequest($verb, $uris = [], $params = [])
    {
        $this->setClient();

        if (strtoupper($verb) == 'GET') {
            $method = 'getAsync';
        } elseif (strtoupper($verb) == 'POST') {
            $method = 'postAsync';
        }

        foreach ($uris as $i => $uri) {
            $p = isset($params[$i]) ? $params[$i] : [];
            $this->promises[] = $this->client->$method($this->buildURL($uri), $this->buildParameters($p));
        }

        // Wait for the requests to complete, even if some of them fail
        try {
            $this->responses = Promise\settle($this->promises)->wait();
        } catch (\Exception $e) {
            // $this->outputError($e->getMessage());
            return;
        }

        return $this->responses;
    }

    /**
     * Get the last effective URL.
     *
     * @param  string $url
     * @return string
     */
    protected function getLastUrl($url = null)
    {
        $this->setClient();

        if (!$url) {
            $url = $this->buildURL();
        }

        try {
            $response = $this->client->get($url, $this->buildParameters([
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }
            ]));
            $this->response = $response;
        } catch (\Exception $e) {
            // $this->outputError($e->getMessage());
            if (method_exists($e, 'getResponse')) {
                $this->response = $e->getResponse();
            }
        }

        return $url;
    }

    /**
     * Build the request parameters.
     *
     * @param  array $params
     * @return array
     */
    protected function buildParameters($params = [])
    {
        $headers = ['User-Agent' => $this->userAgent];
        if (isset($params['headers'])) {
            $headers = array_merge($params['headers'], $headers);
        }

        return array_merge([
            'headers' => $headers,
            'timeout' => $this->getTimeout(),
            'verify' => $this->verifySSL,
        ], $params);
    }

    /**
     * Checks if the response code is success.
     *
     * @return bool
     */
    protected function isSuccess()
    {
        return $this->response && in_array($this->response->getStatusCode(), [200, 202]);
    }

    /**
     * Gets the defined timeout.
     *
     * Default
     *
     * @return int
     */
    protected function getTimeout()
    {
        if (isset($this->timeout)) {
            return $this->timeout;
        }

        return config('pss.modules.http.timeout');
    }
}
