<?php

namespace App\Libs\Modules\Discovery\Domains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Hosts;
use App\Libs\Contracts\Modules\Traits\Process;

/**
 * NameServers Domains Discovery Module.
 *
 * Obtains the NameServers from a domain.
 */
class NameServers extends Module
{
    use Process;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(672)) {
            $this->setMessage('Module already executed in the last month');
            return false;
        }

        if ($this->module->domain_id) {
            $this->setMessage('Not a top level domain');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $nameservers = $this->getNameservers();

        if ($nameservers !== null && count($nameservers)) {
            $this->store($nameservers);
            $this->showOutput();
        }
    }

    /**
     * Get the domain nameservers
     *
     * @return array|void
     */
    private function getNameservers()
    {
        $output = $this->runProcess(['host', '-t', 'ns', $this->model->name]);
        preg_match_all("/name server (.*?)\.$/m", $output, $matches);
        if (isset($matches[1]) && count($matches[1])) {
            return $matches[1];
        }
    }

    /**
     * Store the obtained data.
     *
     * @param array $nameservers
     */
    private function store($nameservers)
    {
        foreach ($nameservers as $nameserver) {
            $host = Hosts::createNameServer($nameserver);
            if (!$host) {
                continue;
            }
            $this->items[] = $this->model->nameservers()->firstOrCreate([
                'name' => $nameserver,
                'host_id' => $host->id,
            ]);
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail('Nameserver', $item->name);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('nameservers');
    }
}
