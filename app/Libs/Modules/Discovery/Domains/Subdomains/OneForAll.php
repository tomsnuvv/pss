<?php

namespace App\Libs\Modules\Discovery\Domains\Subdomains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Domains;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;

/**
 * One For All Subdomains Discovery Module.
 *
 * Obtains subdomains from a domain using One For All tool.
 * https://github.com/shmilylty/OneForAll/blob/master/README.en.md
 *
 * It uses MassDNS for DNS resolution and because of that it requires to run as root.
 *
 * If the Model Domain is key, the discovered Domains will also be key.
 */
class OneForAll extends Module
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
        $this->tmp = 'outputs/oneforall_' . $this->model->name . '.json';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        try {
            $this->runProcess(['sudo', 'python3.8', env('TOOLS_ONEFORALL'),
            '--target', $this->model->name, '--dns', 'TRUE', '--format', 'json',
            '--path', storage_path('app/' . $this->tmp), 'run']);
        } catch (\Exception $e) {
            // do nothing, as *sometimes* the process hangs out, but generates output
        }
        $content = Storage::get($this->tmp);
        Storage::delete($this->tmp);
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
