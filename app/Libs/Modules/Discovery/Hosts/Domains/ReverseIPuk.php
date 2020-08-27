<?php

namespace App\Libs\Modules\Discovery\Hosts\Domains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Domains;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * Reverse-IP.uk Domains Discovery Module.
 *
 * Obtains Domains from a Host IP, using the reverse-ip.uk API.
 */
class ReverseIPuk extends Module
{
    use Http;

    /**
     * API URL.
     *
     * @var string
     */
    const URL = 'https://www.reverse-ip.uk/reverse';

    /**
     * API Token.
     *
     * @var string
     */
    private $token;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        if (!$this->token) {
            $this->setMessage('Reverse-ip.uk token is not set.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->token = env('REVERSE_IP_UK_API');
    }

    /**
     * {@inheritdoc}
     */
    protected function buildURL($uri = '')
    {
        return self::URL;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->request('POST', '', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'form_params' => [
                'ip' => $this->model->ip,
            ]
        ]);
        if (!$this->response) {
            return false;
        }

        if (!$this->response->getBody() || $this->response->getStatusCode() !== 200) {
            return false;
        }

        $content = json_decode((string) $this->response->getBody());

        $this->store($content);
        $this->showOutput();
    }

    /**
     * Store the obtained data.
     *
     * @param string $data
     */
    private function store($data)
    {
        foreach ($data->data as $result) {
            $domain = Domains::createDomain($result);
            if ($domain) {
                if ($this->model->key) {
                    $domain->key = 1;
                    $domain->save();
                }
                $this->items[] = $domain;
            }
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail('Domain', $item->name);
        }
    }
}
