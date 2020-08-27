<?php

namespace App\Libs\Modules\Info\Products\Jenkins\Plugins;

use App\Libs\Contracts\Modules\Abstracts\Info;
use App\Libs\Providers\Products\Jenkins\Plugins\Jenkins as Provider;

/**
 * Jenkins Plugins Product Info Module.
 *
 * Grabs available Product information from Jenkins.io.
 * Uses the Jenkins Plugins Provider.
 */
class Jenkins extends Info
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        return $this->model->type && $this->model->type->isJenkinsPlugin();
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $provider = new Provider($this->model->code);

        $info = $provider->getInfo();
        if (is_array($info) && !empty($info)) {
            $this->model->fill($info);
            $this->model->save();
        }
    }
}
