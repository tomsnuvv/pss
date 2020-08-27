<?php

namespace App\Libs\Modules\Info\Products\Javascript;

use App\Libs\Contracts\Modules\Abstracts\Info;
use App\Libs\Helpers\Products;

use App\Libs\Providers\Products\Javascript\Yarnpkg as Provider;

/**
 * Yarnpkg Product Info Module.
 *
 * Grabs available Product information from yarnpkg.com.
 * Uses the Yarnpkg Provider.
 *
 * @todo Implement cache
 */
class Yarnpkg extends Info
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        return $this->model->type && $this->model->type->isJavascript();
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $code = $this->model->code;
        if ($this->model->vendor) {
            $code = $this->model->vendor->name . '/' . $code;
        }

        $provider = new Provider($code);
        $json = $provider->getPackage();

        if (!$this->isValidResponse($json)) {
            $this->outputError('Unknown response');
            return;
        }

        unset($provider);

        $dist = 'dist-tags';
        $version = null;
        if (isset($json->$dist->latest)) {
            $version = $json->$dist->latest;
        }

        $this->model->latest_version = $version;
        $this->model->description = isset($json->description) ? $json->description : null;
        $this->model->website = isset($json->homepage) ? $json->homepage : null;
        $this->model->source = isset($json->repository->url) ? $json->repository->url : null;
        if (!$this->model->vendor) {
            $this->findVendor();
        }
        $this->model->save();

        unset($json);
    }

    /**
     * Tries to find the product vendor from the source repository.
     */
    private function findVendor()
    {
        if ($this->model->source && stristr($this->model->source, 'git')) {
            preg_match_all('/\/([a-zA-Z0-9-_\.]+)\/([a-zA-Z0-9-_\.]+)\.git/', $this->model->source, $matches);
            if (isset($matches[1][0])) {
                $vendor = Products::createVendor($matches[1][0]);
                $this->model->vendor()->associate($vendor);
            }
        }
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
        return is_object($response);
    }
}
