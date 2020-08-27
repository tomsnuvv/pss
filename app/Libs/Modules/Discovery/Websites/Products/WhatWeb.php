<?php

namespace App\Libs\Modules\Discovery\Websites\Products;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Products;

use Illuminate\Support\Facades\Storage;
use App\Libs\Helpers\Websites;
use App\Libs\Contracts\Modules\Traits\Process;

/**
 * WhatWeb Websites Products Module.
 *
 * @todo Different vendors / licenses / types ... Also allow only web-type products.
 *
 * Identifies web technologies (Products) by using WhatWeb.
 * https://github.com/urbanadventurer/WhatWeb/.
 */
class WhatWeb extends Module
{
    use Process;

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

    /**
     * Code relation between WhatWeb and PSS.
     *
     * @var array
     */
    const CODES = [
        'wordpress'     => 'wordpress',
        'microsoft-iis' => 'iis',
        'asp_net'       => 'asp.net',
    ];

    /**
     * List of ignored items.
     *
     * @var array
     */
    const IGNORED = [
        'ip', 'html5', 'youtube', 'x-pingback', 'frame', 'title', 'uncommonheaders',
        'httpserver', 'country', 'open-graph-protocol', 'strict-transport-security',
        'x-frame-options', 'script', 'x-xss-protection', 'x-ua-compatible', 'metagenerator',
        'email', 'cookies', 'httponly', 'meta-author', 'x-powered-by', 'opensearch', 'prototype',
        'redirectlocation', 'passwordfield', 'content-language', 'meta-refresh-redirect',
        'access-control-allow-methods', 'www-authenticate', 'poweredby', 'content-security-policy',
        'via-proxy',
        # To consider
        'google-analytics', 'php', 'apache'
    ];

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->tmp = 'outputs/whatweb_' . $this->model->id . '.txt';
    }

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
        $this->runProcess([env('TOOLS_WHATWEB'), '--user-agent', config('pss.modules.http.user-agent'), $this->model->url, '--log-json', storage_path('app/' . $this->tmp)]);

        $output = Storage::get($this->tmp);
        Storage::delete($this->tmp);

        $contents = json_decode($output);
        if (!is_array($contents) || empty($contents)) {
            throw new \Exception('Output malformed: ' . $output);
        }

        foreach ($contents as $content) {
            $this->store($content);
        }

        $this->showOutput();
    }

    /**
     * Store the obtained data.
     *
     * @param string $content
     */
    private function store($content)
    {
        if (!isset($content->plugins)) {
            return;
        }

        foreach ($content->plugins as $name => $info) {
            $name = strtolower($name);
            if (in_array($name, self::IGNORED)) {
                continue;
            }
            $product = $this->findProduct($name);
            if (!$product) {
                $this->outputError('Product not found for: ' . $name);
                continue;
            }
            $this->items[] = Websites::installProduct($this->model, $product, isset($info->version[0]) ? $info->version[0] : null, $this->getModuleModel());
        }
    }

    /**
     * Finds a product.
     *
     * @param  string $code
     *
     * @return \App\Models\Product
     */
    private function findProduct($code)
    {
        if (isset(self::CODES[$code])) {
            $code = self::CODES[$code];
        }
        return Products::createCodeProduct(null, $code);
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
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->deleteOldItems('installations');
    }
}
