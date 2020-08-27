<?php

namespace App\Libs\Modules\Discovery\Domains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Models\Website as WebsiteModel;
use App\Libs\Helpers\Websites;
use App\Libs\Helpers\Projects;
use App\Libs\Helpers\Domains;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * Domains Website Discovery Module.
 *
 * Obtains a Website from a Domain.
 *
 * @todo try HTTPS as well. Maybe check for host ports?
 */
class Website extends Module
{
    use Http;

    /**
     * HTTP requests timeout.
     *
     * @var int
     */
    const TIMEOUT = 30;

    /**
     * Ignore non-success HTTP status codes.
     *
     * @var bool
     */
    private $onlySuccess = false;

    /**
     * {@inheritdoc}
     */
    protected function buildURL()
    {
        return 'http://' . $this->model->name;
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

        if (WebsiteModel::where('url', 'LIKE', '%://' . $this->model->name)->exists()) {
            $this->setMessage('Website already exists.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $url = $this->getLastUrl();
        if ($this->onlySuccess && !$this->isSuccess()) {
            return;
        }

        if (!$this->response) {
            return;
        }

        $website = Websites::createWebsite($url, null, false);
        if (!$website) {
            return;
        }

        Projects::relateProjectsFromSourceToTarget($this->model, $website);

        // Key only if top domains match (MX subdomains will resolve to webmails / mail providers)
        $domain = parse_url($url, PHP_URL_HOST);
        if (Domains::getTopLevelDomain($domain) === Domains::getTopLevelDomain($this->model->name)) {
            $website->key = true;
        }
        $website->status = $this->response->getStatusCode();
        $website->save();

        // Relate the website with the domain, as some domains resolve to a completely different website domain
        $this->model->websites()->attach($website);

        $this->outputDetail('Website', $website->url);
        $this->items[] = $website;
    }
}
