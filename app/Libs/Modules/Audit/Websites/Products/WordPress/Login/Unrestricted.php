<?php

namespace App\Libs\Modules\Audit\Websites\Products\WordPress\Login;

use App\Libs\Contracts\Modules\Abstracts\Audits\WordPress;
use \App\Libs\Contracts\Modules\Traits\Http;

/**
 * WordPress Unrestricted Login Audit Module.
 *
 * Checks if WordPress admin panel is accessible to anyone.
 */
class Unrestricted extends WordPress
{
    use Http;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'WP_LOGIN_UNRESTRICTED';

    /**
     * URI Payload.
     *
     * @var string
     */
    const PAYLOAD = 'wp-login.php';

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
        return $this->isSuccess() && strpos($this->response->getBody(), 'loginform');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = '`GET ' . self::PAYLOAD . '`' . PHP_EOL . PHP_EOL;
        $details .= 'Response code: ' . $this->response->getStatusCode() . PHP_EOL . PHP_EOL;
        $details .= '[Reproduce](' . $this->buildURL(self::PAYLOAD) . ')';

        return $details;
    }
}
