<?php

namespace App\Libs\Modules\Audit\Websites\Products\WordPress\Enumeration;

use App\Libs\Contracts\Modules\Abstracts\Audits\WordPress;
use Symfony\Component\DomCrawler\Crawler;

/**
 * WordPress Login Enumeration Audit Module.
 *
 * Checks if WordPress Login Enumeration is possible.
 *
 * @todo Check for the real valid wp-login.php uri.
 */
class Login extends WordPress
{
    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'WP_USER_ENUM_LOGIN';

    /**
     * List of common usernames
     *
     * @var array
     */
    const USERNAMES = ['admin', 'root', 'administrator'];

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
        $params = [];
        foreach (self::USERNAMES as $username) {
            $uris[] = 'wp-login.php';
            $params[] = [
                'form_params' => [
                    'log' => $username,
                    'pwd' => mt_rand(),
                    'wp-submit' => 'Log In',
                ]
            ];
        }

        $this->multipleRequest('POST', $uris, $params);
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
        foreach ($this->responses as $response) {
            if (!isset($response['value'])) {
                continue;
            }
            $body = $response['value']->getBody();
            if (strpos($body, 'The password you entered for the username')) {
                $this->details[] = [
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
        $messages = [];
        foreach ($this->details as $detail) {
            $crawler = new Crawler((string) $detail['body']);
            $messages[] = trim(htmlentities($crawler->filter('div#login_error')->html()));
        }

        return 'Responses: ' . PHP_EOL . PHP_EOL . implode(PHP_EOL . PHP_EOL, $messages);
    }
}
