<?php

namespace App\Libs\Modules\Discovery\Organisations\Hosts;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Hosts;
use App\Libs\Helpers\Projects;
use App\Libs\Providers\Mixed\Censys as Provider;

/**
 * Censys.io Hosts Organisations Discovery Module.
 *
 * Obtains IPv4 from Censys.io API.
 */
class Censys extends Module
{
    /**
     * Censys.io API Provider.
     *
     * @var \App\Libs\Providers\Mixed\Censys
     */
    protected $provider;

    /**
     * Query results.
     *
     * @var array
     */
    private $results = [];

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

        if (!env('CENSYS_CREDENTIALS')) {
            $this->setMessage('Censys.io credentials are not set.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->search('ipv4', 'autonomous_system.description.raw: "' . $this->model->name . '"');
        $this->search('ipv4', '443.https.tls.certificate.parsed.subject.organizational_unit: "' . $this->model->name . '"');

        $this->store();
        $this->showOutput();
    }

    /**
     * Perform a search request with pagination.
     */
    public function search($type, $query)
    {
        $page = 0;
        $pages = 1;
        do {
            $page++;
            $data = $this->provider->search($type, $query, $page);
            if (!isset($data->metadata->pages)) {
                return;
            }
            $this->results = array_merge($this->results, $data->results);
            $pages = $data->metadata->pages;
        } while ($page > $pages);
    }

    /**
     * Store the obtained data.
     */
    private function store()
    {
        foreach ($this->results as $result) {
            if (isset($result->ip)) {
                $host = Hosts::createServerFromIP($result->ip);
                // Relate the host to the Organisation projects
                Projects::relateProjectsFromSourceToTarget($this->model, $host);
                $this->items[] = $host;
            }
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail('Host', $item->ip);
        }
    }
}
