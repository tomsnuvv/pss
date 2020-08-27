<?php

namespace App\Libs\Modules\Audit\Installations;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Helpers\Versions;

/**
 * Outdated Installations Audit Module.
 *
 * Checks if an installation is outdated.
 */
class Outdated extends Audit
{
    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'OUTDATED';

    /**
     * Installation product.
     *
     * @var \App\Models\Product
     */
    protected $product;

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->product = $this->model->product;
    }

    /*
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!$this->model->version) {
            $this->setMessage('Unknown version');
            return false;
        }

        if (!$this->product->latest_version) {
            $this->setMessage('Unknown latest version');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        $version = Versions::removeVSigns($this->model->version);

        return version_compare($version, $this->product->latest_version, '<');
    }
}
