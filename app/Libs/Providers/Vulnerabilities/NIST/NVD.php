<?php

namespace App\Libs\Providers\Vulnerabilities\NIST;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

/**
 * NIST National Vulnerability Database Vulnerabilities Provider.
 *
 * Interacts with NIST National Vulnerability Database.
 * https://nvd.nist.gov/
 * https://nvd.nist.gov/vuln/data-feeds#JSON_FEED
 */
class NVD
{
    /**
     * API endpoint URL.
     *
     * @var string
     */
    const URL = 'https://nvd.nist.gov/feeds/json/cve/1.0/';

    /**
     * Download path
     *
     * @var string
     */
    const PATH = 'providers/vulnerabilities/NIST-NVD/';

    /**
     * Min. year
     *
     * @var string
     */
    const MIN_YEAR = 2002;

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

        Storage::makeDirectory(self::PATH);
    }

    /**
     * Gets all the vulnerabilities in the repository.
     *
     * If the json file already exists, it will return null
     * assuming that the vulnerabilities were already imported.
     *
     * @param  int  $year
     * @param  bool $forceDownload
     * @return array|void
     */
    public function getVulnerabilities($year, $forceDownload = false)
    {
        if ($year == 'recent') {
            $file = "nvdcve-1.0-recent.json.zip";
        } else {
            if ($year < self::MIN_YEAR) {
                return;
            }

            $file = 'nvdcve-1.0-'.$year.'.json.zip';
            $unziped = str_replace('.zip', '', $file);
            if (Storage::exists(self::PATH . $unziped) && !$forceDownload) {
                return;
            }
        }

        $file = $this->download($file);
        if (!$file) {
            return;
        }

        return $this->parseData($file);
    }

    /**
     * Parse a downloaded and unzipped feed.
     *
     * @param  string  $file
     * @return array
     */
    private function parseData($file)
    {
        $data = json_decode(Storage::get(self::PATH.$file));

        return $data->CVE_Items;
    }

    /**
     * Download a feed.
     *
     * @param  string  $file
     * @return mixed|void Response
     */
    private function download($file = '')
    {
        try {
            $unziped = str_replace('.zip', '', $file);
            $response = $this->client->request('GET', self::URL.$file);
            $content = $response->getBody();
            Storage::put(self::PATH.$file, $content);

            return $this->extractFile($file);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * Extract the JSON file from a downloaded ZIP.
     *
     * @param  string  $file
     * @return string
     */
    private function extractFile($file)
    {
        $unziped = str_replace('.zip', '', $file);

        $zip = new \ZipArchive;
        $res = $zip->open(storage_path('app/'.self::PATH.$file));
        $zip->extractTo(storage_path('app/'.self::PATH));
        $zip->close();
        Storage::delete(self::PATH.$file);

        return $unziped;
    }
}
