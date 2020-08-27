<?php

namespace App\Libs\Helpers;

use App\Models\Host;
use App\Models\Website;
use App\Models\HostType;
use App\Models\Module;
use App\Models\Domain;

/**
 * Hosts Helper class.
 */
class Hosts
{
    /**
     * Create (if new) a Server Host from an IP.
     *
     * @param  string $ip
     * @return \App\Models\Host|void
     */
    public static function createServerFromIP($ip)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return;
        }
        $host = Host::firstOrNew(['ip' => $ip]);
        $host->type()->associate(HostType::server()->first());
        $name = self::getHostname($ip);
        if ($name != $ip) {
            $host->name = self::getHostname($ip);
        }
        $host->save();

        return $host;
    }

    /**
     * Create (if new) a Server Host from a Domain.
     *
     * @param  \App\Models\Domain $domain
     * @return \App\Models\Host|void
     */
    public static function createServerFromDomain(Domain $domain)
    {
        $ip = self::getIp($domain->name);
        if (!$ip) {
            return;
        }
        $host = self::createServerFromIP($ip);
        if (!$host) {
            return;
        }
        $domain->host()->associate($host);
        $domain->save();

        // Associate the domain related websites
        if ($domain->websites) {
            foreach ($domain->websites as $website) {
                Websites::attachHost($website, $host);
            }
        }

        return $host;
    }

    /**
     * Create (if new) a NameServer Host from a domain.
     *
     * @param  string $domainName
     * @return \App\Models\Host|void
     */
    public static function createNameServer($domainName)
    {
        $ip = self::getIp($domainName);
        if (!$ip) {
            return;
        }
        $host = self::createServerFromIP($ip);
        if (!$host) {
            return;
        }
        $host->type()->associate(HostType::nameServer()->first());
        $host->save();

        return $host;
    }

    /**
     * Create (if new) a Port and, if the product is
     * identified, an Installation models, related to a Host.
     *
     * @param  \App\Models\Host     $host
     * @param  array                $data
     * @param  \App\Models\Module   $module
     * @return \App\Models\Port
     */
    public static function addPort(Host $host, $data, Module $module)
    {
        $port = $host->ports()->firstOrNew(['port' => $data['port']]);
        $port->fill($data);
        $port->save();

        if (isset($data['product_id']) && $data['product_id'] !== null) {
            $installation = $host->installations()->updateOrCreate([], [
                'child_source_type' => get_class($port),
                'child_source_id' => $port->id,
                'product_id' => $data['product_id'],
                'version' => $data['version'],
                'module_id' => $module->id,
            ]);
            $installation->module()->associate($module);
            $installation->save();
        }

        return $port;
    }

    /**
     * Get the host's hostname from an IP.
     *
     * @param  string $ip
     * @return string|void
     */
    public static function getHostname($ip = null)
    {
        if (self::isValidIP($ip)) {
            return gethostbyaddr($ip);
        }
    }

    /**
     * Get the host's IP from a domain / hostname.
     *
     * @param  string $hostname
     * @return string|void
     */
    public static function getIp($hostname)
    {
        $ip = gethostbyname($hostname);
        if (self::isValidIP($ip)) {
            return $ip;
        }
    }

    /**
     * Checks if an IP is valid.
     *
     * @param  string $ip
     * @return bool
     */
    public static function isValidIP($ip)
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }
}
