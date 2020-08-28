<?php

namespace App\Libs\Modules\Import\Vulnerabilities\NIST;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Providers\Vulnerabilities\NIST\NVD as Provider;
use App\Libs\Helpers\Vulnerabilities;
use App\Libs\Helpers\Products;
use App\Libs\Helpers\Versions;
use App\Models\Vulnerability;

/**
 * National Vulnerability Database Vulnerabilities Import Module.
 *
 * Imports all available vulnerabilities from NIST NVD.
 * https://nvd.nist.gov/
 * https://nvd.nist.gov/vuln/data-feeds#JSON_FEED
 */
class NVD extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function run()
    {
        $provider = new Provider();

        if (env('IMPORT_NIST_FULL')) {
            for ($year = 2002; $year <= date('Y'); $year++) {
                $this->output('<info>Downloading ' . $year . ' feed...</info>');

                $vulnerabilities = $provider->getVulnerabilities($year);
                $this->storeVulnerabilities($vulnerabilities);
                unset($vulnerabilities);
            }
        }

        $this->output('<info>Downloading recent feed...</info>');
        $vulnerabilities = $provider->getVulnerabilities('recent');
        $this->storeVulnerabilities($vulnerabilities);
    }

    /**
     * Store vulnerabilities.
     *
     * @todo Generate a better title.
     *
     * @param  array $vulnerabilities
     */
    private function storeVulnerabilities($vulnerabilities)
    {
        if (!is_array($vulnerabilities) || empty($vulnerabilities)) {
            return;
        }

        foreach ($vulnerabilities as $item) {
            if (!$this->isValid($item)) {
                continue;
            }
            $data = $this->parseVulnerabilityData($item);
            $vulnerability = Vulnerabilities::createVulnerability($data);
        }
    }

    /**
     * Check if the entry is valid.
     *
     * @param  object $item
     * @return boolean
     */
    private function isValid($item)
    {
        $description = $this->parseDescriptions($item->cve->description->description_data);
        if (strstr($description, 'DO NOT USE THIS CANDIDATE NUMBER')) {
            return false;
        }

        return true;
    }

    /**
     * Parse an obtained vulnerability data.
     *
     * @param  object $data
     * @return array|void
     */
    public function parseVulnerabilityData($data)
    {
        if (!isset($data->cve)) {
            return;
        }

        $result = [];

        // Details
        $result['details'] = $this->parseReferences($data->cve->references->reference_data);
        $cwes = $this->parseCWE($data->cve->problemtype->problemtype_data);
        foreach ($cwes as $cwe) {
            $result['details'][] = ['type' => 'cwe', 'data' => $cwe];
        }
        $result['details'][] = ['type' => 'cve', 'data' => $data->cve->CVE_data_meta->ID];
        $result['title'] = $data->cve->CVE_data_meta->ID;
        $this->outputDetail('Vulnerability', $result['title']);

        $result['affectances'] = $this->parseAffectance($data->cve->affects);
        $result['description'] = $this->parseDescriptions($data->cve->description->description_data);
        $result['date'] = date('Y-m-d H:i:s', strtotime($data->publishedDate));

        // CVSS
        if (isset($data->impact->baseMetricV2)) {
            $result['severity_id'] = Vulnerabilities::getSeverityIdByCVSS($data->impact->baseMetricV2->cvssV2->baseScore, 2);
            $result['details'][] = [
                'type' => 'cvss_v2',
                'data' => $data->impact->baseMetricV2->cvssV2->baseScore
            ];
            $result['details'][] = [
                'type' => 'cvss_v2_vector',
                'data' => $data->impact->baseMetricV2->cvssV2->vectorString
            ];
        }
        if (isset($data->impact->baseMetricV3)) {
            $result['severity_id'] = Vulnerabilities::getSeverityIdByCVSS($data->impact->baseMetricV3->cvssV3->baseScore, 3);
            $result['details'][] = [
                'type' => 'cvss_v3',
                'data' => $data->impact->baseMetricV3->cvssV3->baseScore
            ];
            $result['details'][] = [
                'type' => 'cvss_v3_vector',
                'data' => $data->impact->baseMetricV3->cvssV3->vectorString
            ];
        }

        return $result;
    }

    /**
     * Parse the description.
     *
     * @param  object $descriptions
     * @return string|void
     */
    private function parseDescriptions($descriptions)
    {
        foreach ($descriptions as $description) {
            if ($description->lang == 'en') {
                return $description->value;
            }
        }
    }

    /**
     * Parse the references.
     *
     * @param  object $references
     * @return array
     */
    private function parseReferences($references)
    {
        $result = [];
        foreach ($references as $reference) {
            if (isset($reference->url)) {
                $result[] = [
                    'type' => 'url',
                    'data' => $reference->url
                ];
            }
        }

        return $result;
    }

    /**
     * Parse the CWEs.
     *
     * @param  object $types
     * @return array
     */
    private function parseCWE($types)
    {
        $result = [];
        foreach ($types as $type) {
            foreach ($type->description as $description) {
                if (strstr($description->value, 'CWE')) {
                    $result[] = $description->value;
                }
            }
        }

        return $result;
    }

    /**
     * Parses the vulnerability affectance.
     *
     * @param object $affects
     * @return array
     */
    private function parseAffectance($affects)
    {
        $result = [];
        if (isset($affects->vendor->vendor_data)) {
            foreach ($affects->vendor->vendor_data as $vendorData) {
                $vendor = $vendorData->vendor_name;
                foreach ($vendorData->product->product_data as $productData) {
                    $product = Products::createCodeProduct(null, $vendorData->vendor_name . '/' . $productData->product_name);
                    $versions = $this->parseVersions($product->id, $productData->version->version_data);
                    $result = array_merge($result, $versions);
                    $this->outputDetail('Product', $product->name);
                    $this->outputDetail('Versions', count($versions));
                }
            }
        }

        return $result;
    }

    /**
     * Parse the affected versions.
     *
     * @param  int $productId
     * @param  object $versions
     * @return array
     */
    private function parseVersions($productId, $versions)
    {
        $result = [];
        foreach ($versions as $version) {
            if ($version->version_value == '-') {
                $version->version_value = null;
            }
            if ($version->version_affected == '=') {
                $result[] = [
                    'product_id' => $productId,
                    'min' => $version->version_value,
                    'fixed_in' => Versions::generateNextVersion($version->version_value),
                ];
            } elseif ($version->version_affected == '<=') {
                $result[] = [
                    'product_id' => $productId,
                    'fixed_in' => Versions::generateNextVersion($version->version_value),
                ];
            }
        }

        return $result;
    }
}
