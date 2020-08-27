<?php

namespace App\Libs\Modules\Info\Products\Javascript;

use App\Libs\Contracts\Modules\Abstracts\Info;
use App\Libs\Providers\Products\Javascript\Npmjs as Provider;

/**
 * Npmjs Product Info Module.
 *
 * Grabs available Product information from npmjs.com.
 * Uses the Npmjs Provider.
 *
 * @todo Implement cache
 */
class Npmjs extends Info
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
        $provider = new Provider($this->model->code);
        $json = $provider->getPackage();

        if (!$this->isValidResponse($json)) {
            $this->outputError('Unknown response');
            return;
        }

        unset($provider);

        $this->model->latest_version = isset($json->context->packageVersion->version) ? $json->context->packageVersion->version : null;
        $this->model->description = isset($json->context->packageVersion->description) ? $json->context->packageVersion->description : null;
        $this->model->website = isset($json->context->packageVersion->homepage) ? $json->context->packageVersion->homepage : null;
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
        return is_object($response) && isset($response->context);
    }
}
