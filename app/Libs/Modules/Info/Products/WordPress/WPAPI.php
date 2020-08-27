<?php

namespace App\Libs\Modules\Info\Products\WordPress;

use App\Libs\Contracts\Modules\Abstracts\Info;
use App\Libs\Providers\Products\WordPress\WPAPI as Provider;
use App\Libs\Helpers\Products;

/**
 * WPAPI Product Info Module.
 *
 * Grabs available Product information from WPAPI.
 * Uses the WPAPI Provider.
 *
 * @todo Implement cache
 */
class WPAPI extends Info
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        return $this->model->type && ($this->model->type->isWordPressPlugin() || $this->model->type->isWordPressTheme());
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $provider = new Provider();

        if ($this->model->type->isWordPressPlugin()) {
            $info = $provider->searchPlugin($this->model->code);
        } elseif ($this->model->type->isWordPressTheme()) {
            $info = $provider->searchTheme($this->model->code);
        }
        if (isset($info['vendor']) && $info['vendor']) {
            Products::createVendor($info['vendor'], $this->model);
        }
        if (is_array($info) && !empty($info)) {
            $this->model->fill($info);
            $this->model->save();
        }
    }
}
