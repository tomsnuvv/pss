<?php

namespace App\Libs\Modules\Audit\Websites\Products\WordPress\Enumeration;

use App\Libs\Contracts\Modules\Abstracts\Audits\WordPress;

/**
 * WordPress WPAPI Enumeration Audit Module.
 *
 * Checks if WordPress WPAPI Enumeration is possible.
 */
class WPAPI extends WordPress
{
    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'WP_USER_ENUM_WPAPI';

    /**
     * URI Payload.
     *
     * @var string
     */
    const PAYLOAD = 'wp-json/wp/v2/users';

    /**
     * Finding details.
     *
     * @var array
     */
    protected $details = [];

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
        $response = json_decode($this->response->getBody());
        if (!is_array($response)) {
            return;
        }

        foreach ($response as $user) {
            if (isset($user->name)) {
                $this->details[] = $user->name;
            }
        }

        return ! empty($this->details);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        $details = 'GET `' . self::PAYLOAD . '`' . PHP_EOL . PHP_EOL;
        $details .= 'Found users: `' . implode(', ', $this->details) . '`' . PHP_EOL . PHP_EOL;
        $details .= '[Reproduce](' . $this->buildURL(self::PAYLOAD) . ')';

        return $details;
    }
}
