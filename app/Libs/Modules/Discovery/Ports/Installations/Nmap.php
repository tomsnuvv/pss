<?php

namespace App\Libs\Modules\Discovery\Ports\Installations;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Hosts;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;
use App\Libs\Contracts\Modules\Traits\PortScan;

/**
 * Nmap Ports Instalaltions Discovery Module.
 *
 * Obtains Installations from a Port.
 */
class Nmap extends Module
{
    use Process;
    use PortScan;

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
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        if (!$this->model->host->key) {
            $this->setMessage('Port is not in a key host');
            return false;
        }

        if ($this->model->installation()->exists()) {
            $this->setMessage('Product already identified.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->tmp = 'outputs/nmap_' . $this->model->host->ip . '-' . $this->model->port . '.xml';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->runProcess(['nmap', '-p', $this->model->port, '-Pn', '-sT', '-sV', $this->model->host->ip, '-oX', storage_path('app/' . $this->tmp)]);
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

            $this->items[] = Hosts::addPort($this->model->host, $data, $this->getModuleModel());
        }
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

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('installation');
    }
}
