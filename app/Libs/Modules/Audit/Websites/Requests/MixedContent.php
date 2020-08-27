<?php

namespace App\Libs\Modules\Audit\Websites\Requests;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Models\Request;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * MixedContent Audit Module.
 *
 * Checks if HTTPS websites are serving assets over HTTP.
 * If assets are loaded over JS or dynamically, will not be detected, as the module
 * looks for those in the main HTML response of the requests.
 */
class MixedContent extends Audit
{
    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'MIXED_CONTENT';

    /**
     * Search query to match requests in the database.
     *
     * @var string
     */
    const QUERY = 'http:';
    /**
     * Regex pattern to find Google Maps Keys.
     *
     * @var string
     */
    const REGEX = '/<(?!a )[^<>]*(src|href|content|location|url|origin)[=\'"]{0,2}http:(.*?)[\'">]/';

    /**
     * Results.
     *
     * @var array
     */
    private $results = [];

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->requests = $this->model->requests()->with('content')->whereHas('content', function ($query) {
            $query->where('body', 'LIKE', '%' . self::QUERY . '%');
        })->get();
    }

    /*
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        if (empty($this->requests)) {
            $this->setMessage('No requests matched');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        foreach ($this->requests as $request) {
            preg_match(self::REGEX, $request->content->body, $matches);
            if (isset($matches[0])) {
                $this->results[] = $matches[0];
            }
        }
        $this->results = array_unique($this->results);

        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        return !empty($this->results);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = 'Content found: ' . PHP_EOL. PHP_EOL;

        foreach ($this->results as $result) {
            $details .= '`' . $result . '`' . PHP_EOL . PHP_EOL;
        }

        return $details;
    }
}
