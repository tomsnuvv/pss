<?php

namespace App\Libs\Modules\Discovery\Websites;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Contracts\Modules\Traits\Http;

/**
 * Headers Websites Discovery Module.
 *
 * Obtains the Headers from a website.
 */
class Headers extends Module
{
    use Http;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->request('GET');
        if (!$this->response) {
            return;
        }

        $this->store($this->response->getHeaders());
        $this->showOutput();
    }

    /**
     * Stores the obtained data.
     *
     * @param array $headers
     */
    private function store($headers)
    {
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $this->items[] = $this->model->headers()->firstOrCreate([
                    'name' => $name,
                    'value' => $value,
                ]);
            }
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail($item->name, $item->value);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('headers');
    }
}
