<?php

namespace App\Libs\Modules\Discovery\Domains\Requests;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Providers\Mixed\AlienVaultOTX as Provider;
use App\Libs\Helpers\Websites;
use App\Libs\Helpers\Requests;
use App\Libs\Helpers\Domains;
use App\Models\Website;
use App\Models\Domain;

/**
 * Alien Vault OTX Domain Discovery Module.
 *
 * Fetch known URLs from AlienVault's Open Threat Exchange for given domain.
 * https://otx.alienvault.com/api
 *
 * @todo It also gets subdomains. Should those be key? Or be attached to the projcet of it's parent?
 */
class AlienVaultOTX extends Module
{
    /**
     * API provider.
     *
     * @var \App\Libs\Providers\Mixed\AlienVaultOTX
     */
    protected $provider;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->provider = new Provider();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $results = $this->provider->indicators('domain', $this->model->name, 'url_list');
        $this->store($results);
        $this->showOutput();
    }

    /**
     * Store the obtained results.
     *
     * @param array $results
     */
    private function store($results)
    {
        if (!$results || empty($results)) {
            $this->setMessage('Empty results.');
            return;
        }

        $this->output('  - Found ' . count($results) . ' urls...');

        foreach ($results as $result) {
            $cleanUrl = Websites::cleanURL($result->url);

            // Allow only subdomains with wildcard parent domain
            $domain = parse_url($cleanUrl, PHP_URL_HOST);
            $parent = Domains::getTopLevelDomain($domain);
            if ($parent != $domain) {
                if (!Domain::where('name', $parent)->where('wildcard', 1)->exists()) {
                    continue;
                }
            }
            try {
                $website = Website::where(['url' => $cleanUrl])->first();
                if (!$website) {
                    $website = Websites::createWebsite($cleanUrl);
                    $website->key = 1;
                    $website->save();
                }
                if (!$website) {
                    continue;
                }
                $request = Requests::storeRequest($website, $result->url, 'GET');
                if ($request) {
                    $this->items[$request->path] = $request;
                }
            } catch (\Exception $e) {
                //
            }
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->output('  - ' . $item->website->url . $item->path);
        }
    }
}
