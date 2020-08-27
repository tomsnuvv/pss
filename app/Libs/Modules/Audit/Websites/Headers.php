<?php

namespace App\Libs\Modules\Audit\Websites;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Models\VulnerabilityType;

/**
 * Websites Headers Audit Module.
 *
 * Audit the websites headers.
 */
class Headers extends Audit
{
    /**
     * List of secure headers help strings.
     *
     * @var array
     */
    const HEADERS_DEFINITIONS = [
        'Strict-Transport-Security'   => 'HSTS',
        'Content-Security-Policy'     => 'XCSP',
        'Public-Key-Pins'             => 'HPKP',
        'X-Frame-Options'             => 'XFRAMEOPTIONS',
        'X-XSS-Protection'            => 'XXSS',
        'X-Content-Type-Options'      => 'XCTO',
        'Referrer-Policy'             => 'RP',
    ];

    /**
     * List of security cookie flags.
     *
     * @var array
     */
    const COOKIE_FLAGS_DEFINITIONS = [
        'HTTPOnly' => 'COOKIES_HTTPONLY',
        'Secure'   => 'COOKIES_SECURE',
        'SameSite' => 'COOKIES_SAMESITE',
    ];

    /**
     * Website headers.
     *
     * @var array
     */
    private $headers;

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->headers = array_map('strtolower', $this->model->headers()->pluck('name')->toArray());
    }

    /*
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (empty($this->headers)) {
            $this->setMessage('No headers returned');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        foreach (self::HEADERS_DEFINITIONS as $name => $type) {
            if (!$this->hasHeader($name)) {
                $this->storeFinding(null, null, null, VulnerabilityType::whereCode($type)->firstOrFail());
            }
        }

        $this->auditCors();

        // Not all websites return cookies
        if (!$this->hasCookies()) {
            return;
        }
        foreach (self::COOKIE_FLAGS_DEFINITIONS as $name => $type) {
            if (!$this->hasCookieFlag($name)) {
                $this->storeFinding(null, null, null, VulnerabilityType::whereCode($type)->firstOrFail());
            }
        }
    }

    /**
     * Audit the CORS header.
     */
    private function auditCors()
    {
        if ($this->hasHeader('Access-Control-Allow-Origin')) {
            $header = $this->model->headers()->whereName('Access-Control-Allow-Origin')->first();
            if ($header && $header->value == '*') {
                $this->storeFinding(null, null, null, VulnerabilityType::whereCode('ACAO')->firstOrFail());
            }
        }
    }

    /**
     * Checks if the current website has a header.
     *
     * @param string $header
     *
     * @return bool
     */
    public function hasHeader($header)
    {
        return in_array(strtolower($header), $this->headers, true);
    }

    /**
     * Checks if the current website cookie has a flag.
     *
     * @param string $flag
     *
     * @return bool
     */
    public function hasCookieFlag($flag)
    {
        return strstr(strtolower($this->headers['Set-Cookie']), $flag) !== false;
    }

    /**
     * Checks if the current website has cookies header.
     *
     * @return bool
     */
    public function hasCookies()
    {
        return isset($this->headers['Set-Cookie']);
    }
}
