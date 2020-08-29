<?php

namespace App\Libs\Modules\Discovery\Websites\Requests;

use App\Libs\Contracts\Modules\Abstracts\Module;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;
use App\Libs\Helpers\Requests;

/**
 * Crawler Website Discovery Module.
 *
 * Crawls the website for discovering links and pages.
 * Uses https://sdk.apify.com/.
 */
class Crawler extends Module
{
    use Process;

    /**
     * Max links to crawl.
     *
     * @var int
     */
    const MAX = 20;

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->tmp = 'outputs/crawler/' . $this->model->id;
    }

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!$this->model->crawl) {
            $this->setMessage('Website is not set to be crawled');
            return false;
        }

        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->runProcess([env('TOOLS_NODE'), env('TOOLS_CRAWLER'), $this->model->url, self::MAX], null, [
            'APIFY_LOCAL_STORAGE_DIR' => storage_path('app/' . $this->tmp),
            'APIFY_HEADLESS' => true,
        ]);

        $this->store();

        Storage::delete($this->tmp);

        $this->showOutput();
    }

    /**
     * Store the obtained data.
     */
    private function store()
    {
        $datasetsPath = $this->tmp . '/datasets/default';

        if (!Storage::exists($datasetsPath)) {
            $this->setMessage('No datasets found.');
            return;
        }

        foreach (Storage::files($datasetsPath) as $file) {
            $data = json_decode(file_get_contents(storage_path('app/' . $file)));
            $url = str_replace($this->model->url, '', $data->url);
            $request = Requests::storeRequest($this->model, $url, 'GET', $data->status, $data->content);
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
