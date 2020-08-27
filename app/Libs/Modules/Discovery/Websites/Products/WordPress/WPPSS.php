<?php

namespace App\Libs\Modules\Discovery\Websites\Products\WordPress;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Http;
use App\Libs\Helpers\Products;
use App\Libs\Helpers\Websites;
use App\Libs\Modules\Info\Products\WordPress\WPAPI;
use App\Models\ProductType;

/**
 * WordPress Plugin (WPPSS) Websites Products Discovery Module.
 *
 * Obtains installed Products information from Websites using
 * WPPSS WordPress plugin through a secure API.
 * https://github.com/Endouble/WPPSS
 *
 * If the product is new, will obtain online data using Info modules,
 * in order to save more details, such as latest version available.
 */
class WPPSS extends Audit
{
    use Http;

    /**
     * API URI
     *
     * @var string
     */
    const URI = 'endouble-pss';

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
        if (!$this->model->token()->exists()) {
            $this->setMessage('Missing token');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function buildURL($uri = '')
    {
        $parsed = parse_url($this->model->url);
        return $parsed['scheme'].'://'.$parsed['host'].'/'.$uri;
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $this->request('POST', self::URI, [
            'form_params' => [
                'token' => $this->model->token->token,
            ],
            'allow_redirects' => [
                'strict' => true
            ]
        ]);

        $this->checkResponse();
        $this->parseResponse();
        $this->checkContent();

        $this->store();
    }

    /**
     * Checks if the response is valid.
     *
     * @throws \Exception
     */
    private function checkResponse()
    {
        if (!$this->response) {
            throw new \Exception('No response');
        }

        if (strstr($this->response->getBody(), 'Invalid token')) {
            throw new \Exception('Invalid token');
        }

        if (!$this->isSuccess()) {
            throw new \Exception($this->response->getStatusCode());
        }
    }

    /**
     * Parse the API response.
     */
    private function parseResponse()
    {
        // Extract the JSON content (websites might contain PHP notices & warnings)
        preg_match_all('/\[\{(.*)\}\]/x', $this->response->getBody(), $matches);
        if (isset($matches[0]) && count($matches[0]) > 0) {
            $this->content = json_decode($matches[0][0], true);
        } else {
            $this->content = json_decode($this->response->getBody(), true);
        }
    }

    /**
     * Checks if the API response data was valid.
     *
     * @throws \Exception
     */
    private function checkContent()
    {
        if (!is_array($this->content) || empty($this->content)) {
            throw new \Exception('Unknown response content');
        }
    }

    /**
     * Store the products.
     */
    protected function store()
    {
        foreach ($this->content as $item) {
            if (!isset($item['code']) || !$item['code']) {
                continue;
            }
            // Fix for WPPSS plugin
            if ($item['code'] == 'wordpress-core') {
                $item['code'] = 'wordpress/wordpress';
            }
            $product = Products::createCodeProduct($this->getProductType($item['type']), $item['code'], WPAPI::class);
            $installation = Websites::installProduct($this->model, $product, $item['version'], $this->getModuleModel());
            $this->items[] = $installation;

            $this->outputDetail($product->name, $installation->version);
        }
    }

    /**
     * Get the Product Type by ID.
     *
     * @param  int  $type
     * @return \App\Models\ProductType
     */
    protected function getProductType($type)
    {
        return ProductType::find($type);
    }

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('installations');
    }
}
