<?php

namespace App\Libs\Helpers;

use App\Models\Domain;
use App\Libs\Helpers\Hosts;

/**
 * Domains Helper class.
 */
class Domains
{
    /**
     * Create (if new) a domain from a domain name.
     *
     * IPs are not allowed domains.
     *
     * @param  string $name
     * @param  bool   $validate Only creates the domain if it resolves
     * @return \App\Models\Domain|void
     */
    public static function createDomain($name, $validate = false)
    {
        $name = self::clearDomain($name);

        if (!self::isValid($name)) {
            return;
        }

        if (Hosts::isValidIP($name)) {
            return;
        }

        $domain = Domain::where('name', $name)->first();
        if ($domain) {
            return $domain;
        }

        if ($validate) {
            $ip = Hosts::getIp($name);
            if (!$ip) {
                return;
            }
        }

        return Domain::create(['name' => $name]);
    }

    /**
     * Get the top level domain.
     *
     * @param  string $name
     * @return string|void
     */
    public static function getTopLevelDomain($name)
    {
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $name, $matches)) {
            if (isset($matches['domain'])) {
                return $matches['domain'];
            }
        }
    }

    /**
     * Check if the domain is a subdomain.
     *
     * @param  string  $name
     * @return bool
     */
    public static function isSubdomain($name)
    {
        return self::getTopLevelDomain($name) != $name;
    }

    /**
     * Get the domain (or host) from the website URL.
     *
     * @param  string $url
     * @return string
     */
    public static function getDomainFromURL($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Clears a domain from string.
     *
     * @param  string $domain
     * @return string
     */
    public static function clearDomain($domain)
    {
        return preg_replace('/\s+/', '', strtolower($domain));
    }

    /**
     * Check if a domain is valid.
     *
     * @param  string $domain
     * @return bool
     */
    public static function isValid($domain)
    {
        return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain) //valid chars check
                && preg_match("/^.{1,253}$/", $domain) //overall length check
                && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain)); //length of each label
    }

    /**
     * Check if a domain has wildcard configuration.
     *
     * When a domain has a wildcard configuration,
     * subdomain enumeration will result a lot of false positives.
     *
     * https://en.wikipedia.org/wiki/Wildcard_DNS_record
     *
     * @param  string $domain
     * @return bool
     */
    public static function hasWildcardConfig($domain)
    {
        // It uses @ because of a PHP bug: https://bugs.php.net/bug.php?id=73149
        $result = @dns_get_record('test123securityscanner321test.' . $domain);

        return ! $result || ! empty($result);
    }
}
