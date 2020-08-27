<?php

namespace App\Libs\Modules\Discovery\Domains\DNS;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Contracts\Modules\Traits\Process;
use App\Libs\Helpers\DNS;
use App\Libs\Helpers\Hosts;
use Illuminate\Support\Facades\Storage;

/**
 * DNSRecon Domains DNS Discovery Module.
 *
 * Enumerates DNS records.
 * https://github.com/darkoperator/dnsrecon
 *
 * The discovered Domains and Hosts will be added as models.
 */
class DNSRecon extends Module
{
    use Process;

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

    /**
     * DNS resolvers to use.
     *
     * @var array
     */
    const DNS = '1.1.1.1';

    /**
     * Timeout (in seconds).
     *
     * @var int
     */
    protected $timeout = 3600;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the last 24 hours');
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
    protected function init()
    {
        $this->tmp = 'outputs/dnsrecon_'.$this->model->name.'.txt';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->environment != 'local' || !Storage::exists($this->tmp)) {
            $this->runProcess([
                env('TOOLS_DNSRECON'), '-n', self::DNS, '-t', 'std,axfr', '-d', $this->model->name, '-j',
                storage_path('app/'.$this->tmp)
            ], true);
        }
        $content = Storage::get($this->tmp);
        if ($this->environment != 'local') {
            Storage::delete($this->tmp);
        }
        $this->store($content);
        $this->showOutput();
    }

    /**
     * Store the obtained data.
     *
     * @param  array  $content
     */
    private function store($content)
    {
        if (!$content) {
            $this->setMessage('Empty output.');
            return;
        }
        $json = json_decode($content);
        if (!is_array($json)) {
            $this->setMessage('Malformed output.');
            return;
        }

        foreach ($json as $item) {
            if (!in_array($item->type, ['SOA', 'NS', 'A', 'AAAA', 'MX', 'SRV', 'TXT'])) {
                continue;
            }

            // Nameservers
            if ($item->type === 'NS') {
                $this->addNameserver($item);
                continue;
            }

            // DNS record
            $data = $this->fillData($item);
            $dns = DNS::createDNS($this->model, $data);
            if ($dns) {
                $this->items[] = $dns;
            }
        }
    }

    /**
     * Adds a Nameserver host and attach it to the current domain.
     *
     * @param  object  $item
     */
    private function addNameserver($item)
    {
        $host = Hosts::createNameServer($item->target);
        if ($host) {
            $this->model->nameservers()->firstOrCreate([
                'name' => $item->target,
                'host_id' => $host->id,
            ]);
            $this->items[] = $host;
        }
    }

    /**
     * Fill the record data.
     *
     * @param  object  $item
     * @return array
     */
    private function fillData($item)
    {
        $value = null;

        switch ($item->type) {
            case 'AAA':
            case 'A':
                $value = $item->name;
                break;
            case 'MX':
                $value = $item->exchange;
                break;
            case 'TXT':
                $value = $item->strings;
                break;
            case 'SOA':
                $value = $item->mname;
                break;
            case 'SRV':
                $value = $item->target;
                break;
        }

        return [
            'type' => $item->type,
            'value' => $value,
        ];
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            if (get_class($item) == 'App\Models\DNS') {
                $this->outputDetail($item->type, $item->value);
            } elseif (get_class($item) == 'App\Models\Host') {
                $this->outputDetail('Nameserver', $item->name);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('dns');
    }
}
