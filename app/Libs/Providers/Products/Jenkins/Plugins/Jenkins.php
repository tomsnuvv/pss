<?php

namespace App\Libs\Providers\Products\Jenkins\Plugins;

use App\Models\ProductLicense;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Jenkins Provider.
 *
 * Interacts with plugins.jenkins.io.
 */
class Jenkins
{
    /**
     * HTTP Client
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Plugin Code.
     *
     * @var array
     */
    public $code;

    /**
     * URL
     *
     * @var string
     */
    const URL = 'https://plugins.jenkins.io/';

    /**
     * @param  string|null  $code
     */
    public function __construct($code = null)
    {
        $this->code = $code;
        $this->client = new Client();
    }

    /**
     * Get the plugin information.
     *
     * @return array|void
     */
    public function getInfo()
    {
        $html = $this->request($this->code);
        if (!$html) {
            return;
        }

        $crawler = new Crawler((string) $html);

        return [
            'latest_version' => $this->getLatestVersion($crawler),
            'website' => self::URL.$this->code,
            'description' => $this->getDescription($crawler),
            'name' => $this->getName($crawler),
            'source' => $this->getSource($crawler),
            'license_id' => ProductLicense::free()->first()->id,
        ];
    }

    /**
     * Get the plugin name.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return string|void
     */
    private function getName($crawler)
    {
        try {
            $name = $crawler->filter('h1.title')->html();
            preg_match('/>(.*?)</', $name, $matches);

            return isset($matches[1]) ? $matches[1] : null;
        } catch (\Exception $e) {
            // Node list is empty
        }
    }

    /**
     * Get the latest version.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return string|void
     */
    private function getLatestVersion($crawler)
    {
        try {
            return $crawler->filter('h1.title span.v')->text();
        } catch (\Exception $e) {
            // Node list is empty
        }
    }

    /**
     * Get the decription.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return string|void
     */
    private function getDescription($crawler)
    {
        try {
            return $crawler->filter('div.content > p > span')->text();
        } catch (\Exception $e) {
            // Node list is empty
        }
    }

    /**
     * Get the source.
     *
     * @param  \Symfony\Component\DomCrawler\Crawler  $crawler
     * @return string|void
     */
    private function getSource($crawler)
    {
        try {
            return $crawler->selectLink("GitHub →")->link()->getUri();
        } catch (\Exception $e) {
            // Node list is empty
        }
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
            #echo self::URL . $uri . PHP_EOL;
            $response = $this->client->request('GET', self::URL.$uri);

            return $response->getBody();
        } catch (\Exception $e) {
            #echo 'Error: ' . $e->getMessage() . PHP_EOL;
            return;
        }
    }
}
