<?php

namespace App\Libs\Modules\Discovery\Domains;

use App\Libs\Contracts\Modules\Abstracts\Module;
use Iodev\Whois\Whois as Client;

/**
 * Whois Domains Discovery Module.
 *
 * Obtains Whois records from a domain.
 */
class Whois extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->model->parent()->exists()) {
            $this->setMessage('Not a top level domain');
            return false;
        }

        if ($this->ranInLastHours(672)) {
            $this->setMessage('Module already executed in the last month');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $client = Client::create();
        $info = $client->loadDomainInfo($this->model->name);
        $response = $client->lookupDomain($this->model->name);
        if ($info && $response) {
            $data = $this->parseInfo($info, $response);
            $this->store($data);
            $this->showOutput();
        }
    }

    /**
     * Parse the obtained info to Model fields.
     *
     * @param  array $info
     * @param  string $response
     *
     * @return array
     */
    private function parseInfo($info, $response)
    {
        return [
            'registrar' => $info->getRegistrar() ?: null,
            'owner' => $info->getOwner() ?: null,
            'nameservers' => $info->getNameServers() ?: null,
            'creation_date' => $info->getCreationDate() ?: null,
            'expiration_date' => $info->getExpirationDate() ?: null,
            'raw' => $response->getText(),
        ];
    }

    /**
     * Store the obtained data.
     *
     * @param array $data
     */
    private function store($data)
    {
        $this->model->whois()->updateOrCreate([], $data);
        $this->items[] = $this->model->whois;
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            foreach ($item->toArray() as $field => $value) {
                if ($item->isFillable($field)) {
                    if ($field == 'raw') {
                        continue;
                    }
                    $this->outputDetail($field, $value);
                }
            }
        }
    }
}
