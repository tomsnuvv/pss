<?php

namespace App\Libs\Modules\Discovery\Websites\Requests;

use App\Libs\Contracts\Modules\Abstracts\Module;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;
use App\Libs\Helpers\Requests;

/**
 * Common Crawl Requests Website Discovery Module.
 *
 * Obtains requests from Common Crawl API.
 *
 * @TODO Consider port and path of the website?
 */
class CommonCrawl extends Module
{
    use Process;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        if (!$this->model->crawl) {
            $this->setMessage('Website is not set to be crawled');
            return false;
        }

        if ($this->model->environment && !$this->model->environment->isPublic()) {
            $this->setMessage('Website is not public.');
            return false;
        }

        return true;
    }

    /**
     * Generate output path.
     *
     * @param  string $domain
     *
     * @return string
     */
    protected function outputPath($domain)
    {
        return 'outputs/commoncrawl_' . $domain . '.txt';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $domain = parse_url($this->model->url, PHP_URL_HOST);

        $outputPath = $this->outputPath($domain);
        $this->runProcess(['python3', env('TOOLS_CC'), $domain, '-y', date('Y'), '-u', '-o', storage_path('app/' . $outputPath)]);
        if (!Storage::exists($outputPath)) {
            $this->setMessage('No results.');
            return;
        }
        $content = Storage::get($outputPath);
        Storage::delete($outputPath);

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
        if (!$content) {
            $this->setMessage('Empty output.');
            return;
        }

        $urls = [];
        foreach (explode(PHP_EOL, $content) as $url) {
            $urls[] = $url;
        }
        $urls = array_unique($urls);

        $this->output(' - Storing ' . count($urls) . ' urls...');

        foreach ($urls as $url) {
            $request = Requests::storeRequest($this->model, $url, 'GET');
            if ($request) {
                $this->items[$request->path] = $request;
            }
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->output('  - ' . $item->path);
        }
    }
}
