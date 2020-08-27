<?php

namespace App\Libs\Providers\Vulnerabilities\WordPress;

use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

/**
 * WPVulnDB WordPress Vulnerabilities Provider.
 *
 * Interacts with WP Vuln DB database API.
 * https://wpvulndb.com/api
 */
class WPVulnDB
{
    /**
     * API endpoint URL.
     *
     * @var string
     */
    const URL = 'https://wpvulndb.com/api/v3/';

    /**
     * Token.
     *
     * @var string
     */
    protected $token;

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
        $this->token = env('WPVULNDB_TOKEN');
    }

    /**
     * Gets the latest 20 vulnerabilities.
     *
     * @param  string $type All, plugins, themes, wordpresses
     * @return array
     */
    public function getLatest($type = 'all')
    {
        return $this->request($type . '/latest');
    }

    /**
     * Gets vulnerability details.
     *
     * @param  int   $id
     * @return array
     */
    public function getVulnerability($id)
    {
        return $this->request('vulnerabilities/' . $id);
    }

    /**
     * Perform a HTTP request.
     *
     * @param string $uri
     * @return mixed Response
     */
    private function request($uri = '')
    {
        $response = $this->client->request('GET', self::URL . $uri, [
            'headers' => [
                'Authorization' => 'Token token=' . $this->token,
            ]
        ]);
        $content = $response->getBody();

        return json_decode($content);
    }
}
