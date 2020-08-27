<?php

namespace App\Libs\Modules\Audit\Websites\Products\WordPress;

use App\Libs\Contracts\Modules\Abstracts\Audits\WordPress;
use \App\Libs\Contracts\Modules\Traits\Http;

/**
 * WordPress XMLRPC Audit Module.
 *
 * Checks if XMLRPC l is enabled and unrestricted.
 */
class XMLRPC extends WordPress
{
    use Http;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'WP_XMLRPC_UNRESTRICTED';

    /**
     * URI Payload.
     *
     * @var string
     */
    const PAYLOAD = 'xmlrpc.php';

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $this->request('POST', self::PAYLOAD);
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
        return $this->isSuccess();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = '`POST ' . self::PAYLOAD . '`' . PHP_EOL . PHP_EOL;
        $details .= 'Response code: ' . $this->response->getStatusCode() . PHP_EOL . PHP_EOL;
        $details .= '[Reproduce](' . $this->buildURL(self::PAYLOAD) . ')';

        return $details;
    }
}
