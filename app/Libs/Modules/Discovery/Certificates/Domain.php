<?php

namespace App\Libs\Modules\Discovery\Certificates;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Helpers\Domains;
use App\Models\Certificate;
use App\Models\Domain as DomainModel;

/**
 * Domain Certificate Discovery Module.
 *
 * Obtains a Domain from a Certificate.
 * If a subdomain didn't exist and the parent domain is key,
 * the new subdomain will also be key.
 */
class Domain extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $domainName = str_replace('*.', '', $this->model->subject_common_name);
        if (!Domains::isValid($domainName)) {
            return;
        }
        if (DomainModel::where('name', $domainName)->exists()) {
            return;
        }
        $domain = Domains::createDomain($domainName);
        if (!$domain) {
            return;
        }
        if ($domain->parent && $domain->parent->key) {
            $domain->key = 1;
            $domain->save();
        }
        
        $this->items[] = $domain;
        $this->outputDetail('Domain', $domain->name);
    }
}
