<?php

namespace App\Libs\Modules\Audit\Websites\Products\WordPress\Enumeration;

use App\Libs\Contracts\Modules\Abstracts\Audits\WordPress;

/**
 * WordPress Author Enumeration Audit Module.
 *
 * Checks if WordPress Author Enumeration is possible.
 */
class Author extends WordPress
{
    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'WP_USER_ENUM_AUTHOR';

    /**
     * Max amount of tries.
     *
     * @var int
     */
    const MAX_TRIES = 5;

    /**
     * Request details.
     *
     * @var array
     */
    private $details = [];

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $uris = [];
        for ($id = 1; $id <= self::MAX_TRIES; $id++) {
            $uris[] = '?author=' . $id;
        }

        $this->multipleRequest('GET', $uris);
        if (!$this->responses) {
            return;
        }

        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        foreach ($this->responses as $id => $response) {
            if (!isset($response['value'])) {
                continue;
            }
            $body = $response['value']->getBody();
            if (strpos($body, 'archive author')) {
                $this->details[] = [
                    'id' => $id,
                    'body' => $body,
                ];
            }
        }

        return !empty($this->details);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $usernames = [];
        foreach ($this->details as $detail) {
            preg_match('/archive author author-(\w*) author/', $detail['body'], $matches);
            if (isset($matches[1])) {
                $usernames[] = $matches[1];
            }
        }

        return 'Found users: ' . implode(', ', $usernames);
    }
}
