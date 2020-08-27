<?php

namespace App\Libs\Providers\Mixed;

use GuzzleHttp\Client;

/**
 * Shodan.io Provider.
 *
 * Interacts with Shodan.io API.
 */
class Shodan
{
    /**
     * HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Token.
     *
     * @var string
     */
    protected $token;

    /**
     * Packagist URL
     *
     * @var string
     */
    const URL = 'https://api.shodan.io/';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
        $this->token = env('SHODAN_TOKEN');
    }

    /**
     * Search.
     *
     * @param  string $query
     * @return array
     */
    public function search($query)
    {
        return $this->request('shodan/host/search', ['query' => $query]);
    }

    /**
     * Perform a HTTP request.
     *
     * @param string $uri
     * @param array  $data
     * @return mixed Response
     */
    private function request($uri = '', $data = [])
    {
        $data = array_merge($data, ['key' => $this->token]);
        $response = $this->client->request('GET', self::URL . $uri, ['query' => $data]);

        return json_decode($response->getBody());
    }
}
