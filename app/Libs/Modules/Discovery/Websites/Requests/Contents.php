<?php

namespace App\Libs\Modules\Discovery\Websites\Requests;

use App\Libs\Contracts\Modules\Abstracts\Module;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Process;
use App\Libs\Helpers\Requests;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Contents Requests Website Discovery Module.
 *
 * Request the content from Requests models that have no response code.
 * Failed requests will be set to status = 0.
 * Uses https://sdk.apify.com/.
 *
 * @TODO Allow POST requests?
 * @TODO Parameters
 * @TODO Perform a request and only fetch the status code with large files (.zip, .pdf...)
 */
class Contents extends Module
{
    use Process;

    /**
     * Path to the temporary input URLs list.
     *
     * @var string
     */
    protected $inputPath;

    /**
     * Path to the temporary output dir.
     *
     * @var string
     */
    protected $outputPath;

    /**
     * Number of days to consider a request outdated.
     *
     * @var integer
     */
    const OUTDATED_CONTENT_DAYS = 15;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!$this->model->crawl) {
            $this->setMessage('Website is not set to be crawled');
            return false;
        }

        if (!$this->query()->exists()) {
            $this->setMessage('There are no requests pending to visit.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->inputPath = 'inputs/contents_' . $this->model->id . '.txt';
        $this->outputPath = 'outputs/contents/' . $this->model->id;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->prepareInput();

        if ($this->environment != 'local' || !Storage::exists($this->outputPath)) {
            $this->runProcess([env('TOOLS_NODE'), env('TOOLS_CONTENTS'), storage_path('app/' . $this->inputPath)], null, [
                'APIFY_LOCAL_STORAGE_DIR' => storage_path('app/' . $this->outputPath),
                'APIFY_HEADLESS' => true,
            ]);
        }

        $this->store();

        if ($this->environment != 'local') {
            Storage::delete($this->inputPath);
            Storage::delete($this->outputPath);
        }

        $this->showOutput();
    }

    /**
     * Prepare the input list of urls to visit.
     */
    private function prepareInput()
    {
        $urls = [];
        foreach ($this->query()->get() as $request) {
            $urls[] = $this->model->url . $request->path;
        }
        Storage::put($this->inputPath, implode(PHP_EOL, $urls));
    }

    /**
     * Get the requests pending to process.
     *
     * @return Builder
     */
    private function query()
    {
        return $this->model->requests()->where(function (Builder $query) {
            $query->whereNull('content_id')->orWHereHas('content', function (Builder $query) {
                $query->where('updated_at', '>', Carbon::today()->subDays(self::OUTDATED_CONTENT_DAYS));
            });
        });
    }

    /**
     * Store the obtained data.
     */
    private function store()
    {
        $datasetsPath = $this->outputPath . '/datasets/default';

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
