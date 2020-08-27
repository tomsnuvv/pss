<?php

namespace App\Libs\Modules\Import\Vulnerabilities\Javascript\Nodejs;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Providers\Vulnerabilities\Javascript\Nodejs\SecurityWG as Provider;
use App\Libs\Helpers\Vulnerabilities;
use App\Libs\Helpers\Products;
use App\Libs\Helpers\Versions;

/**
 * SecurityWG Nodejs Javascript Vulnerabilities Import Module.
 *
 * Imports all available vulnerabilities from Nodejs/SecurityWG vulnerability database.
 */
class SecurityWG extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $provider = new Provider();
        $items = $provider->getVulnerabilities();

        foreach ($items as $package => $vulns) {
            $product = Products::createJavascriptProduct($package);
            $this->outputDetail('Product', $product->name);
            foreach ($vulns as $vuln) {
                $data = $this->parseVulnerabilityData($product->id, $vuln);
                $vulnerability = Vulnerabilities::createVulnerability($data);
                $this->outputDetail('Vulnerability', $vulnerability->title);
            }
        }
    }

    /**
     * Parse the vulnerability data, to be used by the Vulnerabilities helper.
     *
     * @param  int $productId
     * @param  array $data
     * @return array
     */
    public function parseVulnerabilityData($productId, $data)
    {
        $result['date'] = $data->publish_date;
        $result['title'] = $data->title;
        $result['description'] = $data->overview;

        $result['affectances'] = Versions::parseVersionsFromString($data->vulnerable_versions);
        foreach($result['affectances'] as $i => $affectance){
            $result['affectances'][$i]['product_id'] = $productId;
        }

        // Details: CVE
        if (isset($data->cves)) {
            foreach ($data->cves as $cve) {
                $result['details'][] =[
                    'type' => 'cve',
                    'data' => $cve,
                ];
            }
        }
        // Details: CVSS Score
        if (isset($data->cvss_score) && $data->cvss_score) {
            $result['severity_id'] = Vulnerabilities::getSeverityIdByCVSS($data->cvss_score, 3);
            $result['details'][] =[
                'type' => 'cvss_v3',
                'data' => $data->cvss_score,
            ];
        }
        // Details: CVSS Vector
        if (isset($data->cvss_vector) && $data->cvss_vector) {
            $result['details'][] =[
                'type' => 'cvss_v3_vector',
                'data' => $data->cvss_vector,
            ];
        }

        // Details
        if (isset($data->references)) {
            foreach ($data->references as $reference) {
                $result['details'][] =[
                    'type' => 'url',
                    'data' => $reference,
                ];
            }
        }

        return $result;
    }
}
