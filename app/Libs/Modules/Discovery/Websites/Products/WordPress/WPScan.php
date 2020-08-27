<?php

namespace App\Libs\Modules\Discovery\Websites\Products\WordPress;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Products;
use Illuminate\Support\Facades\Storage;
use App\Libs\Helpers\Websites;
use App\Libs\Contracts\Modules\Traits\Process;
use \Exception;

/**
 * WPScan WordPress Websites Products Module.
 *
 * @todo It needs to be updated periodically (cronjob?)
 *
 * Identifies WordPress core, themes and plugins (Products) by using WPScan v3.
 * https://github.com/wpscanteam/wpscan.
 */
class WPScan extends Module
{
    use Process;

    /**
     * Path to the temporary output file
     *
     * @var string
     */
    protected $tmp;

    /**
     * Min confidence to consider a Product as installed
     *
     * If the min level is not reached,
     * the product will not be created nor installed.
     *
     * @var integer
     */
    const MIN_CONFIDENCE_INSTALL = 60;

    /**
     * Min confidence to consider an Installation version
     *
     * If the min level is not reached,
     * the installation will be created without version.
     *
     * @var integer
     */
    const MIN_CONFIDENCE_VERSION = 60;

    /**
     * The max threads to use
     *
     * @var integer
     */
    const MAX_THREADS = 10;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(24)) {
            $this->setMessage('Module already executed in the 24 hours');
            return false;
        }

        // Website must run WordPress
        if (!$this->model->installations()->where('product_id', Products::getWordPressCore()->id)->exists()) {
            $this->setMessage('Website is not WordPress');
            return false;
        }

        // WPPSS module is more precise
        if ($this->model->token()->exists()) {
            $this->setMessage('Website has a token');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->tmp = 'outputs/wpscan_' . $this->model->id . '.txt';
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->environment != 'local' || !Storage::exists($this->tmp)) {
            $this->runProcess([
                env('TOOLS_WPSCAN'), '--url', $this->model->url, '--no-update',
                '--random-user-agent', '--disable-tls-checks', '--max-threads', self::MAX_THREADS,
                '--enumerate', 'p,t',
                '--format', 'json', '--output', storage_path('app/' . $this->tmp)
            ]);
        }

        $output = Storage::get($this->tmp);
        if ($this->environment != 'local') {
            Storage::delete($this->tmp);
        }

        $content = json_decode($output);
        if (!is_object($content) || empty($content)) {
            throw new \Exception('Output malformed: ' . $output);
        }

        $this->store($content);
        $this->showOutput();
    }

    /**
     * Store the obtained data.
     *
     * @param object $content
     */
    private function store($content)
    {
        $this->storeCore($content);
        $this->storeThemes($content);
        $this->storePlugins($content);
    }

    /**
     * Store the Core results.
     *
     * @param object $content
     */
    private function storeCore($content)
    {
        if (!isset($content->version->number)) {
            return;
        }

        $product = Products::getWordPressCore();
        $version = $content->version->number;

        $this->installProduct($product, $version, $content->version->confidence);
    }

    /**
     * Store the Themes results.
     *
     * @param object $content
     */
    private function storeThemes($content)
    {
        if (!isset($content->themes)) {
            return;
        }
        foreach ($content->themes as $theme) {
            if ($theme->confidence < self::MIN_CONFIDENCE_INSTALL) {
                continue;
            }

            $product = Products::createWordPressThemeProduct($theme->slug);

            if (isset($theme->version->number)) {
                $version = $theme->version->number;
            } else {
                $version = null;
            }

            $this->installProduct($product, $version);
        }
    }

    /**
     * Store the Plugins results.
     *
     * @param object $content
     */
    private function storePlugins($content)
    {
        if (!isset($content->plugins)) {
            return;
        }
        foreach ($content->plugins as $plugin) {
            if ($plugin->confidence < self::MIN_CONFIDENCE_INSTALL) {
                continue;
            }

            $product = Products::createWordPressPluginProduct($plugin->slug);

            if (isset($plugin->version->number)) {
                $version = $plugin->version->number;
            } else {
                $version = null;
            }

            $this->installProduct($product, $version);
        }
    }

    /**
     * Install a Product into the Website.
     *
     * @param  \App\Models\Product $product
     * @param  string              $version
     * @param  integer             $confidence
     */
    private function installProduct($product, $version = null, $confidence = null)
    {
        if ($version && $confidence !== null && $confidence < self::MIN_CONFIDENCE_VERSION) {
            $version = null;
        }

        $this->items[] = Websites::installProduct($this->model, $product, $version, $this->getModuleModel());
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail($item->product->name, $item->version ?: '?');
        }
    }
}
