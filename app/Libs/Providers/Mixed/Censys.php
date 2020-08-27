<?php

namespace App\Libs\Providers\Mixed;

use GuzzleHttp\Client;

/**
 * Censys.io Provider.
 *
 * Interacts with Censys.io API.
 */
class Censys
{
    /**
     * HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * API username.
     *
     * @var string
     */
    protected $username;

    /**
     * API password.
     *
     * @var string
     */
    protected $password;

    /**
     * Packagist URL
     *
     * @var string
     */
    const URL = 'https://censys.io/api/v1/';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->client = new Client();

        $credentials = env('CENSYS_CREDENTIALS');
        if (!$credentials) {
            return;
        }
        $credentials = explode(':', $credentials);
        if (isset($credentials[0]) && isset($credentials[1])) {
            $this->username = $credentials[0];
            $this->password = $credentials[1];
        }
    }

    /**
     * Search.
     *
     * @param  string  $type
     * @param  string  $query
     * @param  int  $page
     * @param  array  $fields
     * @return object|void
     */
    public function search($type, $query, $page = 1, $fields = [])
    {
        return $this->request('search/'.$type, [
            'query' => $query,
            'page' => $page,
            'fields' => $fields,
        ]);
    }

    /**
     * Perform a HTTP request.
     *
     * @param  string  $uri
     * @param  array  $data
     * @return object|void
     */
    private function request($uri = '', $data)
    {
        try {
            $response = $this->client->request('POST', self::URL.$uri, [
                'auth' => [$this->username, $this->password],
                'json' => $data,
            ]);

            return json_decode($response->getBody());
        } catch (\Exception $e) {
            return;
        }
    }
}
