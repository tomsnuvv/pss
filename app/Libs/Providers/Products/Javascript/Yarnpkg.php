<?php

namespace App\Libs\Providers\Products\Javascript;

use GuzzleHttp\Client;

/**
 * Yarnpkg Provider.
 *
 * Interacts with Yarn packages.
 */
class Yarnpkg
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
    const URL = 'https://registry.yarnpkg.com/';

    /**
     * @param string|null $code
     */
    public function __construct($code = null)
    {
        $this->code = $code;
        $this->client = new Client();
    }

    /**
     * Get the package information in json format.
     *
     * @return \stdClass|null
     */
    public function getPackage()
    {
        $content = $this->request($this->code);
        if (!$content) {
            return;
        }

        return json_decode($content);
    }

    /**
     * Perform a HTTP request.
     *
     * @param string $uri
     * @return mixed Response
     */
    private function request($uri = '')
    {
        try {
            $response = $this->client->request('GET', self::URL . $uri);

            return $response->getBody();
        } catch (\Exception $e) {
            # echo 'Error: ' . $e->getMessage() . PHP_EOL;
            return;
        }
    }
}
