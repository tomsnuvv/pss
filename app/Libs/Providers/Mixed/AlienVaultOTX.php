<?php

namespace App\Libs\Providers\Mixed;

use GuzzleHttp\Client;

/**
 * Alien Vault OTX Provider.
 *
 * Fetch data from AlienVault's Open Threat Exchange.
 * https://otx.alienvault.com/api
 */
class AlienVaultOTX
{
    /**
     * HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * API URL.
     *
     * @var string
     */
    const URL = 'https://otx.alienvault.com/';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Search Indicators.
     *
     * @param  string $indicator
     * @param  string $value
     * @param  string $section
     * @param  string $query     Query parameters
     * @return array
     */
    public function indicators($indicator, $value, $section, $query = [])
    {
        $results = [];

        $query['limit'] = 50;
        $query['page'] = 0;
        do {
            $response = $this->request('api/v1/indicators/' . $indicator . '/' . $value . '/' . $section, $query);

            if (isset($response->$section) && !empty($response->$section)) {
                foreach ($response->$section as $item) {
                    $results[] = $item;
                }
            }

            $query['page']++;
        } while ($response->has_next);

        return $results;
    }

    /**
     * Perform an API HTTP request.
     *
     * @param string $uri
     * @param array  $query
     * @return mixed Response
     */
    private function request($uri = '', $query = [])
    {
        $response = $this->client->request('GET', self::URL . $uri, ['query' => $query]);

        return json_decode($response->getBody());
    }
}
