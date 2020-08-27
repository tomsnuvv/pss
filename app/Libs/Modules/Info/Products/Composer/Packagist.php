<?php

namespace App\Libs\Modules\Info\Products\Composer;

use App\Libs\Contracts\Modules\Abstracts\Info;
use App\Libs\Providers\Products\Composer\Packagist as Provider;

/**
 * Packagist Product Info Module.
 *
 * Grabs available Product information from Packagist.
 * Uses the Packagist Provider.
 *
 * @todo Implement cache
 */
class Packagist extends Info
{
    /**
     * Strings to avoid in the latest version parser.
     *
     * @var array
     */
    const VERSION_BLACKLIST = [
        'dev',
    ];

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        return $this->model->type && $this->model->type->isComposer();
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $provider = new Provider($this->model->code);
        $json = $provider->getPackage();

        if (!$this->isValidResponse($json)) {
            if (isset($json->message)) {
                $this->output('<error>' . $json->message . '</error>');
            }
            return;
        }

        unset($provider);

        $this->model->latest_version = $this->parseLatestVersion($json);
        $this->model->description = $json->package->description;
        $this->model->website = $json->package->repository;
        $this->model->source = $json->package->repository;
        $this->model->save();

        unset($json);
    }

    /**
     * Check if the Provider response is valid.
     *
     * @param mixed $response
     *
     * @return bool
     */
    private function isValidResponse($response)
    {
        return is_object($response) && isset($response->package);
    }

    /**
     * Parses the product versions, returning the latest one.
     *
     * @param \stdClass $json
     *
     * @return string|void
     */
    private function parseLatestVersion($json)
    {
        if (!count((array) $json->package->versions)) {
            return null;
        }

        foreach ($json->package->versions as $version => $data) {
            if ($this->allowedVersion($version)) {
                return $version;
            }
        }
    }

    /**
     * Cheks if version is allowed.
     *
     * @param string $version
     *
     * @return bool
     */
    private function allowedVersion($version)
    {
        foreach (self::VERSION_BLACKLIST as $blacklist) {
            if (strstr($version, $blacklist)) {
                return false;
            }
        }

        return true;
    }
}
