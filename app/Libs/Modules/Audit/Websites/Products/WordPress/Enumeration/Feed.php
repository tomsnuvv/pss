<?php

namespace App\Libs\Modules\Audit\Websites\Products\WordPress\Enumeration;

use App\Libs\Contracts\Modules\Abstracts\Audits\WordPress;

/**
 * WordPress Feed Enumeration Audit Module.
 *
 * Checks if WordPress Feed Enumeration is possible.
 */
class Feed extends WordPress
{
    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'WP_USER_ENUM_FEED';

    /**
     * URI Payload.
     *
     * @var string
     */
    const PAYLOAD = '?feed=rss2';

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $this->request('GET', self::PAYLOAD);
        if (!$this->response) {
            return;
        }

        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function isVulnerable()
    {
        return strpos($this->response->getBody(), '<dc:creator>');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = '`GET ' . self::PAYLOAD . '`' . PHP_EOL . PHP_EOL;
        $details .= '`&#x3C;dc:creator&#x3E;` was found in the response.' . PHP_EOL . PHP_EOL;
        $details .= '[Reproduce](' . $this->buildURL(self::PAYLOAD) . ')';

        return $details;
    }
}
