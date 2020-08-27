<?php

namespace App\Libs\Modules\Audit\Websites\Requests;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Models\Request;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * Google Maps Audit Module.
 *
 * Audit Google Maps API keys in the website source.
 */
class GoogleMaps extends Audit
{
    use Http;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'UNRESTRICTED_API_KEY';

    /**
     * Search query to match requests in the database.
     *
     * @var string
     */
    const QUERY = [
        'maps.google.com',
        'maps.googleapis.com',
    ];

    /**
     * Regex pattern to find Google Maps Keys.
     *
     * @var string
     */
    const REGEX = '/maps\.(google|googleapis)\.com\/maps\/.*?key=(.*?)(&|\?|"|\')/';

    /**
     * API URLs to check.
     *
     * @var array
     */
    const APIS = [
        'staticmap' => 'https://maps.googleapis.com/maps/api/staticmap?center=45%2C10&zoom=7&size=400x400&key=',
        'streetview' => 'https://maps.googleapis.com/maps/api/streetview?size=400x400&location=40.720032,-73.988354&fov=90&heading=235&pitch=10&key=',
        'embed' => 'https://www.google.com/maps/embed/v1/search?q=record+stores+in+Seattle&key=',
        'directions' => 'https://maps.googleapis.com/maps/api/directions/json?origin=Disneyland&destination=Universal+Studios+Hollywood4&key=',
        'geocode' => 'https://maps.googleapis.com/maps/api/geocode/json?latlng=40,30&key=',
        'distance_matrix' => 'https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=40.6655101,-73.89188969999998&destinations=40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.6905615%2C-73.9976592%7C40.659569%2C-73.933783%7C40.729029%2C-73.851524%7C40.6860072%2C-73.6334271%7C40.598566%2C-73.7527626%7C40.659569%2C-73.933783%7C40.729029%2C-73.851524%7C40.6860072%2C-73.6334271%7C40.598566%2C-73.7527626&key=',
        'find_place' => 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json?input=Museum%20of%20Contemporary%20Art%20Australia&inputtype=textquery&fields=photos,formatted_address,name,rating,opening_hours,geometry&key=',
        'autocomplete' => 'https://maps.googleapis.com/maps/api/place/autocomplete/json?input=Bingh&types=%28cities%29&key=',
        'elevation' => 'https://maps.googleapis.com/maps/api/elevation/json?locations=39.7391536,-104.9847034&key=',
        'timezone' => 'https://maps.googleapis.com/maps/api/timezone/json?location=39.6034810,-119.6822510&timestamp=1331161200&key=',
        'roads' => 'https://roads.googleapis.com/v1/nearestRoads?points=60.170880,24.942795|60.170879,24.942796|60.170877,24.942796&key=',
        'geolocate' => 'https://www.googleapis.com/geolocation/v1/geolocate?key=',
    ];

    /**
     * Referer.
     *
     * @var string
     */
    const REFERER = 'https://evil.com/';

    /**
     * Requests that matches the search.
     *
     * @var array
     */
    private $requests = [];

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->requests = $this->model->requests()->with('content')->whereHas('content', function ($query) {
            foreach (self::QUERY as $i => $string) {
                if ($i == 0) {
                    $query->where('body', 'LIKE', '%' . $string . '%');
                } else {
                    $query->orWhere('body', 'LIKE', '%' . $string . '%');
                }
            }
        })->get();
    }

    /*
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        if (empty($this->requests)) {
            $this->setMessage('No requests matched');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildURL($uri)
    {
        return $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $keys = [];
        foreach ($this->requests as $request) {
            preg_match(self::REGEX, $request->content->body, $matches);
            if (!isset($matches[2])) {
                continue;
            }
            $keys[] = $matches[2];
        }

        if (!empty($keys)) {
            $keys = array_unique($keys);
            foreach ($keys as $key) {
                $this->checkKey($key);
            }
        }

        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        return !empty($this->results);
    }

    /**
     * Performs API requests using the key.
     *
     * @param  string $key
     */
    private function checkKey($key)
    {
        $this->outputDetail('Auditing key', $key);
        foreach (self::APIS as $name => $url) {
            $text = null;
            $url = $url . $key;
            if ($name == 'geolocate') {
                $this->request('POST', $url, [
                    'form_params' => [
                        'considerIp' => true
                    ],
                    'headers' => [
                        'Referer' => self::REFERER
                    ]
                ]);
                $text = '(POST request)';
            } else {
                $this->request('GET', $url, ['headers' => ['Referer' => self::REFERER]]);
            }

            if ($this->isSuccess() && !strstr($this->response->getBody(), 'error')) {
                $this->results[$key][] = [
                    'api' => $name,
                    'url' => $url,
                    'text' => $text,
                ];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = 'Vulnerable keys / APIs found: ' . PHP_EOL. PHP_EOL;

        foreach ($this->results as $key => $results) {
            $details .= '**Key**: ' . $key . PHP_EOL. PHP_EOL;
            foreach ($results as $result) {
                if ($result['text']) {
                    $details .= '* ' . $result['api'] . ' ' . $result['text'] . PHP_EOL;
                } else {
                    $details .= '* [' . $result['api'] . '](' . $result['url'] . ')' . PHP_EOL;
                }
            }
        }

        return $details;
    }
}
