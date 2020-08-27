<?php

namespace App\Libs\Helpers;

use App\Models\Domain;

/**
 * DNS Helper class.
 */
class DNS
{
    /**
     * Create (if new) a DNS record.
     *
     * @param  \App\Models\Domain $domain
     * @param  array $data
     * @return \App\Models\DNS
     */
    public static function createDNS(Domain $domain, $data)
    {
        $dns = $domain->dns()->firstOrNew([
            'type'   => $data['type'],
            'value' => $data['value']
        ]);

        $dns->fill($data);

        // Associate a Host
        if (filter_var($dns->value, FILTER_VALIDATE_IP)) {
            $targetHost = Hosts::createServerFromIP($dns->value);
            if ($targetHost) {
                /*if ($domain->key) {
                    $targetHost->key = 1;
                    $targetHost->save();
                }*/
                $dns->target()->associate($targetHost);
            }

            // Associate a Domain
        } elseif (Domains::isValid($dns->value)) {
            $targetDomain = Domains::createDomain($dns->value);
            if ($targetDomain) {
                /*if ($domain->key && $dns->type != 'SOA' && $dns->type != 'NS') {
                    $targetDomain->key = 1;
                    $targetDomain->save();
                }*/
                $dns->target()->associate($targetDomain);
            }
        }

        $dns->save();

        return $dns;
    }
}
