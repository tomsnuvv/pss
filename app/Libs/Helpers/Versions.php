<?php

namespace App\Libs\Helpers;

/**
 * Versions Helper class.
 */
class Versions
{
    /**
     * Extracts the affected versions from a generic version syntax.
     *
     * From:
     * <= 2.4.2 || >= 3.0.0 <=3.0.1
     *
     * Will Return:
     * 1) [fixed_in] 2.4.2.x
     * 2) [min] 3.0.0 [fixed_in] 3.0.1.x
     *
     * @param string $input
     * @return array
     */
    public static function parseVersionsFromString($input)
    {
        $result = [];
        $ranges = explode(' || ', $input);
        foreach ($ranges as $range) {
            $range = self::removeVSigns($range);
            $range = self::removeSpacesSigns($range);
            $versions = explode(' ', $range);
            $item = [];
            foreach ($versions as $version) {
                if (self::isLess($version)) {
                    $item['fixed_in'] = self::parseVersion($version);
                } else {
                    $item['min'] = self::removeVersionSigns($version);
                }
            }
            $result[] = $item;
        }

        return $result;
    }

    /**
     * Extracts the affected versions from a generic version syntax.
     *
     * From:
     * [0] => 3.0.0 [1] < 3.0.1
     * Will return:
     * [min] 3.0.0 [fixed_in] 3.0.1.x
     *
     * @param array $versions
     * @return array
     */
    public static function parseVersions($versions)
    {
        $result = [];
        foreach ($versions as $item) {
            $item = self::removeVSigns($item);
            if (self::isLess($item)) {
                $result['fixed_in'] = self::parseVersion($item);
            } else {
                $result['min'] = self::removeVersionSigns($item);
            }
        }

        return $result;
    }

    /**
     * Parses the version to store the 'min affected' version and the 'fixed in' version.
     *
     * [1.7.0, 1.7.9]       1.7.0, 1.7.9
     * [>1.8.0, <=1.8.5]    1.8.0.x, 1.8.5.x
     * [>=1.9.0, <1.9.7]    1.9.0, 1.9.7
     *
     * @param  string $version
     * @return string
     */
    public static function parseVersion($version)
    {
        if (self::needsNextVersion($version)) {
            $version = self::generateNextVersion($version);
        }

        return self::removeVersionSigns($version);
    }

    /**
     * Checks if the version contains <= or > (but no >=)
     *
     * @param string $version
     * @return bool
     */
    public static function needsNextVersion($version)
    {
        return strstr($version, '<=') || (strstr($version, '>') && !strstr($version, '='));
    }

    /**
     * Checks if the version contains a less or equal less
     *
     * @param string $version
     * @return bool
     */
    public static function isLess($version)
    {
        return strstr($version, '<=') || strstr($version, '<');
    }

    /**
     * Remove the version comparator signs
     *
     * @param string $version
     * @return string
     */
    public static function removeVersionSigns($version)
    {
        return str_replace(['>', '<', '='], '', $version);
    }

    /**
     * Remove the spaces between the comparator signs and the version
     *
     * @param string $version
     * @return string
     */
    public static function removeSpacesSigns($version)
    {
        return str_replace(
            ['> ', '< ', '= ', '>= ', '<= ', '== '],
            ['>', '<', '=', '>=', '<=', '=='],
            $version
        );
    }

    /**
     * Remove the V signs.
     *
     * @param string $version
     * @return string
     */
    public static function removeVSigns($version)
    {
        return trim($version, 'vV');
    }

    /**
     * Generate the next version.
     *
     * @param string $version
     * @return string|null
     */
    public static function generateNextVersion($version)
    {
        return $version ? $version . '.x' : null;
    }
}
