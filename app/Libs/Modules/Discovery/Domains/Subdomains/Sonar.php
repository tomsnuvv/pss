<?php

namespace App\Libs\Modules\Discovery\Domains\Subdomains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Domains;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;

/**
 * Sonar Subdomains Domains Discovery Module.
 *
 * Obtains subdomains from a domain using Rapid7 Sonar tool.
 * https://opendata.rapid7.com/sonar.fdns_v2/
 *
 * @todo Update the file from time to time
 *
 * If the Model Domain is key, the discovered Domains will also be key.
 */
class Sonar extends Module
{
    use Process;

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
        if (!env('TOOLS_SONAR_BIN') || !env('TOOLS_SONAR_DATA')) {
            $this->setMessage('Envs not set');
            return false;
        }

        if (!$this->model->wildcard) {
            $this->setMessage('Not a wildcard domain');
            return false;
        }

        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
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
        $this->tmp = 'outputs/fdns_' . $this->model->name . '.txt';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $domain = str_replace('.', '\.', $this->model->name);

        if ($this->environment != 'local' || !Storage::exists($this->tmp)) {
            $this->runProcess([
                'bash', env('TOOLS_SONAR_BIN'), env('TOOLS_SONAR_DATA'), $domain, storage_path('app/' . $this->tmp)
            ]);
        }
        $content = Storage::get($this->tmp);
        if ($this->environment != 'local') {
            Storage::delete($this->tmp);
        }
        $this->store($content);
    }

    /**
     * Store the obtained data.
     *
     * @param array $content
     */
    private function store($content)
    {
        if (!$content) {
            $this->setMessage('Empty output.');
            return;
        }
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $content) as $name) {
            if (!$name) {
                continue;
            }

            $domain = Domains::createDomain($name);
            if ($domain) {
                $this->outputDetail('Domain', $domain->name);
                if ($domain->wasRecentlyCreated) {
                    $domain->key = 1;
                    $domain->save();
                }
                $this->items[] = $domain;
            }
        }
    }
}
