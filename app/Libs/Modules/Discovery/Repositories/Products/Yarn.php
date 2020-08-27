<?php

namespace App\Libs\Modules\Discovery\Repositories\Products;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Providers\Repositories\Git;
use App\Libs\Helpers\Repositories;
use App\Libs\Helpers\Products;
use Mindscreen\YarnLock\YarnLock;

/**
 * Yarn Repositories Products Discovery Module.
 *
 * Obtains javascript packages from a yarn.lock files, from a repository.
 *
 * @todo Avoid using 3rd party dependencies...
 */
class Yarn extends Module
{
    /**
     * Repository provider.
     *
     * @var \App\Libs\Providers\Repositories\Git
     */
    protected $provider;

    /**
     * Yarn.lock file contents.
     *
     * Might be more than one file in the repo.
     *
     * @var array \Mindscreen\YarnLock\YarnLock
     */
    protected $contents = [];

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->provider = new Git($this->model);
        $this->loadContent();
    }

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (empty($this->contents)) {
            $this->setMessage('yarn.lock files not found');
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
            $this->auditContent($content);
        }
    }

    /**
     * Audit a Yarn content.
     *
     * @param \Mindscreen\YarnLock\YarnLock $content
     */
    protected function auditContent($content)
    {
        foreach ($content->getPackages() as $package) {
            $name = $package->getName();
            if (!$name) {
                continue;
            }
            $this->addPackage($name, $package->getVersion());
        }
    }

    /**
     * Loads the yarn.lock file content.
     *
     * @return bool
     */
    protected function loadContent()
    {
        $results = $this->provider->searchFile('yarn.lock');
        if (empty($results)) {
            return false;
        }

        foreach ($results as $file) {
            $content = file_get_contents($file);
            $this->contents[] = YarnLock::fromString($content);
        }

        return true;
    }

    /**
     * Adds a package from the yarn file.
     *
     * @param string $code
     * @param string $version
     */
    protected function addPackage($code, $version)
    {
        $product = Products::createJavascriptProduct($code);
        $installation = Repositories::installProduct($this->model, $product, $version, $this->getModuleModel());
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
