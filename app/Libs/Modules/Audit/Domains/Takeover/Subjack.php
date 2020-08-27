<?php

namespace App\Libs\Modules\Audit\Domains\Takeover;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Process;
use Illuminate\Support\Facades\Storage;

/**
 * Subjack Domains Takeover Audit Module.
 *
 * Audits Domains / Subdomains for takeover vulnerabilities.
 * https://github.com/haccer/subjack
 */
class Subjack extends Audit
{
    use Process;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'SUBDOMAIN_TAKEOVER';

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
     * Subjack timeout parameter.
     *
     * @var int
     */
    const TIMEOUT = 120;

    /**
     * Finding details.
     *
     * @var string
     */
    protected $details;

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

    /*
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->inputPath = 'inputs/subjack_'.$this->model->name.'.txt';
        $this->outputPath = 'outputs/subjack_'.$this->model->name.'.json';

        Storage::put($this->inputPath, $this->model->name);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->environment != 'local' || !Storage::exists($this->outputPath)) {
            $command = [
                env('TOOLS_SUBJACK'), '-a', '-ssl', '-timeout', self::TIMEOUT, '-w',
                storage_path('app/'.$this->inputPath), '-o',
                storage_path('app/'.$this->outputPath)
            ];

            if (env('TOOLS_SUBJACK_CONFIG')) {
                $command = array_merge($command, [
                    '-c', env('TOOLS_SUBJACK_CONFIG'),
                ]);
            }

            $this->runProcess($command);
        }

        Storage::delete($this->inputPath);

        // The tool only generates output if success
        if (!Storage::exists($this->outputPath)) {
            return;
        }

        $content = Storage::get($this->outputPath);
        if ($this->environment != 'local') {
            Storage::delete($this->outputPath);
        }

        $this->store($content);
    }

    /**
     * Store the obtained data.
     *
     * @param  array  $content
     * @throws \Exception
     */
    private function store($content)
    {
        $json = json_decode($content);
        if (empty($json)) {
            throw new \Exception('Output JSON malformed');
        }
        foreach ($json as $item) {
            if (!$item->vulnerable) {
                continue;
            }
            $this->details = $item->service;
            $this->storeFinding();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = 'Domain resolved to '.$this->details.': '.PHP_EOL.PHP_EOL;
        $details .= '\$ dig '.$this->model->name.PHP_EOL;

        $details .= $this->runProcess(['dig', $this->model->name]);

        return $details;
    }
}
