<?php

namespace App\Libs\Modules\Discovery\Domains\Subdomains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Domains;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;
use Symfony\Component\Process\Process as ProcessClass;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * One For All Subdomains Discovery Module.
 *
 * Obtains subdomains from a domain using MassDNS tool.
 * https://github.com/blechschmidt/massdns
 *
 * It uses @jhaddix all.txt list as dictionary:
 * https://gist.github.com/jhaddix/f64c97d0863a78454e44c2f7119c2a6a
 *
 * This module might cause connectivity issues.
 *
 * If the Model Domain is key, the discovered Domains will also be key.
 *
 * @todo: DNS association.
 */
class MassDNS extends Module
{
    use Process;

    /**
     * Path to the temporary input file.
     *
     * @var string
     */
    protected $inputPath;

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $outputPath;

    /**
     * Endless timeout.
     *
     * @var int
     */
    protected $timeout = 0;

    /**
     * Number of concurrent lookups.
     *
     * @var int
     */
    const THREADS = 1000;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!env('TOOLS_MASSDNS') || !env('TOOLS_MASSDNS_RESOLVERS') || !env('TOOLS_MASSDNS_WORDLIST')) {
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

        if (Domains::hasWildcardConfig($this->model->name)) {
            $this->setMessage('Domain has a wildcard config');
            return false;
        }

        return true;
    }

    /*
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->inputPath = 'inputs/massdns_' . $this->model->name . '.txt';
        $this->outputPath = 'outputs/massdns_' . $this->model->name . '.txt';
    }

    /**
     * Generates the dictionary by attaching the domain as suffix
     *
     * sed -e 's/$/.domain.com/' massdns_all.txt > domain_all.txt
     *
     * Since the command is pipped, it requires a different approach than the one used in trait.
     */
    private function generateDictionary()
    {
        $process = new ProcessClass('sed -e "s/\$/.$DOMAIN/" $WORDLIST > $OUTPUT');
        $process->setTimeout($this->getTimeout());
        $process->run(null, [
            'DOMAIN' => $this->model->name,
            'WORDLIST' => env('TOOLS_MASSDNS_WORDLIST'),
            'OUTPUT' => storage_path('app/' . $this->inputPath)
        ]);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->generateDictionary();

        $this->runProcess([env('TOOLS_MASSDNS'), '-r', env('TOOLS_MASSDNS_RESOLVERS'), storage_path('app/' . $this->inputPath),
        '-s', self::THREADS, '-t', 'A', '-o', 'S', '-w', storage_path('app/' . $this->outputPath)]);

        $content = Storage::get($this->outputPath);
        Storage::delete($this->inputPath);
        Storage::delete($this->outputPath);

        if (!$content) {
            $this->setMessage('Empty output.');
            return;
        }

        $domains = $this->process($content);
        $this->output('  - Analysing ' . count($domains) . ' results...');
        $this->store($domains);
    }

    /**
     * Process the obtained data.
     *
     * @param  array $content
     * @return array Domain list
     */
    private function process($content)
    {
        $domains = [];

        foreach (preg_split("/((\r?\n)|(\r\n?))/", $content) as $line) {
            if (!$line) {
                continue;
            }

            $parts = explode(' ', $line);
            $subdomain = isset($parts[0]) ? trim($parts[0], '.') : null;
            $dnsType = isset($parts[1]) ? strtoupper($parts[1]) : null;
            $resolution = isset($parts[2]) ? trim($parts[2], '.') : null;

            $name = null;
            if ($dnsType == 'CNAME') {
                $name = $resolution;
            } elseif ($dnsType == 'A') {
                $name = $subdomain;
            }

            if ($name) {
                $domains[] = $name;
            }
        }

        return array_unique($domains);
    }

    /**
     * Store the obtained domains.
     *
     * @param array $domains
     */
    private function store($domains)
    {
        foreach ($domains as $name) {
            // Allow only subdomains
            if (Domains::getTopLevelDomain($name) !== $this->model->name) {
                continue;
            }
            $domain = Domains::createDomain($name, true);
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
