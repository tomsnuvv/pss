<?php

namespace App\Libs\Providers\Products\Javascript;

use GuzzleHttp\Client;

/**
 * Npmjs Provider.
 *
 * Interacts with Npmjs packages.
 */
class Npmjs
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
    const URL = 'https://www.npmjs.com/';

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
     * @return \stdClass|void
     */
    public function getPackage()
    {
        $html = $this->request('package/'.$this->code);
        if (!$html) {
            return;
        }

        preg_match('/window\.__context__ = {(.*?)}<\/script>/', $html, $matches, PREG_OFFSET_CAPTURE);
        if (isset($matches[1][0])) {
            return json_decode('{'.$matches[1][0].'}');
        }

        return;
    }

    /**
     * Perform a HTTP request.
     *
     * @param  string  $uri
     * @return mixed Response
     */
    private function request($uri = '')
    {
        try {
            $response = $this->client->request('GET', self::URL.$uri);

            return $response->getBody();
        } catch (\Exception $e) {
            # echo 'Error: ' . $e->getMessage() . PHP_EOL;
        }
    }
}
