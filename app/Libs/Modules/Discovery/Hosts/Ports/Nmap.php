<?php

namespace App\Libs\Modules\Discovery\Hosts\Ports;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Hosts;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;
use App\Libs\Contracts\Modules\Traits\PortScan;

/**
 * Nmap Hosts Ports Discovery Module.
 *
 * Obtains Ports from a host.
 */
class Nmap extends Module
{
    use Process;
    use PortScan;

    /**
     * Ports to scan.
     *
     * @var array
     */
    const PORTS = [21, 22, 23, 25, 53, 80, 81, 110, 119, 143, 443, 2376, 8080, 8081, 8082, 9200, 9100];

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

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
        $this->tmp = 'outputs/nmap_' . $this->model->ip . '.xml';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->runProcess(['nmap', '-p', implode(',', self::PORTS), '-Pn', '-sT', '-sV', $this->model->ip, '-oX', storage_path('app/' . $this->tmp)]);
        $content = Storage::get($this->tmp);
        Storage::delete($this->tmp);
        $this->store($content);
        $this->showOutput();
    }

    /**
     * Store the obtained data.
     *
     * @param array $content
     */
    private function store($content)
    {
        $xml = simplexml_load_string($content);

        if (!isset($xml->host->ports->port)) {
            return;
        }

        foreach ($xml->host->ports->port as $port) {
            if (!$this->isPortOpen($port)) {
                continue;
            }

            $product = $this->getPortProduct($port);

            $data = [
                'product_id' => $product ? $product->id : null,
                'version' => $this->getPortProductVersion($port),
                'protocol' => $this->getPortProtocol($port),
                'service' => $this->getPortServiceName($port),
                'port' => $this->getPortNumber($port),
            ];

            $this->items[] = Hosts::addPort($this->model, $data, $this->getModuleModel());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('ports');
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->output('  - <comment>Port: </comment>' . $item->protocol . ' ' . $item->port . ' ' . $item->service);
            if ($item->installation) {
                $this->output('  - <comment>Installation: </comment>' . $item->installation->product->name . ' ' . $item->installation->version);
            }
        }
    }
}
