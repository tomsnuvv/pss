<?php

namespace App\Libs\Providers\Products\WordPress;

use Carbon\Carbon;
use GuzzleHttp\Client;

/**
 * WordPress API Provider.
 *
 * Interacts with WordPress API to request Product's information.
 * Allows to search Plugins & Themes by it's code, and obtain the latest WP version available.
 * https://developer.wordpress.org/rest-api/
 */
class WPAPI
{
    /**
     * API Endpoint.
     *
     * @var string
     */
    const URL = 'https://api.wordpress.org/';

    /**
     * Http Requests timeout.
     *
     * @var int
     */
    const TIMEOUT = 1;

    /**
     * HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Gets the latest WordPress version available.
     *
     * @return string
     */
    public function getLatestWordPressVersion()
    {
        if (!$res = $this->request('core/version-check/1.7/')) {
            return;
        }
        $latest = head($res->offers);

        return $latest->version;
    }

    /**
     * Search a Wordpress plugin.
     *
     * @param string $code Plugin code
     *
     * @return \stdClass|null JSON Response
     */
    public function searchPlugin($code)
    {
        $response = $this->request('plugins/info/1.0/' . $code . '.json');
        if ($response) {
            return $this->parseData($response);
        }
    }

    /**
     * Search a Wordpress theme.
     *
     * @param string $code Theme code
     *
     * @return \stdClass|null JSON Response
     */
    public function searchTheme($code)
    {
        $response = $this->request('themes/info/1.0/' . $code . '.json');
        if ($response) {
            return $this->parseData($response, 'theme');
        }
    }

    /**
     * Checks if the request response is valid.
     *
     * @param mixed $response
     *
     * @return bool
     */
    private function isValidResponse($response)
    {
        return is_object($response) && !isset($response->error);
    }

    /**
     * Parses the API data result.
     *
     * @param object $response WP API JSON response
     * @param string $type     Product type
     *
     * @return array
     */
    private function parseData($response, $type = 'plugin')
    {
        $data = (array)$response;
        $data['vendor'] = html_entity_decode(strip_tags($response->author));
        $data['product'] = html_entity_decode($response->name);
        $data['code'] = $response->slug;
        $data['date'] = isset($response->added) ? $response->added : null;
        $data['last_update'] = isset($response->last_updated) ? Carbon::parse($response->last_updated) : null;
        $data['description'] = isset($response->sections->description) ? html_entity_decode($response->sections->description) : null;
        $data['changelog'] = isset($response->sections->changelog) ? html_entity_decode($response->sections->changelog) : null;
        $data['installation'] = isset($response->sections->installation) ? html_entity_decode($response->sections->installation) : null;
        $data['latest_version'] = isset($response->version) ? $response->version : null;
        if (isset($response->homepage)) {
            $data['website'] = $response->homepage;
        } else {
            $data['website'] = 'https://wordpress.org/' . $type . '/' . $response->slug;
        }

        return $data;
    }

    /**
     * Perform a HTTP request.
     *
     * @param string $uri
     *
     * @return mixed Response
     */
    private function request($uri = '')
    {
        try {
            $response = $this->client->request('GET', self::URL . $uri, $this->buildParameters());

            $content = json_decode($response->getBody());

            if ($this->isValidResponse($content)) {
                return $content;
            }
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Build the request parameters.
     *
     * @param  array $params
     * @return array
     */
    private function buildParameters($params = [])
    {
        return array_merge([
            'timeout' => self::TIMEOUT,
        ], $params);
    }
}
