<?php

namespace App\Libs\Modules\Discovery\Domains\Subdomains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Domains;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;

/**
 * Amass Subdomains Domains Discovery Module.
 *
 * Obtains subdomains from a domain using Amass tool.
 * https://github.com/OWASP/Amass
 *
 * If the Model Domain is key, the discovered Domains will also be key.
 */
class Amass extends Module
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
    const DNS = ['8.8.8.8', '1.1.1.1'];

    /**
     * Endless timeout.
     *
     * @var int
     */
    protected $timeout = 0;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
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
        $this->tmp = 'outputs/amass_' . $this->model->name . '.txt';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->runProcess([
            env('TOOLS_AMASS'),
            'enum', '-active', '-r', implode(',', self::DNS),
            '-d', $this->model->name,
            '-o', storage_path('app/' . $this->tmp)
        ]);
        $content = Storage::get($this->tmp);
        Storage::delete($this->tmp);
        $this->store($content);
    }

    /**
     * Store the obtained data.
     *
     * @param string $content
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
