<?php

namespace App\Libs\Modules\Discovery\Organisations\Hosts;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Hosts;
use App\Libs\Helpers\Projects;
use App\Libs\Providers\Mixed\Shodan as Provider;

/**
 * Shodan Hosts Organisations Discovery Module.
 *
 * Obtains Hosts from Shodan API by searching the organisation name.
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
        $data = $this->provider->search('org:"' . $this->model->name . '"');

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
            if (isset($result->ip)) {
                $ip = long2ip($result->ip);
                $host = Hosts::createServerFromIP($ip);
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
