<?php

namespace App\Libs\Helpers;

use App\Models\Website;
use App\Models\Environment;
use App\Models\Product;
use App\Models\Module;
use App\Models\Host;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;

/**
 * Websites Helper class.
 *
 * @todo Implement cache + singeltone to avoid performing http calls when creating websites.
 */
class Websites
{
    /**
     * Create (if new) Website.
     *
     * @param  string                  $url
     * @param  \App\Models\Environment $environment
     * @param  bool                    $check
     * @return \App\Models\Website
     */
    public static function createWebsite($url, Environment $environment = null, $check = true)
    {
        if ($check) {
            $url = self::getFinalURL($url);
        }
        $url = self::cleanURL($url);

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return;
        }

        $website = Website::firstOrCreate(['url' => $url]);

        $environment = $environment ?: Environment::production()->first();
        $website->environment()->associate($environment);
        $website->save();

        return $website;
    }

    /**
     * Create (if new) an Installation model, related to a website.
     *
     * @param  \App\Models\Website      $website
     * @param  \App\Models\Product      $product
     * @param  string                   $version
     * @param  \App\Models\Module       $module
     * @return \App\Models\Installation
     */
    public static function installProduct(Website $website, Product $product, $version = null, Module $module)
    {
        $installation = $website->installations()->firstOrNew(['product_id' => $product->id]);
        if ($version !== null) {
            $installation->version = $version;
        }
        $installation->module()->associate($module);
        $installation->save();

        return $installation;
    }

    /**
     * Attach a Host into a Website model.
     *
     * @param  \App\Models\Website $website
     * @param  \App\Models\Host    $host
     */
    public static function attachHost(Website $website, Host $host)
    {
        $website->hosts()->syncWithoutDetaching([$host->id]);
    }

    /**
     * Get the correct url.
     *
     * Checks if a website is seved by HTTPS instead of HTTP.
     * Frequent typos from users.
     *
     * @param  string $url
     * @return string
     */
    public static function getFinalURL($url)
    {
        $client = new Client();
        try {
            $client->get($url, [
                'headers' => [
                    'User-Agent' => config('pss.modules.http.user-agent'),
                ],
                'timeout' => config('pss.modules.http.timeout'),
                'verify' => config('pss.modules.http.erify-ssl'),
                'on_stats' => function (TransferStats $stats) use (&$url) {
                    $url = $stats->getEffectiveUri();
                }
            ])->getBody()->getContents();
        } catch (\Exception $e) {
            // Do nothing
        }

        return $url;
    }

    /**
     * Cleans the URL.
     *
     * Removes anything except scheme and host.
     *
     * @param  string $url
     * @return string
     */
    public static function cleanURL($url)
    {
        $parts = parse_url($url);
        $url = $parts['scheme'] . "://" . $parts['host'];

        if (isset($parts['port'])) {
            $url .= ':' . $parts['port'];
        }

        return trim($url, '/');
    }
}
