<?php

namespace App\Libs\Modules\Import\Vulnerabilities\Composer;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Providers\Vulnerabilities\Composer\SecurityAdvisories as Provider;
use App\Libs\Helpers\Vulnerabilities;
use App\Libs\Helpers\Products;
use App\Libs\Helpers\Versions;

/**
 * SecurityAdvisories Composer Vulnerabilities Import Module.
 *
 * Imports all available vulnerabilities from SecurityAdvisories vulnerability database.
 */
class SecurityAdvisories extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $provider = new Provider();
        $vulns = $provider->getVulnerabilities();

        foreach ($vulns as $vendor => $products) {
            foreach ($products as $product => $vulnerabilities) {
                $product = Products::createComposerProduct($vendor . '/' . $product);
                $this->outputDetail('Product', $product->name);
                foreach ($vulnerabilities as $details) {
                    $data = $this->parseVulnerabilityData($product->id, $details);
                    $vulnerability = Vulnerabilities::createVulnerability($data);
                    $this->outputDetail('Vulnerability', $vulnerability->title);
                }
            }
        }
    }

    /**
     * Parse the vulnerability data, to be used by the Vulnerabilities helper.
     *
     * The date is stored in each affectance, however we're only going to store the last one.
     *
     * @param  int $productId
     * @param  array   $data
     * @return array
     */
    public function parseVulnerabilityData($productId, $data)
    {
        $result = $data;

        // Product & Versions
        foreach ($data['branches'] as $branch) {
            $time = strstr($branch['time'], '-') ? strtotime($branch['time']) : $branch['time'];
            $result['date'] = date('Y-m-d H:i:s', $time);
            $affectance = Versions::parseVersions($branch['versions']);
            $affectance['product_id'] = $productId;
            $result['affectances'][] = $affectance;
        }

        // Details
        if (isset($data['cve']) && $data['cve']) {
            $result['details'][] = [
                'type' => 'cve',
                'data' => $data['cve'],
            ];
        }
        if (isset($data['link']) && $data['link']) {
            $result['details'][] = [
                'type' => 'url',
                'data' => $data['link'],
            ];
        }

        return $result;
    }
}
