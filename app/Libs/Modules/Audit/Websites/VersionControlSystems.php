<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Http as HttpTrait;

/**
 * Websites Version Control Systems Audit Module.
 *
 * Checks if the website is exposing GIT or SVN directories over HTTP.
 * https://github.com/bl4de/research/tree/master/hidden_directories_leaks
 */
class VersionControlSystems extends Audit
{
    use HttpTrait;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'EXPOSED_VCS';

    /**
     * Vulnerable patterns.
     *
     * @var array
     */
    protected $patterns = [
        '.git/' => ['Index of'],
        '.git/HEAD' => ['refs/heads/'],
        '.git/config' => ['[core]', '[remote', '[branch'],
        '.git/objects' => ['Index of'],
        '.git/logs/HEAD' => ['clone:', 'checkout:', 'commit:', 'pull:'],
        '.svn/' => ['Index of'],
        '.svn/entries' => ['/^\d{1,2}\s?\ndir\n/'],
        '.svn/all-wcprops' => ['/^K\s\d{1,2}\s?\nsvn:/'],
    ];

    /**
     * Results.
     *
     * @var array
     */
    protected $results = [];

    /*
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->multipleRequest('GET', array_keys($this->patterns), ['allow_redirects' => false]);
        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        $paths = array_keys($this->patterns);

        foreach ($this->responses as $id => $response) {
            if (!isset($response['value'])) {
                continue;
            }
            $path = $paths[$id];
            if ($this->isValidResponse($path, $response['value'])) {
                $this->results[$path] = $response['value']->getStatusCode();
            }
        }

        return !empty($this->results);
    }

    /**
     * Check if the response is valid.
     *
     * @param  string $path
     * @param  \GuzzleHttp\Psr7\Response|null $response
     * @return bool|void
     */
    protected function isValidResponse($path, $response)
    {
        if (!$response || !$response->getStatusCode()) {
            return false;
        }

        if (substr($response->getStatusCode(), 0, 1) === 3) {
            return false;
        }

        if ($response->getStatusCode() === 404) {
            return false;
        }

        if ($response->getStatusCode() === 403) {
            return false;
        }

        $patterns = $this->patterns[$path];
        foreach ($patterns as $pattern) {
            // Regex pattern
            if ($pattern[0] == '/' && substr($pattern, -1) == '/') {
                if (preg_match($pattern, $response->getBody())) {
                    return true;
                }
                // Text search
            } else {
                if (strstr($response->getBody(), $pattern)) {
                    return true;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = 'Responses: ' . PHP_EOL . PHP_EOL . PHP_EOL . '```' . PHP_EOL;
        foreach ($this->results as $path => $code) {
            $details .= 'GET ' . $path . ' - ' . $code . PHP_EOL;
        }
        $details .= '```';

        return $details;
    }
}
