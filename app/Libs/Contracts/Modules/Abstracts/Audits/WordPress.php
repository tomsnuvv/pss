<?php

namespace App\Libs\Contracts\Modules\Abstracts\Audits;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Helpers\Products;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * WordPress Audit Module abstract.
 */
abstract class WordPress extends Audit
{
    use Http;

    /**
     * WP Installation Model.
     *
     * @var \App\Models\Installation
     */
    protected $installation;

    /**
     * @inheritDoc
     */
    protected function init()
    {
        return $this->installation = $this->getWPInstallation();
    }

    /**
     * @inheritDoc
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        if ($this->installation === null) {
            $this->setMessage('Not WordPress');
            return false;
        }

        return true;
    }

    /**
     * Get the WP Installation model.
     *
     * @return \App\Models\Installation
     */
    protected function getWPInstallation()
    {
        return $this->model->installations()
            ->where('product_id', Products::getWordPressCore()->id)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    protected function vulnerable()
    {
        parent::storeFinding();
    }
}
