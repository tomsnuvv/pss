<?php

namespace App\Libs\Modules\Discovery\Websites;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Domains as DomainsHelper;

/**
 * Domain Websites Discovery Module.
 *
 * Obtains the Domains from a website.
 * If the Website uses a subdomain, will also add the top domain.
 */
class Domains extends Module
{
    /**
     * URL host.
     *
     * @var string
     */
    protected $urlHost;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->urlHost = DomainsHelper::getDomainFromURL($this->model->url);
    }

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }
        
        if (ip2long($this->urlHost) != false) {
            $this->setMessage('Domain is an IP');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $domain = DomainsHelper::createDomain($this->urlHost);
        if ($domain) {
            $this->model->domains()->syncWithoutDetaching([$domain->id]);

            // Keep the key value
            if ($this->model->key) {
                $domain->key = true;
                $domain->save();
            }

            $this->outputDetail('Domain', $domain->name);
            if ($domain->parent()->exists()) {
                $this->outputDetail('Parent', $domain->parent->name);
            }
        }
    }
}
