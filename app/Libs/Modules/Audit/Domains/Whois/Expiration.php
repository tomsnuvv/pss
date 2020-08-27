<?php

namespace App\Libs\Modules\Audit\Domains\Whois;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Models\VulnerabilityType;

/**
 * Domains Whois Expiration Audit Module.
 *
 * Checks a domain will exprie soon or is already expired.
 */
class Expiration extends Audit
{
    /**
     * Days left for the "Expires Soon" finding.
     *
     * @var int
     */
    const EXPIRES_SOON_DAYS = 30;

    /**
     * Vulnerability Type Code for "Expired" Finding.
     *
     * @var string
     */
    const TYPE_EXPIRED = 'DOMAIN_EXPIRED';

    /**
     * Vulnerability Type Code for "Expires Soon" Finding.
     *
     * @var string
     */
    const TYPE_EXPIRES_SOON_EXPIRE = 'DOMAIN_EXPIRES_SOON';

    /*
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!$this->model->whois()->exists()) {
            $this->setMessage('Whois not found');
            return false;
        }

        if (!$this->model->whois->expiration_date) {
            $this->setMessage('Unknown Whois expiration date');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->model->isValid() === false) {
            $this->storeFinding(null, null, null, VulnerabilityType::whereCode(self::TYPE_EXPIRED)->first());
        } elseif ($this->model->isValid(self::EXPIRES_SOON_DAYS) === false) {
            $this->storeFinding(null, null, null, VulnerabilityType::whereCode(self::TYPE_EXPIRES_SOON_EXPIRE)->first());
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDetails()
    {
        return 'Expiration Date: ' . $this->model->whois->expiration_date;
    }
}
