<?php

namespace App\Libs\Modules\Discovery\Hosts\Ports;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Hosts;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;
use App\Libs\Contracts\Modules\Traits\PortScan;

/**
 * Masscan Hosts Ports Discovery Module.
 *
 * Obtains Ports from a host.
 */
class Masscan extends Module
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
     * Ports to scan.
     *
     * @var string
     */
    const PORTS = '1-65535';

    /**
     * Max packets per second.
     *
     * @var string
     */
    const MAX_RATE = 10000;

    /**
     * Max ports to store.
     *
     * Sometimes masscan gives false positives.
     * When this happens, the results report a lot of ports opened.
     * If there are more than MAX_PORTS, we will mark the module log as failed.
     *
     * @var integer
     */
    const MAX_PORTS = 200;

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
        $this->tmp = 'outputs/masscan_' . $this->model->ip . '.xml';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->environment != 'local' || !Storage::exists($this->tmp)) {
            $this->runProcess(['sudo', 'masscan', '--max-rate', self::MAX_RATE, '-p', self::PORTS, $this->model->ip, '-oX', storage_path('app/' . $this->tmp)]);
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
     * @param array $content
     */
    private function store($content)
    {
        $xml = simplexml_load_string($content);

        if (!isset($xml->host->ports->port)) {
            return;
        }

        if (count($xml->host) > self::MAX_PORTS) {
            throw new \Exception('Too many ports open (false positives): ' . count($xml->host));
        }

        foreach ($xml->host as $host) {
            foreach ($host->ports->port as $port) {
                if (!$this->isPortOpen($port)) {
                    continue;
                }

                $data = [
                    'protocol' => $this->getPortProtocol($port),
                    'port' => $this->getPortNumber($port),
                ];

                $this->items[] = Hosts::addPort($this->model, $data, $this->getModuleModel());
            }
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
            $this->output('  - <comment>Port: </comment>' . $item->protocol . ' ' . $item->port);
        }
    }
}
