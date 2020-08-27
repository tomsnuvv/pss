<?php

namespace App\Libs\Modules\Discovery\Organisations\Websites;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Websites;
use App\Libs\Helpers\Projects;
use App\Libs\Providers\Mixed\Shodan as Provider;

/**
 * Shodan Websites Organisations Discovery Module.
 *
 * Obtains Websites (IPs) from Shodan API by searching the organisation name.
 */
class Shodan extends Module
{
    /**
     * Shodan.io API Provider.
     *
     * @var \App\Libs\Providers\Mixed\Shodan
     */
    protected $provider;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->provider = new Provider;
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

        if (!$this->model->projects()->exists()) {
            $this->setMessage('This org is not part of any project.');
            return false;
        }

        if (!env('SHODAN_TOKEN')) {
            $this->setMessage('Shodan.io token is not set.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $data = $this->provider->search('ssl:"' . $this->model->name . '"');

        $this->store($data);
        $this->showOutput();
    }

    /**
     * Store the obtained data.
     *
     * @param array $data
     */
    private function store($data)
    {
        foreach ($data->matches as $result) {
            if (!isset($result->http)) {
                continue;
            }

            $url = 'https://' . $result->http->host;
            if ($result->port != 443) {
                $url .= ':' . $result->port;
            }
            $url .= $result->http->location;

            $website = Websites::createWebsite($url);
            if (!$website) {
                continue;
            }

            $this->checkWebsiteDomainKey($website);

            // Relate the website to the Organisation projects
            Projects::relateProjectsFromSourceToTarget($this->model, $website);
            $this->items[] = $website;
        }
    }

    /**
     * Check if the website domain (or it's parent) is a key domain.
     * If so, sets the website as key as well.
     *
     * @param \App\Models\Website $website
     */
    private function checkWebsiteDomainKey($website)
    {
        if (!$website->domains) {
            return;
        }
        foreach ($website->domains as $domain) {
            if ($domain->key || ($domain->parent && $domain->parent->key)) {
                $website->key = 1;
                $website->save();
                return;
            }
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail('Website', $item->url);
        }
    }
}
