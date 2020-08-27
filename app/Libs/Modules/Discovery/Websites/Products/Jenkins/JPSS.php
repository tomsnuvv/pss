<?php

namespace App\Libs\Modules\Discovery\Websites\Products\Jenkins;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Http;
use App\Libs\Helpers\Products;
use App\Libs\Helpers\Websites;

/**
 * Jenkins Plugin Security Scanner (JPPSS) Websites Products Discovery Module.
 *
 * Obtains Jenkins plugins and core versions by using a Jenkins token.
 *
 * @todo Perhaps the credentials should be stored in the DB, somehow.
 */
class JPSS extends Audit
{
    use Http;

    /**
     * API URI
     *
     * @var string
     */
    const URI = 'pluginManager/api/xml?depth=1';

    /**
     * API Response data in JSON
     *
     * @var array
     */
    protected $content;

    /**
     * Obtained Products data to sync with the website.
     *
     * @var array
     */
    private $syncData = [];

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!$this->model->installations()->where('product_id', Products::getJenkinsCore()->id)->exists()) {
            $this->setMessage('Not a Jenkins website');
            return false;
        }

        if (!$this->model->token()->exists()) {
            $this->setMessage('Missing token');
            return false;
        }

        if (!$this->getUsername() || !$this->getPassword()) {
            $this->setMessage('Invalid token');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $this->request('GET', self::URI, [
            'auth' => [
                $this->getUsername(),
                $this->getPassword(),
            ]
        ]);
        if (!$this->response) {
            return;
        }
        $xml = simplexml_load_string($this->response->getBody());
        if (!isset($xml->plugin)) {
            throw new \Exception('Unknwon XML format');
        }

        $this->store($xml);
        $this->showOutput();
    }

    /**
     * Store the products.
     *
     * @param  object  $xml
     */
    private function store($xml)
    {
        foreach ($xml->plugin as $plugin) {
            if ($plugin->active == 'false' || $plugin->deleted == 'true' || $plugin->enabled == 'false') {
                continue;
            }
            $product = Products::createJenkinsPluginProduct($plugin->shortName);
            $product->fill([
                'name' => $plugin->longName ?: null,
                'website' => $plugin->url ?: null,
            ]);
            $product->save();
            $installation = Websites::installProduct($this->model, $product, $plugin->version, $this->getModuleModel());
            $this->items[] = $installation;
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail($item->product->name, $item->version);
        }
    }

    /**
     * Get the username from the model token.
     *
     * @return string|void
     */
    private function getUsername()
    {
        $parts = explode(':', $this->model->token->token);
        if (isset($parts[0])) {
            return $parts[0];
        }
    }

    /**
     * Get the password from the model token.
     *
     * @return string|void
     */
    private function getPassword()
    {
        $parts = explode(':', $this->model->token->token);
        if (isset($parts[1])) {
            return $parts[1];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('installations');
    }
}
