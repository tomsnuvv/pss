<?php

namespace App\Libs\Providers\Products\Composer;

use GuzzleHttp\Client;

/**
 * Packagist Provider.
 *
 * Interacts with Packagist packages.
 */
class Packagist
{
    /**
     * HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Package Code.
     *
     * @var array
     */
    public $code;

    /**
     * Packagist URL
     *
     * @var string
     */
    const URL = 'https://packagist.org/';

    /**
     * @param  string|null  $code
     */
    public function __construct($code = null)
    {
        $this->code = $code;
        $this->client = new Client();
    }

    /**
     * Get the package information in json format.
     *
     * @return \stdClass Response in JSON object
     */
    public function getPackage()
    {
        return $this->request('packages/'.$this->code.'.json');
    }

    /**
     * Perform a HTTP request.
     *
     * @param  string  $uri
     * @return mixed|void Response
     */
    private function request($uri = '')
    {
        try {
            $response = $this->client->request('GET', self::URL.$uri);

            return json_decode($response->getBody());
        } catch (\Exception $e) {

        }
    }
}
