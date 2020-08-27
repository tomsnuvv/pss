<?php

namespace App\Libs\Modules\Discovery\Repositories\Products;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Providers\Repositories\Git;
use App\Libs\Helpers\Products;
use App\Libs\Helpers\Repositories;

/**
 * Composer Repositories Products Discovery Module.
 *
 * Obtains composer packages from a composer.lock files, from a repository.
 */
class Composer extends Module
{
    /**
     * Repository provider.
     *
     * @var \App\Libs\Providers\Repositories\Git
     */
    protected $provider;

    /**
     * Composer.lock file contents.
     *
     * Might be more than one file in the repo.
     *
     * @var array \stdClass
     */
    protected $contents = [];

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->provider = new Git($this->model);
        $this->loadComposerLock();
    }

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (empty($this->contents)) {
            $this->setMessage('Composer.lock files not found');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        foreach ($this->contents as $content) {
            if (!isset($content->packages) || empty($content->packages)) {
                return;
            }
            $this->auditContent($content);
        }
    }

    /**
     * Audit a Composer content.
     *
     * @param \stdClass $content
     */
    protected function auditContent($content)
    {
        foreach ($content->packages as $package) {
            $this->addPackage($package);
        }
    }

    /**
     * Loads the composer.lock file content.
     *
     * @return bool
     */
    protected function loadComposerLock()
    {
        $results = $this->provider->searchFile('composer.lock');
        if (empty($results)) {
            return false;
        }

        foreach ($results as $file) {
            $this->contents[] = json_decode(file_get_contents($file));
        }

        return true;
    }

    /*
     * Adds a package from the composer file.
     *
     * @param \stdClass $data
     */
    protected function addPackage($data)
    {
        $product = Products::createComposerProduct($data->name);
        $installation = Repositories::installProduct($this->model, $product, $data->version, $this->getModuleModel());
        $this->items[] = $installation;

        $this->outputDetail($product->name, $installation->version);
    }

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('installations');
    }
}
