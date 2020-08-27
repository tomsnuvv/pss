<?php

namespace App\Libs\Modules\Discovery\Websites\Requests;

use App\Libs\Contracts\Modules\Abstracts\Module;
use Illuminate\Support\Facades\Storage;
use App\Libs\Contracts\Modules\Traits\Http;
use App\Libs\Helpers\Requests;

/**
 * Archive Requests Website Discovery Module.
 *
 * Fetch all the URLs that Archive.org knows about for a url.
 */
class Archive extends Module
{
    use Http;

    /**
     * Timeout for requests (in seconds).
     *
     * @var int
     */
    protected $timeout = 30;

    /**
     * API URL.
     *
     * @var string
     */
    const API = 'http://web.archive.org/';

    /**
     * API URI.
     *
     * @var string
     */
    const URI = 'cdx/search/cdx';

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
            $this->setMessage('Website is not public');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildURL($uri = '')
    {
        return self::API . $uri;
    }

    /**
     * Generates the request query.
     *
     * @return array
     */
    protected function buildQuery()
    {
        return [
            'url' => $this->model->url . '/*',
            'fl' => 'original',
            'collapse' => 'urlkey',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $pages = $this->getTotalPages();
        $this->output(' - Total pages: ' . ($pages + 1));

        $urls = [];

        $query = $this->buildQuery();
        $page = 0;
        do {
            $query['page'] = $page;
            $this->output(' - Requesting page ' . $page);
            $this->request('GET', self::URI, ['query' => $query]);

            if (!$this->isSuccess()) {
                break;
            }

            foreach (explode(PHP_EOL, $this->response->getBody()) as $url) {
                $urls[] = $url;
            }

            $page++;
        } while ($page <= $pages);

        $this->store(array_unique($urls));
        $this->showOutput();
    }

    /**
     * Get the results total pages.
     *
     * @return int|void
     */
    protected function getTotalPages()
    {
        $query = $this->buildQuery();
        $query['showNumPages'] = true;
        $this->request('GET', self::URI, ['query' => $query]);
        if (!$this->isSuccess()) {
            $this->setMessage('Bad response from the API.');
            return;
        }
        return (int) (string) $this->response->getBody();
    }

    /**
     * Store the obtained data.
     *
     * @param array $urls
     */
    private function store($urls)
    {
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
