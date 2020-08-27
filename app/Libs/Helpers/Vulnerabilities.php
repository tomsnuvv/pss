<?php

namespace App\Libs\Helpers;

use App\Models\Product;
use App\Models\Vulnerability;
use App\Models\VulnerabilityType;
use App\Models\VulnerabilityAffectance;
use App\Models\VulnerabilityDetail;

/**
 * Vulnerabilities Helper class.
 *
 * @todo singelton to cache queries, for example in getVulnerabilityType method.
 */
class Vulnerabilities
{
    /**
     * Create (or update) a vulnerability.
     *
     * @param  array  $data  Data entry
     * @return \App\Models\Vulnerability
     */
    public static function createVulnerability($data)
    {
        $vulnerability = null;

        // Check by CVE
        $cve = self::getCVE($data);
        if ($cve) {
            $vulnerability = Vulnerability::whereHas('details', function ($query) use ($cve) {
                $query->where('type', 'cve')->where('data', $cve);
            })->first();

            // Update the title only if is larger (NIST titles are only the CVE)
            if ($vulnerability && $data['title'] <= $vulnerability->title) {
                unset($data['title']);
            }
        }

        // Check by Title and ProductId
        if (!$vulnerability) {
            $productId = self::getProductId($data);
            if ($productId) {
                $vulnerability = Vulnerability::where('title', $data['title'])
                ->whereHas('affectances', function ($query) use ($productId) {
                    $query->where('product_id', $productId);
                })->first();
            }
        }

        // Check (or create) by Title
        if (!$vulnerability) {
            $vulnerability = Vulnerability::firstOrNew([
                'title' => $data['title'],
            ]);
        }

        $vulnerability->fill($data);
        $vulnerability->save();

        if (!$vulnerability->type) {
            $vulnerability->type()->associate(self::getVulnerabilityType(
                $vulnerability,
                isset($data['type']) ? $data['type'] : null
            ));
        }
        if (!$vulnerability->severity && $vulnerability->type && $vulnerability->type->severity) {
            $vulnerability->severity()->associate($vulnerability->type->severity);
        }
        $vulnerability->save();

        self::insertAffectances($vulnerability, $data);
        self::insertDetails($vulnerability, $data);

        return $vulnerability;
    }

    /**
     * Extract the CVE (if any) from the vulnerability data.
     *
     * If contains multiple CVEs, will return void.
     *
     * @param  array $data
     * @return string
     */
    public static function getCVE($data)
    {
        if (!isset($data['details']) || empty($data['details'])) {
            return;
        }
        $cve = null;
        foreach ($data['details'] as $detail) {
            if ($detail['type'] == 'cve') {
                if ($cve == null) {
                    $cve = $detail['data'];
                } else {
                    return;
                }
            }
        }

        return $cve;
    }

    /**
     * Extract the Product Id (if any) from the vulnerability data.
     *
     * If there are multiple Products, return the first one.
     *
     * @param  array $data
     * @return int
     */
    public static function getProductId($data)
    {
        if (!isset($data['affectances']) || !is_array($data['affectances'])) {
            return;
        }
        if (!isset($data['affectances'][0]['product_id'])) {
            return;
        }
        return $data['affectances'][0]['product_id'];
    }

    /**
     * Insert Vulnerability affectances.
     *
     * Users bulk insert ignore to speed up imports.
     *
     * @param  \App\Models\Vulnerability  $vulnerability
     * @param  array|null  $data
     */
    public static function insertAffectances(Vulnerability $vulnerability, $data = null)
    {
        if (!isset($data['affectances']) || empty($data['affectances'])) {
            return;
        }

        // Add the missing fields
        foreach ($data['affectances'] as $i => $affectance) {
            $data['affectances'][$i]['vulnerability_id'] = $vulnerability->id;
            if (!isset($data['affectances'][$i]['min'])) {
                $data['affectances'][$i]['min'] = null;
            }
            if (!isset($data['affectances'][$i]['fixed_in'])) {
                $data['affectances'][$i]['fixed_in'] = null;
            }
        }

        VulnerabilityAffectance::insertOrIgnore($data['affectances']);
    }

    /**
     * Insert the Vulnerability details.
     *
     * Users bulk insert ignore to speed up imports.
     *
     * @param  \App\Models\Vulnerability  $vulnerability
     * @param  array|null  $data
     */
    public static function insertDetails(Vulnerability $vulnerability, $data = null)
    {
        if (!isset($data['details']) || empty($data['details'])) {
            return;
        }

        // Add the missing fields
        foreach ($data['details'] as $i => $affectance) {
            $data['details'][$i]['vulnerability_id'] = $vulnerability->id;
        }

        VulnerabilityDetail::insertOrIgnore($data['details']);
    }

    /**
     * Get the Vulnerability Type from a Vulnerability.
     *
     * @param  \App\Models\Vulnerability  $vulnerability
     * @param  string  $hint  Provided hint (some vulnerabilities providers have that)
     * @return \App\Models\VulnerabilityType
     * @todo Needs to be improved. Is not able to identify:
     *      %Code Execution%
     *      %PHP Object Injection%
     *      %Arbitrary File Download%
     *      %Path Traversal%
     *      %Cross-Site Scripting%
     *
     */
    public static function getVulnerabilityType(Vulnerability $vulnerability, $hint = null)
    {
        foreach (VulnerabilityType::all() as $type) {
            if (self::findStringInVulnerability($vulnerability, $type->code) ||
                self::findStringInVulnerability($vulnerability, $type->name) ||
                stristr($hint, $type->code)) {
                return $type;
            }
        }

        return VulnerabilityType::unknown()->first();
    }

    /**
     * Checks if a vulnerability contains a string.
     *
     * @param  \App\Models\Vulnerability  $vulnerability
     * @param  string  $string
     * @return bool
     * @todo Regex!
     *
     */
    private static function findStringInVulnerability(Vulnerability $vulnerability, $string)
    {
        $string = strtolower($string);

        $fields = ['title', 'description'];
        foreach ($fields as $field) {
            $field = strtolower($vulnerability->$field);
            if (!$field) {
                continue;
            }
            if (
                // Starts with
                substr($field, 0, strlen($string.' ')) === $string.' ' ||
                // Ends with
                substr($field, -strlen(' '.$string)) === ' '.$string ||
                // Contains
                stristr($field, ' '.$string.' ') || stristr($field, ' '.$string.'.') ||
                // Equals
                $field == $string
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a Severity ID by a CVSS value.
     *
     * https://nvd.nist.gov/vuln-metrics/cvss
     *
     * @param  int  $cvss
     * @return int Severity Id
     */
    public static function getSeverityIdByCVSS($cvss, $version = 3)
    {
        if ($version == 3) {
            if ($cvss == 0) {
                return 1;
            } elseif ($cvss <= 4) {
                return 2;
            } elseif ($cvss < 7) {
                return 3;
            } elseif ($cvss < 9) {
                return 4;
            } else {
                return 5;
            }
        } elseif ($version == 2) {
            if ($cvss <= 4) {
                return 2;
            } elseif ($cvss < 7) {
                return 3;
            } else {
                return 4;
            }
        }
    }
}
