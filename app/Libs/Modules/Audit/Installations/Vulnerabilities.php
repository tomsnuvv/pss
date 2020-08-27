<?php

namespace App\Libs\Modules\Audit\Installations;

use App\Libs\Contracts\Modules\Abstracts\Audit;

/**
 * Installation Vulnerabilities Audit Module.
 *
 * Checks if an installation is vulnerable.
 */
class Vulnerabilities extends Audit
{
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

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $affectances = $this->product->affectances;

        foreach ($affectances as $affectance) {
            if ($affectance->affects($this->model->version)) {
                $this->storeFinding(null, null, $affectance->vulnerability);
            }
        }
    }
}
