<?php

namespace App\Libs\Modules\Import\Vulnerabilities\WordPress;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Providers\Vulnerabilities\WordPress\WPVulnDB as Provider;
use App\Libs\Helpers\Products;
use App\Libs\Helpers\Vulnerabilities;
use App\Models\Product;

/**
 * WPVulnDB WordPress Vulnerabilities Import Module.
 *
 * Imports latest vulnerabilities from WPVulnDB.
 */
class WPVulnDB extends Module
{
    /**
     * Github Provider.
     *
     * @var \App\Libs\Providers\Vulnerabilities\WordPress\WPVulnDB
     */
    protected $provider;

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!env('WPVULNDB_TOKEN')) {
            $this->setMessage('wpvulndb.com token is not set.');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->provider = new Provider();
    }

    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $this->output('<info>Fetching last 20 vulnerabilities...</info>');
        $vulnerabilities = $this->provider->getLatest();
        foreach ($vulnerabilities as $vulnerability) {
            $this->importVulnerability($vulnerability->id);
        }
    }

    /**
     * Import a vulnerability by it's ID.
     *
     * @param  int $id WPVulnDB ID
     */
    protected function importVulnerability($id)
    {
        $this->output('<info>Importing vulnerability ' . $id . '...</info>');
        $response = $this->provider->getVulnerability($id);

        $details = [
            'title' => $response->title,
            'date' => date('Y-m-d H:i:s', strtotime($response->published_date)),
            'type' => $response->vuln_type,
            'versions' => [],
        ];

        $details['details'][] = [
            'type' => 'wpvulndb_id',
            'data' => $id,
        ];
        foreach ($response->references as $type => $items) {
            foreach ($items as $reference) {
                $details['details'][] = [
                    'type' => $type,
                    'data' => $reference,
                ];
            }
        }

        foreach ($response->plugins as $code => $versions) {
            $product = Products::createWordPressPluginProduct($code);
            $this->outputDetail('Plugin', $product->name);

            $details['versions'][] = [
                'product_id' => $product->id,
                'fixed_in' => $versions->fixed_in,
            ];
            $this->storeVulnerability($details);

            // Set versions empty again
            $details['versions'] = [];
        }

        foreach ($response->themes as $code => $versions) {
            $product = Products::createWordPressThemeProduct($code);
            $this->outputDetail('Theme', $product->name);

            $details['versions'][] = [
                'product_id' => $product->id,
                'fixed_in' => $versions->fixed_in,
            ];
            $this->storeVulnerability($details);

            // Set versions empty again
            $details['versions'] = [];
        }

        if (isset($response->wordpresses) && !empty((array)$response->wordpresses)) {
            $details['versions'] = [];
            $product = Products::createWordPressCoreProduct();
            $this->output('WordPress Core');

            foreach ($response->wordpresses as $version => $versions) {
                $details['versions'][] = [
                    'product_id' => $product->id,
                    'min' => $version,
                    'fixed_in' => $versions->fixed_in,
                ];
            }
            $this->storeVulnerability($details);
        }
    }

    /**
     * Store a vulnerability.
     *
     * @param  array               $data
     */
    protected function storeVulnerability($data)
    {
        $vuln = Vulnerabilities::createVulnerability($data);
        if ($vuln) {
            $this->outputDetail('Vulnerability', $vuln->title);
            $this->items[] = $vuln;
        }
    }
}
