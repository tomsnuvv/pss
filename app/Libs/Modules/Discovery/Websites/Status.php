<?php

namespace App\Libs\Modules\Discovery\Websites;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Contracts\Modules\Traits\Http;
use App\Models\Request;
use App\Libs\Helpers\Websites;
use App\Libs\Helpers\Projects;
use App\Libs\Helpers\Requests;
use Exception;

/**
 * Status Websites Discovery Module.
 *
 * Obtains the HTTP status of a website.
 * It also stores the request content.
 *
 * @todo On a redirect, should the old website be deleted?
 */
class Status extends Module
{
    use Http;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
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
        $this->request('GET', '', ['allow_redirects' => false]);
        if (!$this->response) {
            $this->storeStatus();
            return;
        }

        $this->storeStatus($this->response->getStatusCode());
        Requests::storeRequest($this->model, '/', 'GET', $this->model->status, $this->response->getBody());

        // Redirect?
        if ($this->model->status >= 300 && $this->model->status < 400) {
            $lastUrl = $this->getLastUrl();
            $website = Websites::createWebsite($lastUrl);
            Projects::relateProjectsFromSourceToTarget($this->model, $website);
            $this->outputDetail('New website', $website->url);
        }
    }

    /**
     * Store the website response status.
     *
     * @param  integer|null $status
     */
    private function storeStatus($status = null)
    {
        $this->model->status = $status;
        $this->model->save();
        $this->outputDetail('Status', $status);
    }
}
